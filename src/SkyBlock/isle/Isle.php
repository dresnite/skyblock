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

namespace SkyBlock\isle;


use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use SkyBlock\session\OfflineSession;
use SkyBlock\session\Session;

class Isle {
    
    /** @var IsleManager */
    private $manager;
    
    /** @var string */
    private $identifier;
    
    /** @var OfflineSession[] */
    private $members = [];
    
    /** @var Session[] */
    private $visitors = [];
    
    /** @var bool */
    private $locked = false;
    
    /** @var string */
    private $type = self::TYPE_BASIC;
    
    const TYPE_BASIC = "basic.isle";
    const TYPE_OP = "op.isle";
    
    /** @var Level */
    private $level;
    
    /**
     * Isle constructor.
     * @param IsleManager $manager
     * @param string $identifier
     * @param array $members
     * @param bool $locked
     * @param string $type
     * @param Level $level
     */
    public function __construct(IsleManager $manager, string $identifier, array $members, bool $locked, string $type,
        Level $level) {
        $this->manager = $manager;
        $this->identifier = $identifier;
        $this->members = $members;
        $this->locked = $locked;
        $this->type = $type;
        $this->level = $level;
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
    public function getVisitors(): array {
        return $this->visitors;
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
    
    public function updateVisitors(): void {
        $this->visitors = [];
        foreach($this->level->getPlayers() as $player) {
            $session = $this->manager->getPlugin()->getSessionManager()->getSession($player);
            if(!in_array($session, $this->getMembersOnline())) {
                $this->visitors[] = $session;
            }
        }
    }
    
    public function tryToClose(): void {
        $this->updateMembers();
        $this->updateVisitors();
        if(empty($this->visitors) and empty($this->getMembersOnline())) {
            $this->manager->closeIsle($this);
        }
    }
    
}