<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace room17\SkyBlock\island\generator;

use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\permission\PermissionParser;
use pocketmine\world\generator\GeneratorManager;
use room17\SkyBlock\island\generator\presets\BasicIsland;
use room17\SkyBlock\island\generator\presets\LostIsland;
use room17\SkyBlock\island\generator\presets\OPIsland;
use room17\SkyBlock\island\generator\presets\PalmIsland;
use room17\SkyBlock\island\generator\presets\ShellyGenerator;
use room17\SkyBlock\SkyBlock;

class IslandGeneratorManager {

    private SkyBlock $plugin;

    /** @var IslandGenerator[]|string[] */
    private array $generators = [];

    /**
     * GeneratorManager constructor.
     * @param SkyBlock $plugin
     */
    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
        $this->registerDefaultGenerators();
    }

    /**
     * Returns the name of all the generators
     *
     * @return string[]
     */
    public function getGenerators(): array {
        return array_keys($this->generators);
    }

    /**
     * @param string $name
     * @return null|string|IslandGenerator
     */
    public function getGenerator(string $name): ?string {
        return $this->generators[strtolower($name)] ?? null;
    }

    public function isGenerator(string $name): bool {
        return isset($this->generators[strtolower($name)]);
    }

    public function registerGenerator(string $name, string $class): void {
        $generator = strtolower($name);
        GeneratorManager::getInstance()->addGenerator($class, $generator, fn() => null, true);
        if(isset($this->generators[$generator])) {
            $this->plugin->getLogger()->debug("Overwriting generator: $generator");
        }
        $this->generators[$generator] = $class;
        $this->registerGeneratorPermission($generator);
    }

    private function registerGeneratorPermission(string $name): void {
        PermissionManager::getInstance()->addPermission(new Permission("skyblock.island." . $name, "",
            [PermissionParser::DEFAULT_TRUE]));
    }

    private function registerDefaultGenerators(): void {
        $this->registerGenerator("basic", BasicIsland::class);
        $this->registerGenerator("op", OPIsland::class);
        $this->registerGenerator("shelly", ShellyGenerator::class);
        $this->registerGenerator("palm", PalmIsland::class);
        $this->registerGenerator("lost", LostIsland::class);
    }

}