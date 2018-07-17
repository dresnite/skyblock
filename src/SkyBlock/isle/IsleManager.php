<?php
/*
 * Copyright (C) Andrés Arias - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

namespace SkyBlock\isle;


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
    
}