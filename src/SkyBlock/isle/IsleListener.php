<?php
/*
 * Copyright (C) Andrés Arias - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

namespace SkyBlock\isle;


use pocketmine\event\Listener;

class IsleListener implements Listener {
    
    /** @var IsleManager */
    private $manager;
    
    /**
     * IsleListener constructor.
     * @param IsleManager $manager
     */
    public function __construct(IsleManager $manager) {
        $this->manager = $manager;
        $this->manager->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $this->manager->getPlugin());
    }
    
}