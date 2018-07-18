<?php
/*
 * Copyright (C) Andrés Arias - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

namespace SkyBlock\session;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;

class SessionListener implements Listener {
    
    /** @var SessionManager */
    private $manager;
    
    /**
     * SessionListener constructor.
     * @param SessionManager $manager
     */
    public function __construct(SessionManager $manager) {
        $this->manager = $manager;
        $manager->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $manager->getPlugin());
    }
    
    /**
     * @param PlayerLoginEvent $event
     */
    public function onLogin(PlayerLoginEvent $event): void {
        $this->manager->openSession($event->getPlayer());
    }
    
    /**
     * @param PlayerQuitEvent $event
     */
    public function onQuit(PlayerQuitEvent $event): void {
        $this->manager->closeSession($event->getPlayer());
    }
    
}