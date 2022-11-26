<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace room17\SkyBlock\island\generator\presets;


use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\world\ChunkManager;
use room17\SkyBlock\island\generator\IslandGenerator;

class LostIsland extends IslandGenerator {

    public function getName(): string {
        return "Lost";
    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {
        $chunk = $world->getChunk($chunkX, $chunkZ);
        if($chunkX == 0 and $chunkZ == 0) {
            $world->setBlockAt(11, 34, 9, VanillaBlocks::GRASS());
            $world->setBlockAt(11, 35, 9, VanillaBlocks::OAK_FENCE());
            $world->setBlockAt(11, 32, 8, VanillaBlocks::DIRT());
            $world->setBlockAt(11, 33, 8, VanillaBlocks::DIRT());
            $world->setBlockAt(11, 34, 8, VanillaBlocks::GRASS());
            $world->setBlockAt(11, 35, 8, VanillaBlocks::OAK_FENCE());
            $world->setBlockAt(11, 33, 7, VanillaBlocks::DIRT());
            $world->setBlockAt(11, 34, 7, VanillaBlocks::GRASS());
            $world->setBlockAt(11, 35, 7, VanillaBlocks::OAK_FENCE());
            $world->setBlockAt(10, 33, 10, VanillaBlocks::DIRT());
            $world->setBlockAt(10, 34, 10, VanillaBlocks::ICE());
            $world->setBlockAt(10, 32, 9, VanillaBlocks::STONE());
            $world->setBlockAt(10, 33, 9, VanillaBlocks::DIRT());
            $world->setBlockAt(10, 34, 9, VanillaBlocks::COBBLESTONE());
            $world->setBlockAt(10, 31, 8, VanillaBlocks::STONE());
            $world->setBlockAt(10, 32, 8, VanillaBlocks::STONE());
            $world->setBlockAt(10, 33, 8, VanillaBlocks::DIRT());
            $world->setBlockAt(10, 34, 8, VanillaBlocks::SAND());
            $world->setBlockAt(10, 32, 7, VanillaBlocks::STONE());
            $world->setBlockAt(10, 33, 7, VanillaBlocks::DIRT());
            $world->setBlockAt(10, 34, 7, VanillaBlocks::GRAVEL());
            $world->setBlockAt(10, 35, 7, VanillaBlocks::OAK_FENCE());
            $world->setBlockAt(10, 33, 6, VanillaBlocks::DIRT());
            $world->setBlockAt(10, 34, 6, VanillaBlocks::GRASS());
            $world->setBlockAt(10, 35, 6, VanillaBlocks::OAK_FENCE());
            $world->setBlockAt(9, 34, 11, VanillaBlocks::GRASS());
            $world->setBlockAt(9, 32, 10, VanillaBlocks::STONE());
            $world->setBlockAt(9, 33, 10, VanillaBlocks::DIRT());
            $world->setBlockAt(9, 34, 10, VanillaBlocks::ICE());
            $world->setBlockAt(9, 31, 9, VanillaBlocks::STONE());
            $world->setBlockAt(9, 32, 9, VanillaBlocks::STONE());
            $world->setBlockAt(9, 33, 9, VanillaBlocks::DIRT());
            $world->setBlockAt(9, 34, 9, VanillaBlocks::ICE());
            $world->setBlockAt(9, 30, 8, VanillaBlocks::STONE());
            $world->setBlockAt(9, 31, 8, VanillaBlocks::STONE());
            $world->setBlockAt(9, 32, 8, VanillaBlocks::STONE());
            $world->setBlockAt(9, 33, 8, VanillaBlocks::DIRT());
            $world->setBlockAt(9, 34, 8, VanillaBlocks::GRAVEL());
            $world->setBlockAt(9, 31, 7, VanillaBlocks::STONE());
            $world->setBlockAt(9, 32, 7, VanillaBlocks::STONE());
            $world->setBlockAt(9, 33, 7, VanillaBlocks::DIRT());
            $world->setBlockAt(9, 34, 7, VanillaBlocks::COBBLESTONE());
            $world->setBlockAt(9, 32, 6, VanillaBlocks::STONE());
            $world->setBlockAt(9, 33, 6, VanillaBlocks::DIRT());
            $world->setBlockAt(9, 34, 6, VanillaBlocks::GRAVEL());
            $world->setBlockAt(9, 35, 6, VanillaBlocks::OAK_FENCE());
            $world->setBlockAt(9, 33, 5, VanillaBlocks::DIRT());
            $world->setBlockAt(9, 34, 5, VanillaBlocks::GRASS());
            $world->setBlockAt(8, 32, 11, VanillaBlocks::DIRT());
            $world->setBlockAt(8, 33, 11, VanillaBlocks::DIRT());
            $world->setBlockAt(8, 34, 11, VanillaBlocks::GRASS());
            $world->setBlockAt(8, 31, 10, VanillaBlocks::STONE());
            $world->setBlockAt(8, 32, 10, VanillaBlocks::STONE());
            $world->setBlockAt(8, 33, 10, VanillaBlocks::DIRT());
            $world->setBlockAt(8, 34, 10, VanillaBlocks::ICE());
            $world->setBlockAt(8, 30, 9, VanillaBlocks::STONE());
            $world->setBlockAt(8, 31, 9, VanillaBlocks::STONE());
            $world->setBlockAt(8, 32, 9, VanillaBlocks::STONE());
            $world->setBlockAt(8, 33, 9, VanillaBlocks::DIRT());
            $world->setBlockAt(8, 34, 9, VanillaBlocks::ICE());
            $world->setBlockAt(8, 30, 8, VanillaBlocks::STONE());
            $world->setBlockAt(8, 31, 8, VanillaBlocks::STONE());
            $world->setBlockAt(8, 32, 8, VanillaBlocks::STONE());
            $world->setBlockAt(8, 33, 8, VanillaBlocks::DIRT());
            $world->setBlockAt(8, 34, 8, VanillaBlocks::ICE());
            $world->setBlockAt(8, 30, 7, VanillaBlocks::STONE());
            $world->setBlockAt(8, 31, 7, VanillaBlocks::STONE());
            $world->setBlockAt(8, 32, 7, VanillaBlocks::STONE());
            $world->setBlockAt(8, 33, 7, VanillaBlocks::DIRT());
            $world->setBlockAt(8, 34, 7, VanillaBlocks::GRAVEL());
            $world->setBlockAt(8, 31, 6, VanillaBlocks::STONE());
            $world->setBlockAt(8, 32, 6, VanillaBlocks::STONE());
            $world->setBlockAt(8, 33, 6, VanillaBlocks::DIRT());
            $world->setBlockAt(8, 34, 6, VanillaBlocks::SAND());
            $world->setBlockAt(8, 32, 5, VanillaBlocks::DIRT());
            $world->setBlockAt(8, 33, 5, VanillaBlocks::DIRT());
            $world->setBlockAt(8, 34, 5, VanillaBlocks::GRAVEL());
            $world->setBlockAt(7, 33, 11, VanillaBlocks::DIRT());
            $world->setBlockAt(7, 34, 11, VanillaBlocks::GRASS());
            $world->setBlockAt(7, 32, 10, VanillaBlocks::STONE());
            $world->setBlockAt(7, 33, 10, VanillaBlocks::DIRT());
            $world->setBlockAt(7, 34, 10, VanillaBlocks::GRASS());
            $world->setBlockAt(7, 31, 9, VanillaBlocks::STONE());
            $world->setBlockAt(7, 32, 9, VanillaBlocks::STONE());
            $world->setBlockAt(7, 33, 9, VanillaBlocks::DIRT());
            $world->setBlockAt(7, 34, 9, VanillaBlocks::ICE());
            $world->setBlockAt(7, 30, 8, VanillaBlocks::STONE());
            $world->setBlockAt(7, 31, 8, VanillaBlocks::STONE());
            $world->setBlockAt(7, 32, 8, VanillaBlocks::STONE());
            $world->setBlockAt(7, 33, 8, VanillaBlocks::DIRT());
            $world->setBlockAt(7, 34, 8, VanillaBlocks::ICE());
            $world->setBlockAt(7, 33, 8, VanillaBlocks::DIRT());
            $world->setBlockAt(7, 31, 7, VanillaBlocks::STONE());
            $world->setBlockAt(7, 32, 7, VanillaBlocks::STONE());
            $world->setBlockAt(7, 33, 7, VanillaBlocks::DIRT());
            $world->setBlockAt(7, 34, 7, VanillaBlocks::COBBLESTONE());
            $world->setBlockAt(7, 32, 6, VanillaBlocks::STONE());
            $world->setBlockAt(7, 33, 6, VanillaBlocks::DIRT());
            $world->setBlockAt(7, 34, 6, VanillaBlocks::GRAVEL());
            $world->setBlockAt(7, 34, 5, VanillaBlocks::GRASS());
            $world->setBlockAt(6, 32, 10, VanillaBlocks::DIRT());
            $world->setBlockAt(6, 33, 10, VanillaBlocks::DIRT());
            $world->setBlockAt(6, 34, 10, VanillaBlocks::GRASS());
            $world->setBlockAt(6, 32, 9, VanillaBlocks::STONE());
            $world->setBlockAt(6, 33, 9, VanillaBlocks::DIRT());
            $world->setBlockAt(6, 34, 9, VanillaBlocks::GRASS());
            $world->setBlockAt(6, 31, 8, VanillaBlocks::STONE());
            $world->setBlockAt(6, 32, 8, VanillaBlocks::STONE());
            $world->setBlockAt(6, 33, 8, VanillaBlocks::DIRT());
            $world->setBlockAt(6, 34, 8, VanillaBlocks::DIRT());
            $world->setBlockAt(6, 35, 8, VanillaBlocks::HAY_BALE());
            $world->setBlockAt(6, 32, 7, VanillaBlocks::STONE());
            $world->setBlockAt(6, 33, 7, VanillaBlocks::DIRT());
            $world->setBlockAt(6, 34, 7, VanillaBlocks::SAND());
            $world->setBlockAt(6, 32, 6, VanillaBlocks::DIRT());
            $world->setBlockAt(6, 33, 6, VanillaBlocks::DIRT());
            $world->setBlockAt(6, 34, 6, VanillaBlocks::GRASS());
            $world->setBlockAt(5, 33, 9, VanillaBlocks::DIRT());
            $world->setBlockAt(5, 34, 9, VanillaBlocks::DIRT());
            $world->setBlockAt(5, 35, 9, VanillaBlocks::HAY_BALE());
            $world->setBlockAt(5, 32, 8, VanillaBlocks::DIRT());
            $world->setBlockAt(5, 33, 8, VanillaBlocks::DIRT());
            $world->setBlockAt(5, 34, 8, VanillaBlocks::DIRT());
            $world->setBlockAt(5, 35, 8, VanillaBlocks::HAY_BALE());
            $world->setBlockAt(5, 36, 8, VanillaBlocks::HAY_BALE());
            $world->setBlockAt(5, 33, 7, VanillaBlocks::DIRT());
            $world->setBlockAt(5, 34, 7, VanillaBlocks::DIRT());
            $world->setBlockAt(5, 35, 7, VanillaBlocks::OAK_LOG());
            $world->setBlockAt(5, 36, 7, VanillaBlocks::OAK_LOG());
            $world->setBlockAt(5, 37, 7, VanillaBlocks::OAK_LOG());
            $world->setBlockAt(5, 38, 7, VanillaBlocks::OAK_LOG());
            $world->setBlockAt(5, 39, 7, VanillaBlocks::OAK_LOG());
            $world->setBlockAt(5, 40, 7, VanillaBlocks::OAK_LOG());
            $world->setBlockAt(5, 41, 7, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(5, 38, 6, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(5, 39, 6, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(5, 40, 6, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(5, 41, 6, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(6, 38, 7, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(6, 39, 7, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(6, 40, 7, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(6, 41, 7, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(5, 38, 8, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(5, 39, 8, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(5, 40, 8, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(5, 41, 8, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(4, 38, 7, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(5, 39, 8, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(5, 40, 8, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(5, 41, 8, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(7, 38, 7, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(7, 39, 7, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(7, 40, 7, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(6, 38, 8, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(6, 39, 8, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(6, 40, 8, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(5, 38, 9, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(5, 39, 9, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(5, 40, 9, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(4, 38, 8, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(4, 39, 8, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(4, 40, 8, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(3, 38, 7, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(3, 39, 7, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(3, 40, 7, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(4, 38, 6, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(4, 39, 6, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(4, 40, 6, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(5, 38, 5, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(5, 39, 5, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(5, 40, 5, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(6, 38, 6, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(6, 39, 6, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(6, 40, 6, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(7, 39, 8, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(7, 38, 6, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(6, 39, 5, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(4, 38, 5, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(4, 39, 5, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(3, 38, 6, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(3, 39, 8, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(4, 39, 9, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(6, 38, 9, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(4, 39, 7, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(4, 40, 7, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(4, 41, 7, VanillaBlocks::OAK_LEAVES());
            $world->setBlockAt(6, 35, 9, VanillaBlocks::CHEST());
            $world->setChunk($chunkX, $chunkZ, $chunk);
        }
    }

    public static function getWorldSpawn(): Vector3 {
        return new Vector3(10, 35, 9);
    }

    public static function getChestPosition(): Vector3 {
        return new Vector3(6, 35, 9);
    }

}