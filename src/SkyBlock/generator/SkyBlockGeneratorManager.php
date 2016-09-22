<?php

/**
 * This is GiantQuartz property.
 *
 * Copyright (C) 2016 GiantQuartz
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author GiantQuartz
 *
 */

namespace SkyBlock\generator;

use pocketmine\level\generator\Generator;
use SkyBlock\generator\generators\BasicIsland;
use SkyBlock\Main;

class SkyBlockGeneratorManager {

    /** @var Main */
    private $plugin;

    /** @var string[] */
    private $generators = [];

    /**
     * SkyBlockGeneratorManager constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $this->registerGenerator(BasicIsland::class, "basic", "Basic Island");
        Generator::addGenerator(BasicIsland::class, "basicgen");
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
     * Return skyblock generators
     *
     * @return SkyBlockGenerator[]
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
        Generator::addGenerator($generator, $name);
        $this->generators[$name] = $islandName;
    }

}