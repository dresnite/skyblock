<?php
/*
 * Copyright (C) Andrés Arias - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

namespace SkyBlock\isle;


use pocketmine\level\Level;
use pocketmine\level\Position;
use SkyBlock\session\OfflineSession;

class Isle {
    
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
    
    /** @var Position */
    private $spawn;
    
    /**
     * Isle constructor.
     * @param IsleManager $manager
     * @param string $identifier
     */
    public function __construct(IsleManager $manager, string $identifier) {
        $this->identifier = $identifier;
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
    
}