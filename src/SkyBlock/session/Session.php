<?php
/*
 * Copyright (C) Andrés Arias - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

namespace SkyBlock\session;


use pocketmine\Player;

class Session {
    
    /** @var Player */
    private $player;
    
    /**
     * Session constructor.
     * @param Player $player
     */
    public function __construct(Player $player) {
        $this->player = $player;
    }
    
    /**
     * @return Player
     */
    public function getPlayer(): Player {
        return $this->player;
    }
    
}