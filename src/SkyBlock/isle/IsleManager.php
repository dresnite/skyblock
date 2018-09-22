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

namespace SkyBlock\isle;


use pocketmine\level\Level;
use SkyBlock\session\iSession;
use SkyBlock\session\Session;
use SkyBlock\SkyBlock;

class IsleManager {
    
    /** @var SkyBlock */
    private $plugin;
    
    /** @var Isle[] */
    private $isles = [];
    
    /**
     * IsleManager constructor.
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
     * @return Isle[]
     */
    public function getIsles(): array {
        return $this->isles;
    }
    
    /**
     * @param string $identifier
     * @return null|Isle
     */
    public function getIsle(string $identifier): ?Isle {
        return $this->isles[$identifier] ?? null;
    }
    
    /**
     * @param Session $session
     * @param string $type
     */
    public function createIsleFor(Session $session, string $type): void {
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
        $level->setSpawnLocation($generator::getWorldSpawn());
        
        $this->openIsle($identifier, [$session->getOffline()], true, $type, $level);
        $session->setIsle($isle = $this->isles[$identifier]);
        $session->setRank(iSession::RANK_FOUNDER);
        $session->save();
        $isle->save();
    }
    
    /**
     * @param Isle $isle
     */
    public function disbandIsle(Isle $isle): void {
        foreach($isle->getLevel()->getPlayers() as $player) {
            $player->teleport($player->getServer()->getDefaultLevel()->getSpawnLocation());
        }
        foreach($isle->getMembers() as $offlineMember) {
            $onlineSession = $offlineMember->getSession();
            if($onlineSession != null) {
                $onlineSession->setIsle(null);
                $onlineSession->setRank(Session::RANK_DEFAULT);
                $onlineSession->save();
                $onlineSession->sendTranslatedMessage("ISLE_DISBANDED");
            } else {
                $offlineMember->setIsleId(null);
                $offlineMember->setRank(Session::RANK_DEFAULT);
                $offlineMember->save();
            }
        }
        $isle->setMembers([]);
        $isle->save();
    }
    
    /**
     * @param string $identifier
     * @param array $members
     * @param bool $locked
     * @param string $type
     * @param Level $level
     */
    public function openIsle(string $identifier, array $members, bool $locked, string $type, Level $level): void {
        $this->isles[$identifier] = new Isle($this, $identifier, $members, $locked, $type, $level);
    }
    
    /**
     * @param Isle $isle
     */
    public function closeIsle(Isle $isle): void {
        $isle->save();
        $this->plugin->getServer()->unloadLevel($isle->getLevel());
        unset($this->isles[$isle->getIdentifier()]);
    }
    
}