<?php
/**
 *  _____    ____    ____   __  __  __  ______
 * |  __ \  / __ \  / __ \ |  \/  |/_ ||____  |
 * | |__) || |  | || |  | || \  / | | |    / /
 * |  _  / | |  | || |  | || |\/| | | |   / /
 * | | \ \ | |__| || |__| || |  | | | |  / /
 * |_|  \_\ \____/  \____/ |_|  |_| |_| /_/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 */

declare(strict_types=1);

namespace room17\SkyBlock\island;


use pocketmine\level\Level;
use room17\SkyBlock\event\island\IslandCreateEvent;
use room17\SkyBlock\event\island\IslandDisbandEvent;
use room17\SkyBlock\event\island\IslandOpenEvent;
use room17\SkyBlock\event\island\IslandCloseEvent;
use room17\SkyBlock\generator\IslandGenerator;
use room17\SkyBlock\session\BaseSession;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\SkyBlock;
use room17\SkyBlock\utils\MessageContainer;

class IslandManager {
    
    /** @var SkyBlock */
    private $plugin;
    
    /** @var Island[] */
    private $islands = [];
    
    /**
     * IslandManager constructor.
     * @param SkyBlock $plugin
     */
    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
    }
    
    /**
     * @return SkyBlock
     */
    public function getPlugin(): SkyBlock {
        return $this->plugin;
    }
    
    /**
     * @return Island[]
     */
    public function getIslands(): array {
        return $this->islands;
    }
    
    /**
     * @param string $identifier
     * @return null|Island
     */
    public function getIsland(string $identifier): ?Island {
        return $this->islands[$identifier] ?? null;
    }

    /**
     * @param Session $session
     * @param string $type
     * @throws \ReflectionException
     */
    public function createIslandFor(Session $session, string $type): void {
        $identifier = SkyBlock::generateUniqueId();

        $generatorManager = $this->plugin->getGeneratorManager();
        if($generatorManager->isGenerator($type)) {
            $generator = $generatorManager->getGenerator($type);
        } else {
            $generator = $generatorManager->getGenerator("Basic");
        }
    
        $server = $this->plugin->getServer();
        $server->generateLevel($identifier, null, $generator);
        $server->loadLevel($identifier);
        $level = $server->getLevelByName($identifier);
        /** @var IslandGenerator $generator */
        $level->setSpawnLocation($generator::getWorldSpawn());
        
        $this->openIsland($identifier, [$session->getOffline()], true, $type, $level, 0);
        $session->setIsland($island = $this->islands[$identifier]);
        $session->setRank(BaseSession::RANK_FOUNDER);
        $session->save();
        $island->save();
        $session->setLastIslandCreationTime(microtime(true));
        (new IslandCreateEvent($island))->call();
    }

    /**
     * @param Island $island
     * @throws \ReflectionException
     */
    public function disbandIsland(Island $island): void {
        foreach($island->getLevel()->getPlayers() as $player) {
            $player->teleport($player->getServer()->getDefaultLevel()->getSpawnLocation());
        }
        foreach($island->getMembers() as $offlineMember) {
            $onlineSession = $offlineMember->getSession();
            if($onlineSession != null) {
                $onlineSession->setIsland(null);
                $onlineSession->setRank(Session::RANK_DEFAULT);
                $onlineSession->save();
                $onlineSession->sendTranslatedMessage(new MessageContainer("ISLAND_DISBANDED"));
            } else {
                $offlineMember->setIslandId(null);
                $offlineMember->setRank(Session::RANK_DEFAULT);
                $offlineMember->save();
            }
        }
        $island->setMembers([]);
        $island->save();
        $this->closeIsland($island);
        (new IslandDisbandEvent($island))->call();
    }

    /**
     * @param string $identifier
     * @param array $members
     * @param bool $locked
     * @param string $type
     * @param Level $level
     * @param int $blocksBuilt
     * @throws \ReflectionException
     */
    public function openIsland(string $identifier, array $members, bool $locked, string $type, Level $level, int $blocksBuilt): void {
        $this->islands[$identifier] = new Island($this, $identifier, $members, $locked, $type, $level, $blocksBuilt);
        (new IslandOpenEvent($this->islands[$identifier]))->call();
    }

    /**
     * @param Island $island
     * @throws \ReflectionException
     */
    public function closeIsland(Island $island): void {
        $island->save();
        $server = $this->plugin->getServer();
        (new IslandCloseEvent($island))->call();
        $server->unloadLevel($island->getLevel());
        unset($this->islands[$island->getIdentifier()]);
    }
    
}