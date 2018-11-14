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

namespace room17\SkyBlock\isle;


use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use room17\SkyBlock\session\OfflineSession;
use room17\SkyBlock\session\Session;

class Isle {
    
    /** @var IsleManager */
    private $manager;
    
    /** @var string */
    private $identifier;
    
    /** @var OfflineSession[] */
    private $members = [];
    
    /** @var bool */
    private $locked = false;
    
    /** @var string */
    private $type = self::TYPE_BASIC;
    
    const TYPE_BASIC = "basic.isle";
    const TYPE_OP = "op.isle";
    
    /** @var Level */
    private $level;
    
    /** @var int */
    private $blocksBuilt;
    
    /** @var string */
    private $category;
    
    const CATEGORY_EXTRA_SMALL = "XS";
    const CATEGORY_SMALL = "S";
    const CATEGORY_MEDIUM = "M";
    const CATEGORY_LARGE = "L";
    const CATEGORY_EXTRA_LARGE = "XL";
    
    /** @var Session[] */
    private $cooperators = [];
    
    /**
     * Isle constructor.
     * @param IsleManager $manager
     * @param string $identifier
     * @param array $members
     * @param bool $locked
     * @param string $type
     * @param Level $level
     * @param int $blocksBuilt
     */
    public function __construct(IsleManager $manager, string $identifier, array $members, bool $locked, string $type,
        Level $level, int $blocksBuilt) {
        $this->manager = $manager;
        $this->identifier = $identifier;
        $this->locked = $locked;
        $this->type = $type;
        $this->level = $level;
        $this->blocksBuilt = $blocksBuilt;
    
        foreach($members as $member) {
            if($member instanceof OfflineSession) {
                $this->addMember($member);
            }
        }
        
        $this->updateCategory();
    }
    
    /**
     * @return string
     */
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
     */
    public function getMembersOnline(): array {
        $sessions = [];
        foreach($this->members as $member) {
            $session = $member->getSession();
            if($session != null) {
                $sessions[] = $session;
            }
        }
        return $sessions;
    }
    
    /**
     * @return bool
     */
    public function isLocked(): bool {
        return $this->locked;
    }
    
    /**
     * @return string
     */
    public function getType(): string {
        return $this->type;
    }
    
    /**
     * @return Level
     */
    public function getLevel(): Level {
        return $this->level;
    }
    
    /**
     * @return Position
     */
    public function getSpawnLocation(): Position {
        return $this->level->getSpawnLocation();
    }
    
    /**
     * @return int
     */
    public function getBlocksBuilt(): int {
        return $this->blocksBuilt;
    }
    
    /**
     * @return string
     */
    public function getCategory(): string {
        return $this->category;
    }
    
    /**
     * @return string|null
     */
    public function getNextCategory(): ?string {
        switch($this->category) {
            case self::CATEGORY_EXTRA_LARGE:
                return null;
                break;
            case self::CATEGORY_LARGE:
                return self::CATEGORY_EXTRA_LARGE;
                break;
            case self::CATEGORY_MEDIUM:
                return self::CATEGORY_LARGE;
                break;
            case self::CATEGORY_SMALL:
                return self::CATEGORY_MEDIUM;
                break;
            default:
                return self::CATEGORY_SMALL;
        }
    }
    
    /**
     * @return int
     */
    public function getSlots(): int {
        return $this->manager->getPlugin()->getSettings()->getSlotsBySize($this->category);
    }
    
    /**
     * @return Session[]
     */
    public function getCooperators(): array {
        return $this->cooperators;
    }
    
    /**
     * @param Session $session
     * @return bool
     */
    public function isCooperator(Session $session): bool {
        return isset($this->cooperators[$session->getUsername()]);
    }
    
    /**
     * @param Session $session
     * @return bool
     */
    public function canInteract(Session $session): bool {
        return $session->getIsle() === $this or $this->isCooperator($session);
    }
    
    /**
     * @param bool $locked
     */
    public function setLocked(bool $locked = true): void {
        $this->locked = $locked;
    }
    
