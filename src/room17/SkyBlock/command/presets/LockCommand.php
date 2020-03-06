<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace room17\SkyBlock\command\presets;


use room17\SkyBlock\command\IslandCommand;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\utils\message\MessageContainer;

class LockCommand extends IslandCommand {

    public function getName(): string {
        return "lock";
    }

    public function getUsageMessageContainer(): MessageContainer {
        return new MessageContainer("LOCK_USAGE");
    }

    public function getDescriptionMessageContainer(): MessageContainer {
        return new MessageContainer("LOCK_DESCRIPTION");
    }

    public function onCommand(Session $session, array $args): void {
        if($this->checkLeader($session)) {
            return;
        }
        $island = $session->getIsland();
        $island->setLocked(!$island->isLocked());
        $island->save();
        $session->sendTranslatedMessage(new MessageContainer($island->isLocked() ? "ISLAND_LOCKED" : "ISLAND_UNLOCKED"));
    }

}