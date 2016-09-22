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

namespace SkyBlock\chat;

use pocketmine\Player;
use SkyBlock\island\Island;

class Chat {

    /** @var Island */
    private $island;

    /** @var Player[] */
    private $members = [];

    /**
     * Chat constructor.
     *
     * @param Island $island
     */
    public function __construct(Island $island) {
        $this->island = $island;
    }

    /**
     * Return chat island
     *
     * @return Island
     */
    public function getIsland() {
        return $this->island;
    }

    /**
     * @return Player[]
     */
    public function getMembers() {
        return $this->members;
    }

    /**
     * Add a player to the chat
     *
     * @param Player $player
     */
    public function addMember(Player $player) {
        $this->members[] = $player;
    }

    /**
     * Try remove member
     *
     * @param Player $player
     */
    public function tryRemoveMember(Player $player) {
        if(in_array($player, $this->members)) {
            unset($this->members[array_search($player, $this->members)]);
        }
    }

}