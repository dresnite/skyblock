<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */


declare(strict_types=1);

namespace room17\SkyBlock\island;


use pocketmine\world\World;
use pocketmine\world\WorldCreationOptions;
use room17\SkyBlock\event\island\IslandCreateEvent;
use room17\SkyBlock\event\island\IslandDisbandEvent;
use room17\SkyBlock\island\generator\IslandGenerator;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\SkyBlock;
use room17\SkyBlock\utils\message\MessageContainer;

class IslandFactory {

    public static function createIslandWorld(string $identifier, string $type): World {
        $plugin = SkyBlock::getInstance();

        $generatorManager = $plugin->getGeneratorManager();
        if($generatorManager->isGenerator($type)) {
            $generator = $generatorManager->getGenerator($type);
        } else {
            $generator = $generatorManager->getGenerator("Basic");
        }

        $worldManager = $plugin->getServer()->getWorldManager();
        $worldManager->generateWorld($identifier, (new WorldCreationOptions())->setGeneratorClass($generator));
        $worldManager->loadWorld($identifier, true);
        $world = $worldManager->getWorldByName($identifier);
        /** @var IslandGenerator $generator */
        $world->setSpawnLocation($generator::getWorldSpawn());

        return $world;
    }

    public static function createIslandFor(Session $session, string $type): void {
        $identifier = uniqid("sb-");
        $islandManager = SkyBlock::getInstance()->getIslandManager();

        $islandManager->openIsland($identifier, [$session->getOfflineSession()], true, $type,
            self::createIslandWorld($identifier, $type), 0);

        $session->setIsland($island = $islandManager->getIsland($identifier));
        $session->setRank(RankIds::FOUNDER);
        $session->setLastIslandCreationTime(microtime(true));
        $session->getPlayer()->teleport($island->getSpawnLocation());

        $session->save();
        $island->save();

        (new IslandCreateEvent($island))->call();
    }

    public static function disbandIsland(Island $island): void {
        foreach($island->getWorld()->getPlayers() as $player) {
            $player->teleport($player->getServer()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
        }
        foreach($island->getMembers() as $offlineMember) {
            $onlineSession = $offlineMember->getOnlineSession();
            if($onlineSession != null) {
                $onlineSession->setIsland(null);
                $onlineSession->setRank();
                $onlineSession->save();
                $onlineSession->sendTranslatedMessage(new MessageContainer("ISLAND_DISBANDED"));
            } else {
                $offlineMember->setIslandId(null);
                $offlineMember->setRank();
                $offlineMember->save();
            }
        }
        $island->setMembers([]);
        $island->save();
        SkyBlock::getInstance()->getIslandManager()->closeIsland($island);
        (new IslandDisbandEvent($island))->call();
    }

}