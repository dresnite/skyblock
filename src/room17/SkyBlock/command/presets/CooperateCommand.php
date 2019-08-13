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

class CooperateCommand extends IslandCommand {
    
    /** @var SkyBlock */
    private $plugin;
    
    /**
     * CooperateCommand constructor.
     * @param IslandCommandMap $map
     */
    public function __construct(IslandCommandMap $map) {
        $this->plugin = $map->getPlugin();
        parent::__construct([
            "cooperate"
        ], new MessageContainer("COOPERATE_USAGE"), new MessageContainer("COOPERATE_DESCRIPTION"));
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkLeader($session)) {
            return;
        } elseif(!isset($args[0])) {
            $session->sendTranslatedMessage(new MessageContainer("COOPERATE_USAGE"));
            return;
        }
        $player = $this->plugin->getServer()->getPlayer($args[0]);
        if($player == null) {
            $session->sendTranslatedMessage(new MessageContainer("NOT_ONLINE_PLAYER", [
                "name" => $args[0]
            ]));
            return;
        }
        $playerSession = $this->plugin->getSessionManager()->getSession($player);
        $playerName = $playerSession->getPlayer()->getName();
        $sessionName = $session->getPlayer()->getName();
        $island = $session->getIsland();
        if($this->checkClone($session, $playerSession)) {
            return;
        } elseif($playerSession->getIsland() === $session->getIsland()) {
            $session->sendTranslatedMessage(new MessageContainer("ALREADY_ON_YOUR_ISLAND", [
                "name" => $playerName
            ]));
        } elseif($island->isCooperator($playerSession)) {
            $island->removeCooperator($playerSession);
            $session->sendTranslatedMessage(new MessageContainer("REMOVED_A_COOPERATOR", [
                "name" => $playerName
            ]));
            $playerSession->sendTranslatedMessage(new MessageContainer("NOW_YOU_CANNOT_COOPERATE", [
                "name" => $sessionName
            ]));
        } else {
            $island->addCooperator($playerSession);
            $session->sendTranslatedMessage(new MessageContainer("ADDED_A_COOPERATOR", [
                "name" => $playerName
            ]));
            $playerSession->sendTranslatedMessage(new MessageContainer("NOW_YOU_CAN_COOPERATE", [
                "name" => $sessionName
            ]));
        }
    }
    
}