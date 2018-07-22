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
            "name" => $session->getPlayer()->getName()
        ]);
        $session->sendTranslatedMessage("SUCCESSFULLY_INVITED", [
            "name" => $player->getName()
        ]);
    }
    
}