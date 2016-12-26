<?php

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