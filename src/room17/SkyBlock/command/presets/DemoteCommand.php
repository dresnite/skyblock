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

class DemoteCommand extends IslandCommand {
    
    /** @var SkyBlock */
    private $plugin;
    
    /**
     * DemoteCommand constructor.
     * @param IslandCommandMap $map
     */
    public function __construct(IslandCommandMap $map) {
        $this->plugin = $map->getPlugin();
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "demote";
    }

    /**
     * @return MessageContainer
     */
    public function getUsageMessageContainer(): MessageContainer {
        return new MessageContainer("DEMOTE_USAGE");
    }

    /**
     * @return MessageContainer
     */
    public function getDescriptionMessageContainer(): MessageContainer {
        return new MessageContainer("DEMOTE_DESCRIPTION");
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
            $session->sendTranslatedMessage(new MessageContainer("DEMOTE_USAGE"));
            return;
        }
        
        $offlineSession = $this->plugin->getSessionManager()->getOfflineSession($args[0]);
        if($this->checkClone($session, $offlineSession->getSession())) {
            return;
        } elseif($offlineSession->getIslandId() != $session->getIslandId()) {
            $session->sendTranslatedMessage(new MessageContainer("MUST_BE_PART_OF_YOUR_ISLAND", [
                "name" => $args[0]
            ]));
        } else {
            $rank = null;
            $rankName = "";
            switch($offlineSession->getRank()) {
                case Session::RANK_OFFICER:
                    $rank = Session::RANK_DEFAULT;
                    $rankName = "MEMBER";
                    break;
                case Session::RANK_LEADER:
                    $rank = Session::RANK_OFFICER;
                    $rankName = "OFFICER";
                    break;
                case Session::RANK_FOUNDER:
                    $rank = false;
            }
            if($rank == null) {
                $session->sendTranslatedMessage(new MessageContainer("CANNOT_DEMOTE_MEMBER", [
                    "name" => $args[0]
                ]));
                return;
            } elseif($rank == false) {
                $session->sendTranslatedMessage(new MessageContainer("CANNOT_DEMOTE_FOUNDER"));
                return;
            }
            $onlineSession = $offlineSession->getSession();
            if($onlineSession != null) {
                $onlineSession->setRank($rank);
                $onlineSession->sendTranslatedMessage(new MessageContainer("YOU_HAVE_BEEN_DEMOTED"));
                $onlineSession->save();
            } else {
                $offlineSession->setRank($rank);
                $offlineSession->save();
            }
            $session->sendTranslatedMessage(new MessageContainer("SUCCESSFULLY_DEMOTED_PLAYER", [
                "name" => $args[0],
                "to" => $session->getMessage(new MessageContainer("$rankName"))
            ]));
        }
        
    }
    
}