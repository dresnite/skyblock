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

class ResetHandler {

    /** @var Reset[] */
    private $resets = [];

    /**
     * Return reset timers
     *
     * @return Reset[]
     */
    public function getResetTimers() {
        return $this->resets;
    }

    /**
     * Return a Reset timer
     *
     * @param Player $player
     * @return null|Reset
     */
    public function getResetTimer(Player $player) {
        if(isset($this->resets[strtolower($player->getName())])) {
            return $this->resets[strtolower($player->getName())];
        }
        return null;
    }

    /**
     * Create a new reset timer
     *
     * @param Player $player
     */
    public function addResetTimer(Player $player) {
        $this->resets[strtolower($player->getName())] = new Reset($this, $player);
    }

    /**
     * Remove a reset
     *
     * @param Reset $reset
     */
    public function removeReset(Reset $reset) {
        if(in_array($reset, $this->resets)) {
            unset($this->resets[array_search($reset, $this->resets)]);
        }
    }

    public function tick() {
        foreach($this->resets as $reset) {
            $reset->tick();
        }
    }

}