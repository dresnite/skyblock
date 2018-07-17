<?php
/*
 * Copyright (C) Andrés Arias - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

namespace SkyBlock\session;


use pocketmine\Player;
use SkyBlock\isle\Isle;
use SkyBlock\provider\Provider;

class Session {
    
    /** @var Provider */
    private $provider;
    
    /** @var Player */
    private $player;
    
    /** @var Isle|null */
    private $isle = null;
    
    /** @var bool */
    private $inChat = false;
    
    /** @var bool */
    private $founder = false;
    
    /**
     * Session constructor.
     * @param SessionManager $manager
     * @param Player $player
     */
    public function __construct(SessionManager $manager, Player $player) {
        $this->player = $player;
        $this->provider = $manager->getPlugin()->getProvider();
        $this->provider->openSession($this);
    }
    
    /**
     * @return Player
     */
    public function getPlayer(): Player {
        return $this->player;
    }
    
    /**
     * @return null|Isle
     */
    public function getIsle() {
        return $this->isle;
    }
    
    /**
     * @return bool
     */
    public function isInChat(): bool {
        return $this->inChat;
    }
    
    /**
     * @return bool
     */
    public function hasIsle(): bool {
        return $this->isle != null;
    }
    
    /**
     * @return bool
     */
    public function isFounder(): bool {
        return $this->founder;
    }
    
    /**
     * @param null|Isle $isle
     */
    public function setIsle(?Isle $isle): void {
        $this->isle = $isle;
    }
    
    /**
     * @param bool $inChat
     */
    public function setInChat(bool $inChat = true): void {
        $this->inChat = $inChat;
    }
    
    /**
     * @param bool $founder
     */
    public function setFounder(bool $founder = true): void {
        $this->founder = $founder;
    }
    
    /**
     * Saves session information to the database
     */
    public function save(): void {
        $this->provider->saveSession($this);
    }
    
}