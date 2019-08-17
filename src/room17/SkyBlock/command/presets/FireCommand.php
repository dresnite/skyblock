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

namespace room17\SkyBlock\command\presets;


use room17\SkyBlock\command\IslandCommand;
use room17\SkyBlock\command\IslandCommandMap;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\SkyBlock;
use room17\SkyBlock\utils\MessageContainer;

class FireCommand extends IslandCommand {

    /** @var SkyBlock */
    private $plugin;

    /**
     * FireCommand constructor.
     * @param IslandCommandMap $map
     */
    public function __construct(IslandCommandMap $map) {
        $this->plugin = $map->getPlugin();
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "fire";
    }

    /**
     * @return MessageContainer
     */
    public function getUsageMessageContainer(): MessageContainer {
        return new MessageContainer("FIRE_USAGE");
    }

    /**
     * @return MessageContainer
     */
    public function getDescriptionMessageContainer(): MessageContainer {
        return new MessageContainer("FIRE_DESCRIPTION");
    }

    /**
     * @param Session $session
     * @param array $args
     * @throws \ReflectionException
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkLeader($session)) {
            return;
        } elseif(!isset($args[0])) {
            $session->sendTranslatedMessage(new MessageContainer("FIRE_USAGE"));
            return;
        }
        $offlineSession = $this->plugin->getSessionManager()->getOfflineSession($args[0]);
        if($this->checkClone($session, $offlineSession->getSession())) {
            return;
        } elseif($offlineSession->getIslandId() != $session->getIslandId()) {
            $session->sendTranslatedMessage(new MessageContainer("MUST_BE_PART_OF_YOUR_ISLAND", [
                "name" => $args[0]
            ]));
        } elseif($offlineSession->getRank() == Session::RANK_FOUNDER) {
            $session->sendTranslatedMessage(new MessageContainer("CANNOT_FIRE_FOUNDER"));
        } else {
            $onlineSession = $offlineSession->getSession();
            if($onlineSession != null) {
                if($onlineSession->getIsland()->getLevel() === $onlineSession->getPlayer()->getLevel()) {
                    $onlineSession->getPlayer()->teleport($this->plugin->getServer()->getDefaultLevel()->getSpawnLocation());
                }
                $onlineSession->setRank(Session::RANK_DEFAULT);
                $onlineSession->setIsland(null);
                $onlineSession->sendTranslatedMessage(new MessageContainer("YOU_HAVE_BEEN_FIRED"));
                $onlineSession->save();
            } else {
                $offlineSession->setIslandId(null);
                $offlineSession->setRank(Session::RANK_DEFAULT);
                $offlineSession->save();
            }
            $session->sendTranslatedMessage(new MessageContainer("SUCCESSFULLY_FIRED", [
                "name" => $args[0]
            ]));
        }
    }

}