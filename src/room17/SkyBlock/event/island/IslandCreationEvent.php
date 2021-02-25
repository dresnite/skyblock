<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace room17\SkyBlock\event\island;


use room17\SkyBlock\event\SkyBlockEvent;
use room17\SkyBlock\island\Island;

class IslandCreationEvent extends SkyBlockEvent {

    /** @var string */
    private $islandClass;

    public function __construct(string $islandClass){
        $this->islandClass = $islandClass;
    }

    public function getIslandClass() : string{
        return $this->islandClass;
    }

    public function setIslandClass(string $islandClass) : void{
        if(!is_a($islandClass, Island::class, true)) {
            throw new \InvalidArgumentException("$islandClass must extend Island");
        }

        $this->islandClass = $islandClass;
    }

}