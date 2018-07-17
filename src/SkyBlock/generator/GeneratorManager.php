<?php

namespace SkyBlock\generator;

use pocketmine\level\generator\GeneratorManager as GManager;
use SkyBlock\generator\generators\BasicIsland;
use SkyBlock\SkyBlock;

class GeneratorManager {

    /** @var SkyBlock */
    private $plugin;

    const GENERATORS = [
        "Basic" => BasicIsland::class
    ];
    
    /**
     * SkyBlockGeneratorManager constructor.
     *
     * @param SkyBlock $plugin
     */
    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
        foreach(self::GENERATORS as $name => $class) {
            GManager::addGenerator($class, $name);
        }
    }

    /**
     * Return if a generator exists
     *
     * @param $name
     * @return bool
     */
    public function isGenerator($name) {
        return isset($this->generators[$name]);
    }

}