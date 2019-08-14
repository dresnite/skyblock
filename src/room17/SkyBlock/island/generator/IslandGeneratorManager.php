<?php
/**
 *  _____    ____    ____   __  __  __  ______
 * |  __ \  / __ \  / __ \ |  \/  |/_ ||____  |
 * | |__) || |  | || |  | || \  / | | |    / /
 * |  _  / | |  | || |  | || |\/| | | |   / /
 * | | \ \ | |__| || |__| || |  | | | |  / /
 * |_|  \_\ \____/  \____/ |_|  |_| |_| /_/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 */

declare(strict_types=1);

namespace room17\SkyBlock\island\generator;

use pocketmine\level\generator\GeneratorManager as GManager;
use room17\SkyBlock\island\generator\presets\BasicIsland;
use room17\SkyBlock\island\generator\presets\LostIsland;
use room17\SkyBlock\island\generator\presets\OPIsland;
use room17\SkyBlock\island\generator\presets\PalmIsland;
use room17\SkyBlock\island\generator\presets\ShellyGenerator;
use room17\SkyBlock\SkyBlock;

class IslandGeneratorManager {

    /** @var SkyBlock */
    private $plugin;

    /** @var IslandGenerator[]|string[] */
    private $generators = [];
    
    /**
     * GeneratorManager constructor.
     * @param SkyBlock $plugin
     */
    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
        $this->registerDefaultGenerators();
    }
    
    /**
     * @return string[]
     */
    public function getGenerators(): array {
        return $this->generators;
    }
    
    /**
     * @param string $name
     * @return null|string|IslandGenerator
     */
    public function getGenerator(string $name): ?string {
        return $this->generators[strtolower($name)] ?? null;
    }

    /**
     * Return if a generator exists
     *
     * @param string $name
     * @return bool
     */
    public function isGenerator(string $name): bool {
        return isset($this->generators[strtolower($name)]);
    }
    
    /**
     * @param string $name
     * @param string $class
     */
    public function registerGenerator(string $name, string $class): void {
        GManager::addGenerator($class, $name, true);
        if(isset($this->generators[$name])) {
            $this->plugin->getLogger()->debug("Overwriting generator: $name");
        }
        $this->generators[$name] = $class;
    }

    private function registerDefaultGenerators(): void {
        $this->registerGenerator("basic", BasicIsland::class);
        $this->registerGenerator("op", OPIsland::class);
        $this->registerGenerator("shelly", ShellyGenerator::class);
        $this->registerGenerator("palm", PalmIsland::class);
        $this->registerGenerator("lost", LostIsland::class);
    }

}