<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace room17\SkyBlock\event\island;


use pocketmine\block\Block;
use pocketmine\Player;
use room17\SkyBlock\island\Island;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\session\SessionLocator;

abstract class IslandBlockEvent extends IslandEvent {

    /** @var Player */
    private $player;

    /** @var Block */
    private $block;

    public function __construct(Island $island, Player $player, Block $block) {
        $this->player = $player;
        $this->block = $block;
        parent::__construct($island);
    }

    public function getPlayer(): Player {
        return $this->player;
    }

    public function getSession(): Session {
        return SessionLocator::getSession($this->player);
    }

    public function getBlock(): Block {
        return $this->block;
    }

}