<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace room17\SkyBlock\command\presets;


use room17\SkyBlock\session\SessionLocator;
use room17\SkyBlock\command\IslandCommand;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\SkyBlock;
use room17\SkyBlock\utils\message\MessageContainer;

class BanishCommand extends IslandCommand {

    /** @var SkyBlock */
    private $plugin;

    public function getName(): string {
        return "banish";
    }

    public function getUsageMessageContainer(): MessageContainer {
        return new MessageContainer("BANISH_USAGE");
    }

    public function getDescriptionMessageContainer(): MessageContainer {
        return new MessageContainer("BANISH_DESCRIPTION");
    }

    public function onCommand(Session $session, array $args): void {
        if($this->checkOfficer($session)) {
            return;
        } elseif(!isset($args[0])) {
            $session->sendTranslatedMessage(new MessageContainer("BANISH_USAGE"));
            return;
        }
        $server = $this->plugin->getServer();
        $player = $server->getPlayer($args[0]);
        if($player == null) {
            $session->sendTranslatedMessage(new MessageContainer("NOT_ONLINE_PLAYER", [
                "name" => $args[0]
            ]));
            return;
        }
        $playerSession = SessionLocator::getSession($player);
        if($this->checkClone($session, $playerSession)) {
            return;
        } elseif($playerSession->getIsland() === $session->getIsland()) {
            $session->sendTranslatedMessage(new MessageContainer("CANNOT_BANISH_A_MEMBER"));
        } elseif(in_array($player, $session->getIsland()->getPlayersOnline())) {
            $player->teleport($server->getDefaultLevel()->getSpawnLocation());
            $playerSession->sendTranslatedMessage(new MessageContainer("BANISHED_FROM_THE_ISLAND"));
            $session->sendTranslatedMessage(new MessageContainer("YOU_BANISHED_A_PLAYER", [
                "name" => $playerSession->getName()
            ]));
        } else {
            $session->sendTranslatedMessage(new MessageContainer("NOT_A_VISITOR", [
                "name" => $playerSession->getName()
            ]));
        }
    }

}