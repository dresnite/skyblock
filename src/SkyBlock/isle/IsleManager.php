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
    public function closeIsle(Isle $isle): void {
        $this->plugin->getServer()->unloadLevel($isle->getLevel());
        unset($this->isles[$isle->getIdentifier()]);
    }
    
}