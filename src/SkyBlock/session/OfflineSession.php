<?php
/*
 * Copyright (C) Andrés Arias - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

namespace SkyBlock\session;


class OfflineSession extends iSession {
    
    /**
     * @return null|Session
     */
    public function getSession(): ?Session {
        $player = $this->manager->getPlugin()->getServer()->getPlayerExact($this->username);
        if($player != null) {
            return $this->manager->getSession($player);
        }
        return null;
    }
    
}