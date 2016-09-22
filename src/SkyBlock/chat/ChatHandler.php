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

class ChatHandler {

    /** @var Chat[] */
    private $chats = [];

    public function isInChat(Player $player) {
        foreach($this->chats as $chat) {
            if(in_array($player, $chat->getMembers())) {
                return true;
            }
        }
        return false;
    }

    public function getPlayerChat(Player $player) {
        foreach($this->chats as $chat) {
            if(in_array($player, $chat->getMembers())) {
                return $chat;
            }
        }
        return null;
    }

    public function addPlayerToChat(Player $player, Island $island) {
        if(!isset($this->chats[$island->getIdentifier()])) {
            $this->chats[$island->getIdentifier()] = new Chat($island);
        }
        $this->chats[$island->getIdentifier()]->addMember($player);
    }

    public function removePlayerFromChat(Player $player) {
        foreach($this->chats as $chat) {
            if(in_array($player, $chat->getMembers())) {
                $chat->tryRemoveMember($player);
            }
        }
    }

}