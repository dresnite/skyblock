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

namespace SkyBlock\generator;

use pocketmine\level\generator\GeneratorManager as GManager;
use SkyBlock\generator\generators\BasicIsland;
use SkyBlock\generator\generators\OPIsland;
use SkyBlock\generator\generators\ShellyGenerator;
use SkyBlock\SkyBlock;

class IsleGeneratorManager {

    /** @var SkyBlock */
    private $plugin;

    /** @var string[] */
    private $generators = [
        "Basic" => BasicIsland::class,
        "OP" => OPIsland::class,
        "Shelly" => ShellyGenerator::class
    ];
    
    /**
     * GeneratorManager constructor.
     * @param SkyBlock $plugin
     */
    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
        foreach($this->generators as $name => $class) {
            GManager::addGenerator($class, $name);
        }
    }
    
    /**
     * @return string[]
     */
    public function getGenerators(): array {
        return $this->generators;
    }
    
    /**
     * @param string $name
     * @return null|string
     */
    public function getGenerator(string $name): ?string {
        return $this->generators[$name] ?? null;
    }

    /**
     * Return if a generator exists
     *
     * @param string $name
     * @return bool
     */
    public function isGenerator(string $name) {
        return isset($this->generators[$name]);
    }
    
    /**
     * @param string $name
     * @param string $class
     */
    public function registerGenerator(string $name, string $class): void {
        GManager::addGenerator($class, $name);
        if(isset($this->generators[$name])) {
            $this->plugin->getLogger()->debug("Overwriting generator: $name");
        }
        $this->generators[$name] = $class;
    }

}