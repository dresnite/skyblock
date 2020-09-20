<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace room17\SkyBlock\session;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;

class SessionListener implements Listener {

    /** @var SessionManager */
    private $manager;

    public function __construct(SessionManager $manager) {
        $this->manager = $manager;
    }

    public function onLogin(PlayerLoginEvent $event): void {
        $this->manager->openSession($event->getPlayer());
    }

    public function onQuit(PlayerQuitEvent $event): void {
        $this->manager->closeSession($event->getPlayer());
    }

}