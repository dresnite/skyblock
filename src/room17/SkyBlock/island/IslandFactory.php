<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */


declare(strict_types=1);

namespace room17\SkyBlock\island;


use pocketmine\level\Level;
use ReflectionException;
use room17\SkyBlock\event\island\IslandCreateEvent;
use room17\SkyBlock\event\island\IslandDisbandEvent;
use room17\SkyBlock\island\generator\IslandGenerator;
use room17\SkyBlock\session\BaseSession;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\SkyBlock;
use room17\SkyBlock\utils\message\MessageContainer;

class IslandFactory {

    public static function createIslandWorld(string $identifier, string $type): Level {
        $skyblock = SkyBlock::getInstance();

        $generatorManager = $skyblock->getGeneratorManager();
        if($generatorManager->isGenerator($type)) {
            $generator = $generatorManager->getGenerator($type);
        } else {
            $generator = $generatorManager->getGenerator("Basic");
        }

        $server = $skyblock->getServer();
        $server->generateLevel($identifier, null, $generator);
        $server->loadLevel($identifier);
        $level = $server->getLevelByName($identifier);
        /** @var IslandGenerator $generator */
        $level->setSpawnLocation($generator::getWorldSpawn());

        return $level;
    }

    /**
     * @throws ReflectionException
     */
    public static function createIslandFor(Session $session, string $type): void {
        $identifier = uniqid("sb-");
        $islandManager = SkyBlock::getInstance()->getIslandManager();

        $islandManager->openIsland($identifier, [$session->getOfflineSession()], true, $type,
            self::createIslandWorld($identifier, $type), 0);

        $session->setIsland($island = $islandManager->getIsland($identifier));
        $session->setRank(BaseSession::RANK_FOUNDER);
        $session->setLastIslandCreationTime(microtime(true));
        $session->getPlayer()->teleport($island->getSpawnLocation());

        $session->save();
        $island->save();

        (new IslandCreateEvent($island))->call();
    }

    /**
     * @throws ReflectionException
     */
    public static function disbandIsland(Island $island): void {
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
        SkyBlock::getInstance()->getIslandManager()->closeIsland($island);
        (new IslandDisbandEvent($island))->call();
    }

}