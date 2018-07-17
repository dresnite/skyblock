<?php
/*
 * Copyright (C) Andrés Arias - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

namespace SkyBlock\isle;


use pocketmine\level\Level;
use pocketmine\level\Position;
use SkyBlock\SkyBlock;

class IsleManager {
    
    /** @var SkyBlock */
    private $plugin;
    
    /** @var Isle[] */
    private $isles = [];
    
    /**
     * IsleManager constructor.
     * @param SkyBlock $plugin
     */
    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
    }
    
    /**
     * @return SkyBlock
     */
    public function getPlugin(): SkyBlock {
        return $this->plugin;
    }
    
    /**
     * @return Isle[]
     */
    public function getIsles(): array {
        return $this->isles;
    }
    
    /**
     * @param string $identifier
     * @return null|Isle
     */
    public function getIsle(string $identifier): ?Isle {
        return $this->isles[$identifier] ?? null;
    }
    
    /**
     * @param string $identifier
     * @param array $members
     * @param bool $locked
     * @param string $type
     * @param Level $level
     * @param Position $spawn
     */
    public function openIsle(string $identifier, array $members, bool $locked, string $type, Level $level, Position $spawn): void {
        $this->isles[$identifier] = new Isle($this, $identifier, $members, $locked, $type, $level, $spawn);
    }
    
    /**
     * @param Isle $isle
     */
    public function tryToCloseIsle(Isle $isle) {
        if(empty($isle->getMembersOnline())) {
            $this->plugin->getServer()->unloadLevel($isle->getLevel());
        }
    }
    
}