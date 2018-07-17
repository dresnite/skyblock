<?php
/*
 * Copyright (C) Andrés Arias - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
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
     * @param Player $player
     * @return null|Session
     */
    public function getSession(Player $player): ?Session {
        return $this->sessions[$player->getName()] ?? null;
    }
    
    /**
     * @param string $username
     * @return OfflineSession
     */
    public function getOfflineSession(string $username) {
        return new OfflineSession($this, $username);
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
            $this->sessions[$username]->save();
            unset($this->sessions[$username]);
        }
    }
    
}