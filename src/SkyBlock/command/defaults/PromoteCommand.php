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

namespace SkyBlock\command\defaults;


use SkyBlock\command\IsleCommand;
use SkyBlock\command\IsleCommandMap;
use SkyBlock\session\Session;
use SkyBlock\SkyBlock;

class PromoteCommand extends IsleCommand {
    
    /** @var SkyBlock */
    private $plugin;
    
    /**
     * PromoteCommand constructor.
     * @param IsleCommandMap $map
     */
    public function __construct(IsleCommandMap $map) {
        $this->plugin = $map->getPlugin();
        parent::__construct(["promote"], "PROMOTE_USAGE", "PROMOTE_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkLeader($session)) {
            return;
        } elseif(!isset($args[0])) {
            $session->sendTranslatedMessage("PROMOTE_USAGE");
            return;
        }
    
        $offlineSession = $this->plugin->getSessionManager()->getOfflineSession($args[0]);
        if($this->checkClone($session, $offlineSession->getSession())) {
            return;
        } elseif($offlineSession->getIsleId() != $session->getIsleId()) {
            $session->sendTranslatedMessage("MUST_BE_PART_OF_YOUR_ISLE", [
                "name" => $args[0]
            ]);
        } else {
            $rank = null;
            $rankName = "";
            switch($offlineSession->getRank()) {
                case Session::RANK_DEFAULT:
                    $rank = Session::RANK_OFFICER;
                    $rankName = "OFFICER";
                    break;
                case Session::RANK_OFFICER:
                    $rank = Session::RANK_LEADER;
                    $rankName = "LEADER";
                    break;
            }
            if($rank == null) {
                $session->sendTranslatedMessage("CANNOT_PROMOTE_LEADER", [
                    "name" => $args[0]
                ]);
                return;
            }
            $onlineSession = $offlineSession->getSession();
            if($onlineSession != null) {
                $onlineSession->setRank($rank);
                $onlineSession->sendTranslatedMessage("YOU_HAVE_BEEN_PROMOTED");
                $onlineSession->save();
            } else {
                $offlineSession->setRank($rank);
                $offlineSession->save();
            }
            $session->sendTranslatedMessage("SUCCESSFULLY_PROMOTED_PLAYER", [
                "name" => $args[0],
                "to" => $session->translate($rankName)
            ]);
        }
        
    }
    
}