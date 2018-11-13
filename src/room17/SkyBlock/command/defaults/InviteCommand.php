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

namespace room17\SkyBlock\command\defaults;


use room17\SkyBlock\command\IsleCommand;
use room17\SkyBlock\command\IsleCommandMap;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\SkyBlock;

class InviteCommand extends IsleCommand {
    
    /** @var SkyBlock */
    private $plugin;
    
    /**
     * InviteCommand constructor.
     * @param IsleCommandMap $map
     */
    public function __construct(IsleCommandMap $map) {
        $this->plugin = $map->getPlugin();
        parent::__construct(["invite", "inv"], "INVITE_USAGE", "INVITE_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkOfficer($session)) {
            return;
        } elseif(!isset($args[0])) {
            $session->sendTranslatedMessage("INVITE_USAGE");
            return;
        } elseif(count($session->getIsle()->getMembers()) >= $session->getIsle()->getSlots()) {
            $isle = $session->getIsle();
            $next = $isle->getNextCategory();
            if($next != null) {
                $session->sendTranslatedMessage("ISLE_IS_FULL_BUT_YOU_CAN_UPGRADE", [
                    "next" => $next
                ]);
            } else {
                $session->sendTranslatedMessage("ISLE_IS_FULL");
            }
            return;
        }
        $player = $this->plugin->getServer()->getPlayer($args[0]);
        if($player == null) {
            $session->sendTranslatedMessage("NOT_ONLINE_PLAYER", [
                "name" => $args[0]
            ]);
            return;
        }
        $playerSession = $this->plugin->getSessionManager()->getSession($player);
        if($this->checkClone($session, $playerSession)) {
            return;
        } elseif($playerSession->hasIsle()) {
            $session->sendTranslatedMessage("CANNOT_INVITE_BECAUSE_HAS_ISLE", [
                "name" => $player->getName()
            ]);
            return;
        }
        $playerSession->addInvitation($session->getUsername(), $session->getIsle());
        $playerSession->sendTranslatedMessage("YOU_WERE_INVITED_TO_AN_ISLE", [
            "name" => $session->getUsername()
        ]);
        $session->sendTranslatedMessage("SUCCESSFULLY_INVITED", [
            "name" => $player->getName()
        ]);
    }
    
}