    /**
     * @param OfflineSession[] $members
     */
    public function setMembers(array $members) {
        $this->members = $members;
    }
    
    /**
     * @param Vector3 $position
     */
    public function setSpawnLocation(Vector3 $position) {
        $this->level->setSpawnLocation($position);
    }
    
    /**
     * @param int $blocksBuilt
     */
    public function setBlocksBuilt(int $blocksBuilt) {
        $this->blocksBuilt = max(0, $blocksBuilt);
        $this->updateCategory();
    }
    
    public function updateCategory(): void {
        if($this->blocksBuilt >= 500000) {
            $this->category = self::CATEGORY_EXTRA_LARGE;
        } elseif($this->blocksBuilt >= 100000) {
            $this->category = self::CATEGORY_LARGE;
        } elseif($this->blocksBuilt >= 50000) {
            $this->category = self::CATEGORY_MEDIUM;
        } elseif($this->blocksBuilt >= 10000) {
            $this->category = self::CATEGORY_SMALL;
        } else {
            $this->category = self::CATEGORY_EXTRA_SMALL;
        }
    }
    
    public function addBlock(): void {
        $this->setBlocksBuilt($this->blocksBuilt + 1);
    }
    
    public function destroyBlock(): void {
        $this->setBlocksBuilt($this->blocksBuilt - 1);
    }
    
    /**
     * @param OfflineSession $session
     */
    public function addMember(OfflineSession $session): void {
        $this->members[strtolower($session->getUsername())] = $session;
    }
    
    /**
     * @param Session[] $cooperators
     */
    public function setCooperators(array $cooperators): void {
        $this->cooperators = $cooperators;
    }
    
    /**
     * @param Session $session
     */
    public function addCooperator(Session $session): void {
        $this->cooperators[$session->getUsername()] = $session;
    }
    
    /**
     * @param Session $session
     */
    public function removeCooperator(Session $session): void {
        if(isset($this->cooperators[$username = $session->getUsername()])) {
            unset($this->cooperators[$username]);
        }
    }
    
    /**
     * @param string $message
     */
    public function broadcastMessage(string $message): void {
        foreach($this->getMembersOnline() as $session) {
            $session->getPlayer()->sendMessage($message);
        }
    }
    
    /**
     * @param string $identifier
     * @param array $args
     */
    public function broadcastTranslatedMessage(string $identifier, array $args = []): void {
        foreach($this->getMembersOnline() as $session) {
            $session->sendTranslatedMessage($identifier, $args);
        }
    }
    
    /**
     * @param string $message
     */
    public function broadcastPopup(string $message): void {
        foreach($this->getMembersOnline() as $session) {
            $session->getPlayer()->sendPopup($message);
        }
    }
    
    /**
     * @param string $identifier
     * @param array $args
     */
    public function broadcastTranslatedPopup(string $identifier, array $args = []): void {
        foreach($this->getMembersOnline() as $session) {
            $session->sendTranslatedPopup($identifier, $args);
        }
    }
    
    /**
     * @param string $message
     */
    public function broadcastTip(string $message): void {
        foreach($this->getMembersOnline() as $session) {
            $session->getPlayer()->sendTip($message);
        }
    }
    
    /**
     * @param string $identifier
     * @param array $args
     */
    public function broadcastTranslatedTip(string $identifier, array $args = []): void {
        foreach($this->getMembersOnline() as $session) {
            $session->sendTranslatedTip($identifier, $args);
        }
    }
    
    public function save(): void {
        $this->manager->getPlugin()->getProvider()->saveIsle($this);
    }
    
    public function updateMembers(): void {
        foreach($this->getMembersOnline() as $member) {
            if($member->getIsle() !== $this) {
                unset($this->members[$member->getUsername()]);
            }
        }
    }
    
    public function tryToClose(): void {
        $this->updateMembers();
        if(empty($this->getPlayersOnline()) and empty($this->getMembersOnline())) {
            $this->manager->closeIsle($this);
        }
    }
    
}