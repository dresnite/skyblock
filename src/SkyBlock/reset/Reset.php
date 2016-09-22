<?php

/**
 * This is GiantQuartz property.
 *
 * Copyright (C) 2016 GiantQuartz
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author GiantQuartz
 *
 */

namespace SkyBlock\reset;

use pocketmine\Player;

class Reset {

    /** @var ResetHandler */
    private $handler;

    /** @var Player */
    private $player;

    /** @var int */
    private $time = 600;

    /**
     * Reset constructor.
     *
     * @param ResetHandler $handler
     * @param Player $player
     */
    public function __construct(ResetHandler $handler, Player $player) {
        $this->handler = $handler;
        $this->player = $player;
    }

    /**
     * Return time left to be able to reset
     *
     * @return int
     */
    public function getTime() {
        return $this->time;
    }

    public function expire() {
        $this->handler->removeReset($this);
    }

    public function tick() {
        if($this->time <= 0) {
            $this->expire();
        }
        else {
            $this->time--;
        }
    }

}