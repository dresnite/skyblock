<?php

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