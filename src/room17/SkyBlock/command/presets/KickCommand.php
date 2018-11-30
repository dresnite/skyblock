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

namespace room17\SkyBlock\command\presets;


use room17\SkyBlock\command\IsleCommand;
use room17\SkyBlock\command\IsleCommandMap;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\SkyBlock;

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
        } elseif(in_array($player, $session->getIsle()->getPlayersOnline())) {
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