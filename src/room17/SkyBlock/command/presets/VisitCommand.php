<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace room17\SkyBlock\command\presets;


use pocketmine\permission\DefaultPermissions;
use room17\SkyBlock\command\IslandCommand;
use room17\SkyBlock\command\IslandCommandMap;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\SkyBlock;
use room17\SkyBlock\utils\message\MessageContainer;

class VisitCommand extends IslandCommand {

    private SkyBlock $plugin;

    public function __construct(IslandCommandMap $map) {
        $this->plugin = $map->getPlugin();
    }

    public function getName(): string {
        return "visit";
    }

    public function getAliases(): array {
        return ["teleport", "tp"];
    }

    public function getUsageMessageContainer(): MessageContainer {
        return new MessageContainer("VISIT_USAGE");
    }

    public function getDescriptionMessageContainer(): MessageContainer {
        return new MessageContainer("VISIT_DESCRIPTION");
    }

    public function onCommand(Session $session, array $args): void {
        if(!isset($args[0])) {
            $session->sendTranslatedMessage(new MessageContainer("VISIT_USAGE"));
            return;
        }
        $offline = $this->plugin->getSessionManager()->getOfflineSession($args[0]);
        $islandId = $offline->getIslandId();
        if($islandId == null) {
            $session->sendTranslatedMessage(new MessageContainer("HE_DO_NOT_HAVE_AN_ISLAND", [
                "name" => $args[0]
            ]));
            return;
        }
        $this->plugin->getProvider()->loadIsland($islandId);
        $island = $this->plugin->getIslandManager()->getIsland($islandId);
        if($island->isLocked() && !$session->getPlayer()->hasPermission(DefaultPermissions::ROOT_OPERATOR)) {
            $session->sendTranslatedMessage(new MessageContainer("HIS_ISLAND_IS_LOCKED", [
                "name" => $args[0]
            ]));
            $island->tryToClose();
            return;
        }
        $session->getPlayer()->teleport($island->getWorld()->getSpawnLocation());
        $session->sendTranslatedMessage(new MessageContainer("VISITING_ISLAND", [
            "name" => $args[0]
        ]));
    }

}