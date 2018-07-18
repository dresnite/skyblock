<?php

namespace SkyBlock\generator;

use pocketmine\level\generator\GeneratorManager;
use SkyBlock\generator\generators\BasicIsland;
use SkyBlock\generator\generators\LegacyIsland;
use SkyBlock\SkyBlock;

class SkyBlockGeneratorManager {

    /** @var SkyBlock */
    private $plugin;

    /** @var string|SkyBlockGenerator[] */
    private $generators = [];

    /**
     * SkyBlockGeneratorManager constructor.
     *
     * @param SkyBlock $plugin
     */
    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
        $this->registerGenerator(BasicIsland::class, "basic", "Basic Island");
        $this->registerGenerator(LegacyIsland::class, "legacy", "Legacy Island");
        GeneratorManager::addGenerator(BasicIsland::class, "basicgen");
        GeneratorManager::addGenerator(LegacyIsland::class, "legacygen");
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

    /**
     * Return array of strings with SkyBlockGenerator generator class names.
     *
     * @return string|SkyBlockGenerator[]
     */
    public function getGenerators() {
        return $this->generators;
    }

    public function getGeneratorIslandName($name) {
        return $this->isGenerator($name) ? $this->generators[$name] : "";
    }

    /**
     * Register a generator
     *
     * @param $generator
     * @param string $name
     * @param string $islandName
     */
    public function registerGenerator($generator, $name, $islandName) {
        GeneratorManager::addGenerator($generator, $name);
        $this->generators[$name] = $islandName;
    }

}