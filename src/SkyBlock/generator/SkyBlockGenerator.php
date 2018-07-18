<?php

namespace SkyBlock\generator;

use pocketmine\level\generator\Generator;
use pocketmine\math\Vector3;

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

    abstract public static function getChestLocation() : Vector3;

    abstract public static function getIslandSpawn(): Vector3;

}