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

namespace room17\SkyBlock\generator;


use pocketmine\level\generator\Generator;
use pocketmine\math\Vector3;

abstract class IsleGenerator extends Generator {
    
    /** @var array */
    protected $settings;
    
    /**
     * IsleGenerator constructor.
     * @param array $settings
     */
    public function __construct(array $settings = []) {
        $this->settings = $settings;
    }
    
    /**
     * @return array
     */
    public function getSettings(): array {
        return $this->settings;
    }
    
    /**
     * @return Vector3
     */
    public abstract static function getWorldSpawn(): Vector3;
    
    /**
     * @return Vector3
     */
    public abstract static function getChestPosition(): Vector3;
    
}