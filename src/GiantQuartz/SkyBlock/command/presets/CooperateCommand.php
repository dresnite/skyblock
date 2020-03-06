<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace GiantQuartz\SkyBlock\command\presets;


use ReflectionException;
use GiantQuartz\SkyBlock\command\IslandCommand;
use GiantQuartz\SkyBlock\command\IslandCommandMap;
use GiantQuartz\SkyBlock\session\Session;
use GiantQuartz\SkyBlock\SkyBlock;
use GiantQuartz\SkyBlock\utils\message\MessageContainer;

class CooperateCommand extends IslandCommand {

    /** @var SkyBlock */
    private $plugin;

    public function __construct(IslandCommandMap $map) {
        $this->plugin = $map->getPlugin();
    }

    public function getName(): string {
        return "cooperate";
    }

    public function getUsageMessageContainer(): MessageContainer {
        return new MessageContainer("COOPERATE_USAGE");
    }

    public function getDescriptionMessageContainer(): MessageContainer {
        return new MessageContainer("COOPERATE_DESCRIPTION");
    }

    /**
     * @throws ReflectionException
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