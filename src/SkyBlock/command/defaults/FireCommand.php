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

class FireCommand extends IsleCommand {
    
    /** @var SkyBlock */
    private $plugin;
    
    /**
     * FireCommand constructor.
     * @param IsleCommandMap $map
     */
    public function __construct(IsleCommandMap $map) {
        $this->plugin = $map->getPlugin();
        parent::__construct(["fire"], "FIRE_USAGE", "FIRE_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkLeader($session)) {
            return;
        } elseif(!isset($args[0])) {
            $session->sendTranslatedMessage("FIRE_USAGE");
            return;
        }
        $offlineSession = $this->plugin->getSessionManager()->getOfflineSession($args[0]);
        if($this->checkClone($session, $offlineSession->getSession())) {
            return;
        } elseif($offlineSession->getIsleId() != $session->getIsleId()) {
            $session->sendTranslatedMessage("MUST_BE_PART_OF_YOUR_ISLE", [
                "name" => $args[0]
            ]);
        } elseif($offlineSession->getRank() == Session::RANK_FOUNDER) {
            $session->sendTranslatedMessage("CANNOT_FIRE_FOUNDER");
        } else {
            $onlineSession = $offlineSession->getSession();
            if($onlineSession != null) {
                if($onlineSession->getIsle()->getLevel() === $onlineSession->getPlayer()->getLevel()) {
                    $onlineSession->getPlayer()->teleport($this->plugin->getServer()->getDefaultLevel()->getSpawnLocation());
                }
                $onlineSession->setIsle(null);
                $onlineSession->setRank(Session::RANK_DEFAULT);
                $onlineSession->sendTranslatedMessage("YOU_HAVE_BEEN_FIRED");
                $onlineSession->save();
            } else {
                $offlineSession->setIsleId(null);
                $offlineSession->setRank(Session::RANK_DEFAULT);
                $offlineSession->save();
            }
            $session->sendTranslatedMessage("SUCCESSFULLY_FIRED", [
                "name" => $args[0]
            ]);
        }
    }
    
}