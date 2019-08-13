<?php

declare(strict_types=1);

namespace room17\SkyBlock\island;


use room17\SkyBlock\event\island\IslandCreateEvent;
use room17\SkyBlock\event\island\IslandDisbandEvent;
use room17\SkyBlock\island\generator\IslandGenerator;
use room17\SkyBlock\session\BaseSession;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\SkyBlock;
use room17\SkyBlock\utils\MessageContainer;
use room17\SkyBlock\utils\Utils;

class IslandFactory {

    /**
     * @param Session $session
     * @param string $type
     * @throws \ReflectionException
     */
    public static function createIslandFor(Session $session, string $type): void {
        $identifier = Utils::generateUniqueId();
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

        $islandManager = $skyblock->getIslandManager();
        $islandManager->openIsland($identifier, [$session->getOffline()], true, $type, $level, 0);
        $session->setIsland($island = $islandManager->getIsland($identifier));
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