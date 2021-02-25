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
use room17\SkyBlock\island\Island;

abstract class IslandBlockEvent extends IslandEvent {

    /** @var Block */
    private $block;

    public function __construct(Island $island, Block $block) {
        $this->block = $block;
        parent::__construct($island);
    }

    public function getBlock(): Block {
        return $this->block;
    }

}