<?php

declare(strict_types=1);

namespace room17\SkyBlock\session;


use pocketmine\Player;
use ReflectionException;
use room17\SkyBlock\SkyBlock;

class SessionLocator {

    /**
     * @param Player $player
     * @return bool
     */
    public static function isSessionOpen(Player $player): bool {
        return SkyBlock::getInstance()->getSessionManager()->isSessionOpen($player);
    }

    /**
     * @param Player $player
     * @return Session
     * @throws ReflectionException
     */
    public static function getSession(Player $player): Session {
        return SkyBlock::getInstance()->getSessionManager()->getSession($player);
    }

    /**
     * @param string $username
     * @return OfflineSession
     */
    public static function getOfflineSession(string $username): OfflineSession {
        return SkyBlock::getInstance()->getSessionManager()->getOfflineSession($username);
    }

}