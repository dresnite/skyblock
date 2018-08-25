<?php
/*
 * Copyright (C) PrimeGames - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
*/

namespace SkyBlock\command\defaults;


use SkyBlock\command\IsleCommand;
use SkyBlock\command\IsleCommandMap;
use SkyBlock\session\Session;
use SkyBlock\SkyBlock;

class KickCommand extends IsleCommand {
    
    /** @var SkyBlock */
    private $plugin;
    
    /**
     * KickCommand constructor.
     * @param IsleCommandMap $map
     */
    public function __construct(IsleCommandMap $map) {
        $this->plugin = $map->getPlugin();
        parent::__construct(["kick"], "KICK_USAGE", "KICK_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkOfficer($session)) {
            return;
        } elseif(!isset($args[0])) {
            $session->sendTranslatedMessage("KICK_USAGE");
            return;
        }
        $server = $this->plugin->getServer();
        $player = $server->getPlayer($args[0]);
        if($player == null) {
            $session->sendTranslatedMessage("NOT_ONLINE_PLAYER", [
                "name" => $args[0]
            ]);
            return;
        }
        $playerSession = $this->plugin->getSessionManager()->getSession($player);
        if($this->checkClone($session, $playerSession)) {
            return;
        } elseif($playerSession->getIsle() === $session->getIsle()) {
            $session->sendTranslatedMessage("CANNOT_KICK_A_MEMBER");
        } elseif(in_array($playerSession, $session->getIsle()->getVisitors())) {
            $player->teleport($server->getDefaultLevel()->getSpawnLocation());
            $playerSession->sendTranslatedMessage("KICKED_FROM_THE_ISLE");
            $session->sendTranslatedMessage("YOU_KICKED_A_PLAYER", [
                "name" => $playerSession->getUsername()
            ]);
        } else {
            $session->sendTranslatedMessage("NOT_A_VISITOR", [
                "name" => $playerSession->getUsername()
            ]);
        }
    }
    
}