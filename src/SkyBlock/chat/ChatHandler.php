<?php

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