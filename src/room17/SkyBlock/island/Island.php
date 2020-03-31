<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */


declare(strict_types=1);

namespace room17\SkyBlock\island;


use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use ReflectionException;
use room17\SkyBlock\session\OfflineSession;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\utils\message\MessageContainer;

class Island {

    /** @var IslandManager */
    private $manager;

    /** @var string */
    private $identifier;

    /** @var OfflineSession[] */
    private $members = [];

    /** @var bool */
    private $locked;

    /** @var string */
    private $type;

    /** @var Level */
    private $level;

    /** @var int */
    private $blocksBuilt;

    /** @var string */
    private $category;

    /** @var Session[] */
    private $cooperators = [];

    /** @var bool */
    private $closed = false;

    public function __construct(IslandManager $manager, string $identifier, array $members, bool $locked, string $type,
                                Level $level, int $blocksBuilt) {
        $this->manager = $manager;
        $this->identifier = $identifier;
        $this->locked = $locked;
        $this->type = $type;
        $this->level = $level;
        $this->blocksBuilt = $blocksBuilt;

        foreach($members as $member) {
            $this->addMember($member);
        }

        $this->updateCategory();
    }

    public function getIdentifier(): string {
        return $this->identifier;
    }

    /**
     * @return OfflineSession[]
     */
    public function getMembers(): array {
        return $this->members;
    }

    /**
     * @return Session[]
     */
    public function getPlayersOnline(): array {
        return $this->level->getPlayers();
    }

    /**
     * @return Session[]
     * @throws ReflectionException
     */
    public function getSessionsOnline(): array {
        $sessions = [];
        foreach($this->members as $member) {
            $session = $member->getOnlineSession();
            if($session != null) {
                $sessions[] = $session;
            }
        }
        return $sessions;
    }

    /**
     * Returns the sessions of the players that are in the private island's chat
     *
     * @return Player[]
     * @throws ReflectionException
     */
    public function getChattingPlayers(): array {
        $players = [];
        foreach($this->getSessionsOnline() as $session) {
            if($session->isInChat()) {
                $players[] = $session->getPlayer();
            }
        }
        return $players;
    }

    public function isLocked(): bool {
        return $this->locked;
    }

    public function getType(): string {
        return $this->type;
    }

    public function getLevel(): Level {
        return $this->level;
    }

    public function getSpawnLocation(): Position {
        return $this->level->getSpawnLocation();
    }

    public function getBlocksBuilt(): int {
        return $this->blocksBuilt;
    }

    public function getCategory(): string {
        return $this->category;
    }

    public function getNextCategory(): ?string {
        switch($this->category) {
            case CategoryIds::EXTRA_LARGE:
                return null;
                break;
            case CategoryIds::LARGE:
                return CategoryIds::EXTRA_LARGE;
                break;
            case CategoryIds::MEDIUM:
                return CategoryIds::LARGE;
                break;
            case CategoryIds::SMALL:
                return CategoryIds::MEDIUM;
                break;
            default:
                return CategoryIds::SMALL;
        }
    }

    public function getSlots(): int {
        return $this->manager->getPlugin()->getSettings()->getSlotsByCategory($this->category);
    }

    /**
     * @return Session[]
     */
    public function getCooperators(): array {
        return $this->cooperators;
    }

    public function isCooperator(Session $session): bool {
        return isset($this->cooperators[$session->getLowerCaseName()]);
    }

    public function isClosed(): bool {
        return $this->closed;
    }

    public function canInteract(Session $session): bool {
        return $session->getIsland() === $this or $this->isCooperator($session) or $session->getPlayer()->hasPermission("skyblock.interaction");
    }

    public function setLocked(bool $locked = true): void {
        $this->locked = $locked;
    }

    /**
     * @param OfflineSession[] $members
     */
    public function setMembers(array $members): void {
        $this->members = $members;
    }

    public function setSpawnLocation(Vector3 $position): void {
        $this->level->setSpawnLocation($position);
    }

    public function setBlocksBuilt(int $blocksBuilt): void {
        $this->blocksBuilt = max(0, $blocksBuilt);
        $this->updateCategory();
    }

    public function updateCategory(): void {
        $this->category = $this->manager->getPlugin()->getSettings()->getCategoryByBlocks($this->blocksBuilt);
    }

    public function addBlock(): void {
        $this->setBlocksBuilt($this->blocksBuilt + 1);
    }

    public function destroyBlock(): void {
        $this->setBlocksBuilt($this->blocksBuilt - 1);
    }

    public function addMember(OfflineSession $session): void {
        $this->members[strtolower($session->getLowerCaseName())] = $session;
    }

    /**
     * @param Session[] $cooperators
     */
    public function setCooperators(array $cooperators): void {
        $this->cooperators = $cooperators;
    }

    public function addCooperator(Session $session): void {
        $this->cooperators[$session->getLowerCaseName()] = $session;
    }

    public function removeCooperator(Session $session): void {
        if(isset($this->cooperators[$username = $session->getLowerCaseName()])) {
            unset($this->cooperators[$username]);
        }
    }

    /**
     * @throws ReflectionException
     */
    public function broadcastMessage(string $message): void {
        foreach($this->getSessionsOnline() as $session) {
            $session->getPlayer()->sendMessage($message);
        }
    }

    /**
     * @throws ReflectionException
     */
    public function broadcastTranslatedMessage(MessageContainer $container): void {
        foreach($this->getSessionsOnline() as $session) {
            $session->sendTranslatedMessage($container);
        }
    }

    /**
     * @throws ReflectionException
     */
    public function broadcastPopup(string $message): void {
        foreach($this->getSessionsOnline() as $session) {
            $session->getPlayer()->sendPopup($message);
        }
    }

    /**
     * @throws ReflectionException
     */
    public function broadcastTranslatedPopup(MessageContainer $container): void {
        foreach($this->getSessionsOnline() as $session) {
            $session->sendTranslatedPopup($container);
        }
    }

    /**
     * @throws ReflectionException
     */
    public function broadcastTip(string $message): void {
        foreach($this->getSessionsOnline() as $session) {
            $session->getPlayer()->sendTip($message);
        }
    }

    /**
     * @throws ReflectionException
     */
    public function broadcastTranslatedTip(MessageContainer $container): void {
        foreach($this->getSessionsOnline() as $session) {
            $session->sendTranslatedTip($container);
        }
    }

    public function save(): void {
        $this->manager->getPlugin()->getProvider()->saveIsland($this);
    }

    /**
     * @throws ReflectionException
     */
    public function updateMembers(): void {
        foreach($this->getSessionsOnline() as $member) {
            if($member->getIsland() !== $this) {
                unset($this->members[$member->getLowerCaseName()]);
            }
        }
    }

    /**
     * @throws ReflectionException
     */
    public function tryToClose(): void {
        $this->updateMembers();
        if(!$this->closed and empty($this->getPlayersOnline()) and empty($this->getSessionsOnline())) {
            $this->closed = true;
            $this->manager->closeIsland($this);
        }
    }

}