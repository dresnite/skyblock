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
    private $players = [];
    
    /** @var bool */
    private $locked = false;
    
    /** @var string */
    private $type = self::TYPE_BASIC;
    
    const TYPE_BASIC = "basic.isle";
    const TYPE_OP = "op.isle";
    
    /** @var Level */
    private $level;
    
    /** @var Position */
    private $spawn;
    
    /**
     * Isle constructor.
     * @param IsleManager $manager
     * @param string $identifier
     * @param array $members
     * @param bool $locked
     * @param string $type
     * @param Level $level
     * @param Position $spawn
     */
    public function __construct(IsleManager $manager, string $identifier, array $members, bool $locked, string $type,
        Level $level, Position $spawn) {
        $this->manager = $manager;
        $this->identifier = $identifier;
        $this->members = $members;
        $this->locked = $locked;
        $this->type = $type;
        $this->level = $level;
        $this->spawn = $spawn;
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
    public function getPlayers(): array {
        return $this->players;
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
    public function getSpawn(): Position {
        return $this->spawn;
    }
    
    public function update(): void {
        foreach($this->getMembersOnline() as $member) {
            if($member->getIsle() !== $this) {
                unset($this->members[$member->getUsername()]);
            }
        }
        $this->manager->getPlugin()->getProvider()->saveIsle($this);
        $this->players = [];
        foreach($this->level->getPlayers() as $player) {
            $this->players[] = $this->manager->getPlugin()->getSessionManager()->getSession($player);
        }
        if(empty($this->players) and empty($this->getMembersOnline())) {
            $this->manager->closeIsle($this);
        }
    }
    
}