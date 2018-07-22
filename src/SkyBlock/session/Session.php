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
        parent::__construct($manager, $player->getLowerCaseName());
        if($this->isleId != null) {
            $this->provider->checkIsle($this->isleId);
            $this->isle = $this->manager->getPlugin()->getIsleManager()->getIsle($this->isleId);
        }
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
     * @return OfflineSession
     */
    public function getOffline(): OfflineSession {
        return new OfflineSession($this->manager, $this->username);
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
    public function translate(string $identifier, array $args = []): string {
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