<?php

namespace SkyBlock\generator;

use pocketmine\level\generator\Generator;

abstract class SkyBlockGenerator extends Generator {

    /** @var string */
    protected $islandName;

    /**
     * Return island name
     *
     * @return string
     */
    public function getIslandName() {
        return $this->islandName;
    }

    /**
     * Set island name
     *
     * @param string $name
     */
    public function setIslandName($name) {
        $this->islandName = $name;
    }

}