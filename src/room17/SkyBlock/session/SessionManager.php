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
use room17\SkyBlock\event\session\SessionCloseEvent;
use room17\SkyBlock\event\session\SessionCreationEvent;
use room17\SkyBlock\event\session\SessionOpenEvent;
use room17\SkyBlock\SkyBlock;

class SessionManager {

    private SkyBlock $plugin;

    /** @var Session[] */
    private array $sessions = [];

    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
        $plugin->getServer()->getPluginManager()->registerEvents(new SessionListener($this), $plugin);
    }

    public function getPlugin(): SkyBlock {
        return $this->plugin;
    }

    /**
     * @return Session[]
     */
    public function getSessions(): array {
        return $this->sessions;
    }

    public function getSession(Player $player): Session {
        if(!$this->isSessionOpen($player)) {
            $this->openSession($player);
        }
        return $this->sessions[$player->getName()];
    }

    public function isSessionOpen(Player $player): bool {
        return isset($this->sessions[$player->getName()]);
    }

    public function getOfflineSession(string $username): ?OfflineSession {
        return new OfflineSession($this, $username);
    }

    public function openSession(Player $player): void {
        $e = new SessionCreationEvent(Session::class);
        $e->call();

        $class = $e->getSessionClass();

        $this->sessions[$username = $player->getName()] = new $class($this, $player);

        (new SessionOpenEvent($this->sessions[$username]))->call();
    }

    public function closeSession(Player $player): void {
        if(isset($this->sessions[$username = $player->getName()])) {
            $session = $this->sessions[$username];
            $session->save();
            (new SessionCloseEvent($session))->call();
            unset($this->sessions[$username]);
            if($session->hasIsland()) {
                $session->getIsland()->tryToClose();
            }
        }
    }

}