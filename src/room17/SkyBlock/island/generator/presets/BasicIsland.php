<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace room17\SkyBlock\island\generator\presets;

use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\level\generator\object\Tree;
use pocketmine\math\Vector3;
use room17\SkyBlock\island\generator\IslandGenerator;

class BasicIsland extends IslandGenerator {

    public function getName(): string {
        return "Basic";
    }

    public function generateChunk(int $chunkX, int $chunkZ): void {
        $chunk = $this->level->getChunk($chunkX, $chunkZ);
        $chunk->setGenerated();
        if($chunkX == 0 && $chunkZ == 0) {
            for($x = 6; $x < 12; $x++) {
                for($z = 6; $z < 12; $z++) {
                    $chunk->setBlock($x, 61, $z, Block::DIRT);
                    $chunk->setBlock($x, 62, $z, Block::DIRT);
                    $chunk->setBlock($x, 63, $z, Block::GRASS);
                }
            }
            for($airX = 9; $airX < 12; $airX++) {
                for($airZ = 9; $airZ < 12; $airZ++) {
                    $chunk->setBlock($airX, 61, $airZ, Block::AIR);
                    $chunk->setBlock($airX, 62, $airZ, Block::AIR);
                    $chunk->setBlock($airX, 63, $airZ, Block::AIR);
                }
            }
            Tree::growTree($this->level, 11, 64, 6, $this->random, 0);
            $chunk->setBlock(8, 64, 7, Block::CHEST);
            $chunk->setX($chunkX);
            $chunk->setZ($chunkZ);
            $this->level->setChunk($chunkX, $chunkZ, $chunk);
        }
        if($chunkX == 4 and $chunkZ == 0) {
            for($x = 6; $x < 11; $x++) {
                for($z = 6; $z < 11; $z++) {
                    for($y = 60; $y < 65; $y++) {
                        $chunk->setBlock($x, $y, $z, Block::SAND);
                    }
                }
            }
            $chunk->setBlock(8, 65, 8, BlockIds::CACTUS);
        }
    }

    public static function getWorldSpawn(): Vector3 {
        return new Vector3(7, 66, 7);
    }

    public static function getChestPosition(): Vector3 {
        return new Vector3(8, 64, 7);
    }

}
