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

declare(strict_types=1);

namespace room17\SkyBlock\session;


use pocketmine\Player;
use room17\SkyBlock\isle\Isle;
use room17\SkyBlock\utils\MessageContainer;

class Session extends BaseSession {
    
    /** @var Player */
    private $player;
    
    /** @var null|Isle */
    private $isle = null;
    
    /** @var string|null */
    private $lastInvitation = null;
    
    /** @var array */
    private $invitations = [];
    
    /**
     * Session constructor.
     * @param SessionManager $manager
     * @param Player $player
     */
    public function __construct(SessionManager $manager, Player $player) {
        $this->player = $player;
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
     * @return OfflineSession
     */
    public function getOffline(): OfflineSession {
        return new OfflineSession($this->manager, $this->username);
    }
    
    /**
     * @return array
     */
    public function getInvitations(): array {
        return $this->invitations;
    }
    
    /**
     * @param string $senderName
     * @return null|Isle
     */
    public function getInvitation(string $senderName): ?Isle {
        return $this->invitations[$senderName] ?? null;
    }
    
    /**
     * @return null|string
     */
    public function getLastInvitation(): ?string {
        return $this->lastInvitation;
    }
    
    /**
     * @return bool
     */
    public function hasLastInvitation(): bool {
        return $this->lastInvitation != null;
    }
    
    /**
     * @param null|string $isle
     */
    public function setIsleId(?string $isle): void {
        parent::setIsleId($isle);
        if($isle != null) {
            $this->provider->loadIsle($isle);
            $this->isle = $this->manager->getPlugin()->getIsleManager()->getIsle($isle);
        }
    }
    
    /**
     * @param null|Isle $isle
     */
    public function setIsle(?Isle $isle): void {
        $lastIsle = $this->isle;
        $this->isle = $isle;
        $this->isleId = ($isle != null) ? $isle->getIdentifier() : null;
        if($isle != null) {
            $isle->addMember($this->getOffline());
        }
        if($lastIsle != null) {
            $lastIsle->updateMembers();
        }
        $this->save();
    }
    
    /**
     * @param array $invitations
     */
    public function setInvitations(array $invitations): void {
        $this->invitations = $invitations;
    }
    
    /**
     * @param string $senderName
     * @param Isle $isle
     */
    public function addInvitation(string $senderName, Isle $isle): void {
        $this->invitations[$senderName] = $isle;
        $this->lastInvitation = $senderName;
    }
    
    /**
     * @param string $senderName
     */
    public function removeInvitation(string $senderName): void {
        if(isset($this->invitations[$senderName])) {
            unset($this->invitations[$senderName]);
        }
    }
    
    /**
     * @param null|string $senderName
     */
    public function setLastInvitation(?string $senderName): void {
        $this->lastInvitation = $senderName;
    }

    /**
     * @param MessageContainer $container
     * @return string
     */
    public function getMessage(MessageContainer $container): string {
        return $this->manager->getPlugin()->getSettings()->getMessage($$container);
    }

    /**
     * @param MessageContainer $container
     */
    public function sendTranslatedMessage(MessageContainer $container): void {
        $this->player->sendMessage($this->getMessage($container));
    }

    /**
     * @param MessageContainer $container
     */
    public function sendTranslatedPopup(MessageContainer $container): void {
        $this->player->sendPopup($this->getMessage($container));
    }

    /**
     * @param MessageContainer $container
     */
    public function sendTranslatedTip(MessageContainer $container): void {
        $this->player->sendTip($this->getMessage($container));
    }
    
}