<?php
/*
 * Copyright (C) Andrés Arias - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

namespace SkyBlock\session;


use pocketmine\Player;
use SkyBlock\isle\Isle;

class Session extends iSession {
    
    /** @var Player */
    private $player;
    
    /** @var null|Isle */
    private $isle = null;
    
    /**
     * Session constructor.
     * @param SessionManager $manager
     * @param Player $player
     */
    public function __construct(SessionManager $manager, Player $player) {
        $this->player = $player;
        if($this->isleId != null) {
            $this->provider->checkIsle($this->isleId);
            $this->isle = $this->manager->getPlugin()->getIsleManager()->getIsle($this->isleId);
        }
        parent::__construct($manager, $player->getLowerCaseName());
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
    public function getIsle(): ?Isle {
        return $this->isle;
    }
    
    /**
     * @return bool
     */
    public function hasIsle(): bool {
        return $this->isle != null;
    }
    
    /**
     * @param null|Isle $isle
     */
    public function setIsle(?Isle $isle): void {
        $lastIsle = $this->isle;
        $this->isle = $isle;
        $this->isleId = ($isle != null) ? $isle->getIdentifier() : null;
        if($lastIsle != null) {
            $lastIsle->update();
        }
        $this->update();
    }
    
    public function update(): void {
        parent::update();
        if($this->hasIsle()) {
            $this->isle->update();
        }
    }
    
    /**
     * @param string $identifier
     * @param array $args
     * @return string
     */
    public function translate(string $identifier, array $args): string {
        return $this->manager->getPlugin()->getMessage($identifier, $args);
    }
    
    /**
     * @param string $identifier
     * @param array $args
     */
    public function sendTranslatedMessage(string $identifier, array $args = []): void {
        $this->player->sendMessage($this->translate($identifier, $args));
    }
    
    /**
     * @param string $identifier
     * @param array $args
     */
    public function sendTranslatedPopup(string $identifier, array $args = []): void {
        $this->player->sendPopup($this->translate($identifier, $args));
    }
    
    /**
     * @param string $identifier
     * @param array $args
     */
    public function sendTranslatedTip(string $identifier, array $args = []): void {
        $this->player->sendTip($this->translate($identifier, $args));
    }
    
}