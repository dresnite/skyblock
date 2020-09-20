<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace room17\SkyBlock\session;


use pocketmine\Player;
use room17\SkyBlock\SkyBlock;

class SessionLocator {

    public static function getSession(Player $player): Session {
        return SkyBlock::getInstance()->getSessionManager()->getSession($player);
    }

    public static function getOfflineSession(string $username): OfflineSession {
        return SkyBlock::getInstance()->getSessionManager()->getOfflineSession($username);
    }

    public static function isSessionOpen(Player $player): bool {
        return SkyBlock::getInstance()->getSessionManager()->isSessionOpen($player);
    }

}