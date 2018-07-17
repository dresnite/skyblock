<?php
/*
 * Copyright (C) Andrés Arias - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

namespace SkyBlock\session;


use SkyBlock\isle\Isle;
use SkyBlock\provider\Provider;

abstract class iSession {
    
    /** @var SessionManager */
    protected $manager;
    
    /** @var Provider */
    protected $provider;
    
    /** @var string */
    protected $username;
    
    /** @var Isle|null */
    protected $isle = null;
    
    /** @var bool */
    protected $inChat = false;
    
    /** @var int */
    protected $rank = false;
    
    const RANK_DEFAULT = 0;
    const RANK_FOUNDER = 1;
    
    /**
     * iSession constructor.
     * @param SessionManager $manager
     * @param string $username
     */
    public function __construct(SessionManager $manager, string $username) {
        $this->manager = $manager;
        $this->username = $username;
        $this->provider = $manager->getPlugin()->getProvider();
        $this->provider->openSession($this);
    }
    
    /**
     * @return mixed
     */
    public function getUsername() {
        return $this->username;
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
     * @return int
     */
    public function getRank(): int {
        return $this->rank;
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
     * @param int $rank
     */
    public function setRank(int $rank = self::RANK_DEFAULT): void {
        $this->rank = $rank;
    }
    
    /**
     * Saves session information to the database and checks if the isle needs to be unloaded
     */
    public function update(): void {
        $this->provider->saveSession($this);
        if($this->hasIsle()) {
            $this->manager->getPlugin()->getIsleManager()->tryToCloseIsle($this->isle);
        }
    }
    
}