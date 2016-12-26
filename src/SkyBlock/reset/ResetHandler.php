<?php

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