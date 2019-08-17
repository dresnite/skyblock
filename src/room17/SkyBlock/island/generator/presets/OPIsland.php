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

namespace room17\SkyBlock\island\generator\presets;

use pocketmine\block\Block;
use pocketmine\level\generator\object\Tree;
use pocketmine\math\Vector3;
use room17\SkyBlock\island\generator\IslandGenerator;

class OPIsland extends IslandGenerator {

    /**
     * @return string
     */
    public function getName(): string {
        return "OP";
    }

    /**
     * @param int $chunkX
     * @param int $chunkZ
     */
    public function generateChunk(int $chunkX, int $chunkZ): void {
        $chunk = $this->level->getChunk($chunkX, $chunkZ);
        $chunk->setGenerated();
        if($chunkX == 0 && $chunkZ == 0) {
            for($x = 0; $x < 16; $x++) {
                for($z = 0; $z < 16; $z++) {
                    $chunk->setBlock($x, 0, $z, Block::BEDROCK);
                    for($y = 1; $y <= 3; $y++) {
                        $chunk->setBlock($x, $y, $z, Block::STONE);
                    }
                    $chunk->setBlock($x, 4, $z, Block::DIRT);
                    $chunk->setBlock($x, 5, $z, Block::GRASS);
                }
            }
            Tree::growTree($this->level, 8, 6, 8, $this->random, 0);
            $chunk->setBlock(10, 6, 8, Block::CHEST);
            $chunk->setX($chunkX);
            $chunk->setZ($chunkZ);
            $this->level->setChunk($chunkX, $chunkZ, $chunk);
        }
    }

    /**
     * @param int $chunkX
     * @param int $chunkZ
     */
    public function populateChunk(int $chunkX, int $chunkZ): void {
        return;
    }

    /**
     * @return Vector3
     */
    public static function getWorldSpawn(): Vector3 {
        return new Vector3(8, 7, 10);
    }

    /**
     * @return Vector3
     */
    public static function getChestPosition(): Vector3 {
        return new Vector3(10, 6, 8);
    }

    /**
     * @return Vector3
     */
    public function getSpawn(): Vector3 {
        return new Vector3(8, 7, 10);
    }

}