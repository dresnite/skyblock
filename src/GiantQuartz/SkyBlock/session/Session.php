<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace GiantQuartz\SkyBlock\session;


use pocketmine\Player;
use ReflectionException;
use GiantQuartz\SkyBlock\island\Island;
use GiantQuartz\SkyBlock\utils\Invitation;
use GiantQuartz\SkyBlock\utils\message\MessageContainer;

class Session extends BaseSession {

    /** @var string */
    private $name;

    /** @var Player */
    private $player;

    /** @var null|Island */
    private $island = null;

    /** @var Invitation[] */
    private $invitations = [];

    public function __construct(SessionManager $manager, Player $player) {
        $this->player = $player;
        $this->name = $player->getName();
        parent::__construct($manager, $this->name);
    }

    public function getName(): string {
        return $this->name;
    }

    public function getPlayer(): Player {
        return $this->player;
    }

    public function getIsland(): ?Island {
        return $this->island;
    }

    /**
     * Returns the island the player is currently in or null if he's not in one
     */
    public function getIslandByLevel(): ?Island {
        return $this->manager->getPlugin()->getIslandManager()->getIsland($this->player->getLevel()->getName());
    }

    public function hasIsland(): bool {
        return $this->island != null;
    }

    public function getOfflineSession(): OfflineSession {
        return new OfflineSession($this->manager, $this->lowerCaseName);
    }

    /**
     * @return Invitation[]
     */
    public function getInvitations(): array {
        return $this->invitations;
    }

    public function hasInvitations(): bool {
        return !empty($this->invitations);
    }

    public function getInvitationFrom(string $senderName): ?Invitation {
        return $this->invitations[strtolower($senderName)] ?? null;
    }

    public function hasInvitationFrom(string $senderName): bool {
        return isset($this->invitations[strtolower($senderName)]);
    }

    public function getLastInvitation(): ?Invitation {
        /** @var Invitation|null $result */
        $result = null;
        foreach($this->invitations as $invitation) {
            if($result == null or $invitation->getCreationTime() > $result->getCreationTime()) {
                $result = $invitation;
            }
        }
        return $result;
    }

    public function getMessage(MessageContainer $container): string {
        return $this->manager->getPlugin()->getMessageManager()->getMessage($container);
    }

    public function sendInvitation(Invitation $invitation): void {
        $this->invitations[$invitation->getSender()->getLowerCaseName()] = $invitation;
    }

    public function removeInvitation(Invitation $invitation): void {
        $key = array_search($invitation, $this->invitations);
        if($key != false) {
            unset($this->invitations[$key]);
        }
    }

    public function clearInvitations(): void {
        foreach($this->invitations as $invitation) {
            $invitation->cancel();
        }
    }

    public function setIslandId(?string $identifier): void {
        parent::setIslandId($identifier);
        if($identifier != null) {
            $this->provider->loadIsland($identifier);
            $this->island = $this->manager->getPlugin()->getIslandManager()->getIsland($identifier);
        }
    }

    /**
     * @throws ReflectionException
     */
    public function setIsland(?Island $island): void {
        $lastIsland = $this->island;
        $this->island = $island;
        $this->islandId = ($island != null) ? $island->getIdentifier() : null;
        if($island != null) {
            $island->addMember($this->getOfflineSession());
        }
        if($lastIsland != null) {
            $lastIsland->updateMembers();
        }
        $this->save();
    }

    public function sendTranslatedMessage(MessageContainer $container): void {
        $this->player->sendMessage($this->getMessage($container));
    }

    public function sendTranslatedPopup(MessageContainer $container): void {
        $this->player->sendPopup($this->getMessage($container));
    }

    public function sendTranslatedTip(MessageContainer $container): void {
        $this->player->sendTip($this->getMessage($container));
    }

    public function teleportToSpawn(): void {
        $this->player->teleport($this->player->getServer()->getDefaultLevel()->getSafeSpawn());
    }

}