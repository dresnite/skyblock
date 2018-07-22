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

namespace SkyBlock\session;


use pocketmine\Player;
use SkyBlock\SkyBlock;

class SessionManager {
    
    /** @var SkyBlock */
    private $plugin;
    
    /** @var Session[] */
    private $sessions = [];
    
    /** @var OfflineSession[] */
    private $offlineSessions = [];
    
    /**
     * SessionManager constructor.
     * @param SkyBlock $plugin
     */
    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
        $plugin->getServer()->getPluginManager()->registerEvents(new SessionListener($this), $plugin);
    }
    
    /**
     * @return SkyBlock
     */
    public function getPlugin(): SkyBlock {
        return $this->plugin;
    }
    
    /**
     * @return Session[]
     */
    public function getSessions(): array {
        return $this->sessions;
    }
    
    /**
     * @param string $username
     * @return null|OfflineSession
     */
    public function getOfflineSession(string $username): ?OfflineSession {
        return $this->offlineSessions[$username] ?? $this->offlineSessions[$username] = new OfflineSession($this, $username);
    }
    
    /**
     * @param Player $player
     * @return null|Session
     */
    public function getSession(Player $player): ?Session {
        return $this->sessions[$player->getName()] ?? null;
    }
    
    /**
     * @param Player $player
     */
    public function openSession(Player $player): void {
        $this->sessions[$player->getName()] = new Session($this, $player);
    }
    
    /**
     * @param Player $player
     */
    public function closeSession(Player $player): void {
        if(isset($this->sessions[$username = $player->getName()])) {
            $this->sessions[$username]->update();
            unset($this->sessions[$username]);
        }
    }
    
}