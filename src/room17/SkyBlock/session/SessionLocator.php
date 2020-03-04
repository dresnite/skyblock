<?php

declare(strict_types=1);

namespace room17\SkyBlock\session;


use pocketmine\Player;
use ReflectionException;
use room17\SkyBlock\SkyBlock;

class SessionLocator {

    /**
     * @throws ReflectionException
     */
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