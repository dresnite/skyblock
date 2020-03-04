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
use pocketmine\math\Vector3;
use room17\SkyBlock\island\generator\IslandGenerator;

class PalmIsland extends IslandGenerator {

    public function getName(): string {
        return "Palm";
    }

    public function generateChunk(int $chunkX, int $chunkZ): void {
        $chunk = $this->level->getChunk($chunkX, $chunkZ);
        $chunk->setGenerated();
        if($chunkX == 0 and $chunkZ == 0) {
            $chunk->setBlock(9, 39, 4, Block::SAND);
            $chunk->setBlock(8, 37, 4, Block::SAND);
            $chunk->setBlock(8, 35, 5, Block::SAND);
            $chunk->setBlock(8, 39, 4, Block::SAND);
            $chunk->setBlock(7, 39, 4, Block::SAND);
            $chunk->setBlock(10, 39, 5, Block::SAND);
            $chunk->setBlock(10, 39, 6, Block::END_BRICKS);
            $chunk->setBlock(11, 39, 7, Block::SAND);
            $chunk->setBlock(11, 39, 8, Block::END_BRICKS);
            $chunk->setBlock(10, 39, 8, Block::SAND);
            $chunk->setBlock(11, 39, 9, Block::SAND);
            $chunk->setBlock(10, 39, 9, Block::SAND);
            $chunk->setBlock(9, 39, 9, Block::END_BRICKS);
            $chunk->setBlock(10, 39, 10, Block::SAND);
            $chunk->setBlock(9, 39, 10, Block::GOLD_BLOCK);
            $chunk->setBlock(9, 39, 11, Block::SAND);
            $chunk->setBlock(8, 39, 10, Block::SAND);
            $chunk->setBlock(8, 39, 9, Block::SAND);
            $chunk->setBlock(7, 39, 10, Block::SAND);
            $chunk->setBlock(8, 39, 11, Block::SANDSTONE);
            $chunk->setBlock(7, 39, 5, Block::END_BRICKS);
            $chunk->setBlock(6, 39, 8, Block::END_BRICKS);
            $chunk->setBlock(6, 39, 5, Block::SAND);
            $chunk->setBlock(6, 39, 6, Block::SAND);
            $chunk->setBlock(6, 39, 7, Block::SAND);
            $chunk->setBlock(5, 39, 7, Block::SAND);
            $chunk->setBlock(5, 39, 8, Block::SAND);
            $chunk->setBlock(5, 39, 9, Block::SAND);
            $chunk->setBlock(6, 39, 9, Block::SAND);
            $chunk->setBlock(6, 39, 10, Block::SAND);
            $chunk->setBlock(7, 39, 8, Block::SAND);
            $chunk->setBlock(7, 39, 10, Block::SAND);
            $chunk->setBlock(7, 39, 9, Block::SANDSTONE);
            $chunk->setBlock(7, 39, 7, Block::WATER);
            $chunk->setBlock(7, 39, 6, Block::WATER);
            $chunk->setBlock(8, 39, 8, Block::WATER);
            $chunk->setBlock(8, 39, 7, Block::WATER);
            $chunk->setBlock(8, 39, 6, Block::WATER);
            $chunk->setBlock(8, 39, 5, Block::WATER);
            $chunk->setBlock(9, 39, 8, Block::WATER);
            $chunk->setBlock(9, 39, 7, Block::WATER);
            $chunk->setBlock(10, 39, 7, Block::WATER);
            $chunk->setBlock(9, 39, 6, Block::WATER);
            $chunk->setBlock(9, 39, 5, Block::WATER);
            $chunk->setBlock(10, 38, 11, Block::SANDSTONE);
            $chunk->setBlock(9, 38, 11, Block::SANDSTONE);
            $chunk->setBlock(8, 38, 11, Block::SANDSTONE);
            $chunk->setBlock(7, 38, 11, Block::SANDSTONE);
            $chunk->setBlock(11, 38, 10, Block::SANDSTONE);
            $chunk->setBlock(10, 38, 10, Block::SANDSTONE);
            $chunk->setBlock(9, 38, 10, Block::SANDSTONE);
            $chunk->setBlock(8, 38, 10, Block::SANDSTONE);
            $chunk->setBlock(7, 38, 10, Block::SANDSTONE);
            $chunk->setBlock(6, 38, 10, Block::SANDSTONE);
            $chunk->setBlock(11, 38, 9, Block::SANDSTONE);
            $chunk->setBlock(10, 38, 9, Block::SANDSTONE);
            $chunk->setBlock(9, 38, 9, Block::SANDSTONE);
            $chunk->setBlock(8, 38, 9, Block::SANDSTONE);
            $chunk->setBlock(7, 38, 9, Block::SANDSTONE);
            $chunk->setBlock(6, 38, 9, Block::SANDSTONE);
            $chunk->setBlock(5, 38, 9, Block::SANDSTONE);
            $chunk->setBlock(11, 38, 8, Block::SANDSTONE);
            $chunk->setBlock(10, 38, 8, Block::SANDSTONE);
            $chunk->setBlock(9, 38, 8, Block::SANDSTONE);
            $chunk->setBlock(8, 38, 8, Block::SANDSTONE);
            $chunk->setBlock(7, 38, 8, Block::SANDSTONE);
            $chunk->setBlock(6, 38, 8, Block::SANDSTONE);
            $chunk->setBlock(5, 38, 8, Block::SANDSTONE);
            $chunk->setBlock(11, 38, 8, Block::SANDSTONE);
            $chunk->setBlock(10, 38, 8, Block::SANDSTONE);
            $chunk->setBlock(9, 38, 8, Block::SANDSTONE);
            $chunk->setBlock(8, 38, 8, Block::SANDSTONE);
            $chunk->setBlock(7, 38, 8, Block::SANDSTONE);
            $chunk->setBlock(6, 38, 8, Block::SANDSTONE);
            $chunk->setBlock(5, 38, 8, Block::SANDSTONE);
            $chunk->setBlock(11, 38, 7, Block::SANDSTONE);
            $chunk->setBlock(10, 38, 7, Block::SANDSTONE);
            $chunk->setBlock(9, 38, 7, Block::WATER);
            $chunk->setBlock(8, 38, 7, Block::WATER);
            $chunk->setBlock(7, 38, 7, Block::SANDSTONE);
            $chunk->setBlock(6, 38, 7, Block::SANDSTONE);
            $chunk->setBlock(5, 38, 7, Block::SANDSTONE);
            $chunk->setBlock(11, 38, 6, Block::SANDSTONE);
            $chunk->setBlock(10, 38, 6, Block::SANDSTONE);
            $chunk->setBlock(9, 38, 6, Block::WATER);
            $chunk->setBlock(8, 38, 6, Block::WATER);
            $chunk->setBlock(7, 38, 6, Block::SANDSTONE);
            $chunk->setBlock(6, 38, 6, Block::SANDSTONE);
            $chunk->setBlock(5, 38, 6, Block::SANDSTONE);
            $chunk->setBlock(10, 38, 5, Block::SANDSTONE);
            $chunk->setBlock(9, 38, 5, Block::SANDSTONE);
            $chunk->setBlock(8, 38, 5, Block::WATER);
            $chunk->setBlock(7, 38, 5, Block::SANDSTONE);
            $chunk->setBlock(6, 38, 5, Block::SANDSTONE);
            $chunk->setBlock(9, 38, 4, Block::SANDSTONE);
            $chunk->setBlock(8, 38, 4, Block::SANDSTONE);
            $chunk->setBlock(7, 38, 4, Block::SANDSTONE);
            $chunk->setBlock(10, 37, 11, Block::SANDSTONE);
            $chunk->setBlock(8, 37, 11, Block::SANDSTONE);
            $chunk->setBlock(7, 37, 11, Block::SANDSTONE);
            $chunk->setBlock(10, 37, 10, Block::SANDSTONE);
            $chunk->setBlock(9, 37, 10, Block::SANDSTONE);
            $chunk->setBlock(8, 37, 10, Block::SANDSTONE);
            $chunk->setBlock(7, 37, 10, Block::SANDSTONE);
            $chunk->setBlock(6, 37, 10, Block::SANDSTONE);
            $chunk->setBlock(11, 37, 8, Block::SANDSTONE);
            $chunk->setBlock(10, 37, 8, Block::SANDSTONE);
            $chunk->setBlock(9, 37, 8, Block::SANDSTONE);
            $chunk->setBlock(8, 37, 8, Block::SANDSTONE);
            $chunk->setBlock(7, 37, 8, Block::SANDSTONE);
            $chunk->setBlock(6, 37, 8, Block::SANDSTONE);
            $chunk->setBlock(5, 37, 8, Block::SANDSTONE);
            $chunk->setBlock(10, 37, 9, Block::SANDSTONE);
            $chunk->setBlock(9, 37, 9, Block::SANDSTONE);
            $chunk->setBlock(8, 37, 9, Block::SANDSTONE);
            $chunk->setBlock(7, 37, 9, Block::SANDSTONE);
            $chunk->setBlock(6, 37, 9, Block::SANDSTONE);
            $chunk->setBlock(11, 37, 7, Block::SANDSTONE);
            $chunk->setBlock(10, 37, 7, Block::SANDSTONE);
            $chunk->setBlock(9, 37, 7, Block::SANDSTONE);
            $chunk->setBlock(8, 37, 7, Block::SANDSTONE);
            $chunk->setBlock(7, 37, 7, Block::SANDSTONE);
            $chunk->setBlock(6, 37, 7, Block::SANDSTONE);
            $chunk->setBlock(5, 37, 7, Block::SANDSTONE);
            $chunk->setBlock(10, 37, 7, Block::SANDSTONE);
            $chunk->setBlock(10, 37, 6, Block::SANDSTONE);
            $chunk->setBlock(9, 37, 6, Block::SANDSTONE);
            $chunk->setBlock(8, 37, 6, Block::SANDSTONE);
            $chunk->setBlock(7, 37, 6, Block::SANDSTONE);
            $chunk->setBlock(6, 37, 6, Block::SANDSTONE);
            $chunk->setBlock(9, 37, 5, Block::SANDSTONE);
            $chunk->setBlock(8, 37, 5, Block::SANDSTONE);
            $chunk->setBlock(7, 37, 5, Block::SANDSTONE);
            $chunk->setBlock(8, 36, 11, Block::SANDSTONE);
            $chunk->setBlock(10, 36, 10, Block::SANDSTONE);
            $chunk->setBlock(9, 36, 10, Block::STONE);
            $chunk->setBlock(8, 36, 10, Block::STONE);
            $chunk->setBlock(7, 36, 10, Block::STONE);
            $chunk->setBlock(6, 36, 10, Block::SANDSTONE);
            $chunk->setBlock(10, 36, 9, Block::STONE);
            $chunk->setBlock(9, 36, 9, Block::STONE);
            $chunk->setBlock(8, 36, 9, Block::STONE);
            $chunk->setBlock(7, 36, 9, Block::STONE);
            $chunk->setBlock(6, 36, 9, Block::STONE);
            $chunk->setBlock(11, 36, 8, Block::SANDSTONE);
            $chunk->setBlock(10, 36, 8, Block::STONE);
            $chunk->setBlock(9, 36, 8, Block::STONE);
            $chunk->setBlock(8, 36, 8, Block::SANDSTONE);
            $chunk->setBlock(7, 36, 8, Block::STONE);
            $chunk->setBlock(6, 36, 8, Block::STONE);
            $chunk->setBlock(5, 36, 8, Block::SANDSTONE);
            $chunk->setBlock(10, 36, 7, Block::STONE);
            $chunk->setBlock(9, 36, 7, Block::SANDSTONE);
            $chunk->setBlock(8, 36, 7, Block::SANDSTONE);
            $chunk->setBlock(7, 36, 7, Block::STONE);
            $chunk->setBlock(6, 36, 7, Block::STONE);
            $chunk->setBlock(10, 36, 6, Block::SANDSTONE);
            $chunk->setBlock(9, 36, 6, Block::STONE);
            $chunk->setBlock(8, 36, 6, Block::STONE);
            $chunk->setBlock(7, 36, 6, Block::COBBLESTONE);
            $chunk->setBlock(6, 36, 6, Block::SANDSTONE);
            $chunk->setBlock(9, 36, 5, Block::SANDSTONE);
            $chunk->setBlock(8, 36, 5, Block::SANDSTONE);
            $chunk->setBlock(8, 35, 10, Block::COBBLESTONE);
            $chunk->setBlock(9, 35, 9, Block::STONE);
            $chunk->setBlock(8, 35, 9, Block::STONE);
            $chunk->setBlock(7, 35, 9, Block::STONE);
            $chunk->setBlock(6, 35, 9, Block::STONE);
            $chunk->setBlock(10, 35, 8, Block::STONE);
            $chunk->setBlock(9, 35, 8, Block::STONE);
            $chunk->setBlock(8, 35, 8, Block::STONE);
            $chunk->setBlock(7, 35, 8, Block::STONE);
            $chunk->setBlock(6, 35, 8, Block::COBBLESTONE);
            $chunk->setBlock(5, 35, 8, Block::SANDSTONE);
            $chunk->setBlock(9, 35, 7, Block::STONE);
            $chunk->setBlock(8, 35, 7, Block::STONE);
            $chunk->setBlock(7, 35, 7, Block::STONE);
            $chunk->setBlock(8, 35, 6, Block::STONE);
            $chunk->setBlock(9, 34, 9, Block::STONE);
            $chunk->setBlock(8, 34, 9, Block::COBBLESTONE);
            $chunk->setBlock(7, 34, 9, Block::STONE);
            $chunk->setBlock(9, 34, 8, Block::COBBLESTONE);
            $chunk->setBlock(8, 34, 8, Block::STONE);
            $chunk->setBlock(7, 34, 8, Block::COBBLESTONE);
            $chunk->setBlock(6, 34, 8, Block::COBBLESTONE);
            $chunk->setBlock(8, 34, 7, Block::COBBLESTONE);
            $chunk->setBlock(8, 33, 9, Block::STONE);
            $chunk->setBlock(8, 33, 8, Block::STONE);
            $chunk->setBlock(9, 33, 8, Block::COBBLESTONE);
            $chunk->setBlock(7, 33, 8, Block::STONE);
            $chunk->setBlock(8, 33, 7, Block::STONE);
            $chunk->setBlock(8, 32, 9, Block::STONE);
            $chunk->setBlock(8, 32, 8, Block::STONE);
            $chunk->setBlock(9, 32, 8, Block::STONE);
            $chunk->setBlock(8, 31, 8, Block::COBBLESTONE);
            $chunk->setBlock(8, 30, 8, Block::STONE);
            $chunk->setBlock(8, 29, 8, Block::STONE);
            $chunk->setBlock(7, 40, 8, Block::CHEST);
            $chunk->setBlock(5, 47, 5, Block::LEAVES, 15);
            $chunk->setBlock(5, 47, 6, Block::LEAVES, 15);
            $chunk->setBlock(6, 47, 6, Block::LEAVES, 15);
            $chunk->setBlock(6, 47, 7, Block::LEAVES, 15);
            $chunk->setBlock(6, 47, 8, Block::LEAVES, 15);
            $chunk->setBlock(6, 47, 7, Block::LEAVES, 15);
            $chunk->setBlock(8, 47, 6, Block::LEAVES, 15);
            $chunk->setBlock(9, 47, 6, Block::LEAVES, 15);
            $chunk->setBlock(10, 47, 6, Block::LEAVES, 15);
            $chunk->setBlock(10, 47, 5, Block::LEAVES, 15);
            $chunk->setBlock(11, 47, 5, Block::LEAVES, 15);
            $chunk->setBlock(7, 47, 7, Block::LEAVES, 15);
            $chunk->setBlock(7, 47, 8, Block::LEAVES, 15);
            $chunk->setBlock(6, 47, 7, Block::LEAVES, 15);
            $chunk->setBlock(8, 47, 7, Block::LEAVES, 15);
            $chunk->setBlock(9, 47, 7, Block::LEAVES, 15);
            $chunk->setBlock(8, 47, 8, Block::LEAVES, 15);
            $chunk->setBlock(7, 47, 9, Block::LEAVES, 15);
            $chunk->setBlock(8, 47, 9, Block::LEAVES, 15);
            $chunk->setBlock(9, 47, 8, Block::LEAVES, 15);
            $chunk->setBlock(10, 47, 8, Block::LEAVES, 15);
            $chunk->setBlock(9, 47, 9, Block::LEAVES, 15);
            $chunk->setBlock(10, 47, 9, Block::LEAVES, 15);
            $chunk->setBlock(10, 47, 10, Block::LEAVES, 15);
            $chunk->setBlock(6, 47, 10, Block::LEAVES, 15);
            $chunk->setBlock(7, 47, 10, Block::LEAVES, 15);
            $chunk->setBlock(8, 47, 10, Block::LEAVES, 15);
            $chunk->setBlock(11, 47, 10, Block::LEAVES, 15);
            $chunk->setBlock(11, 47, 11, Block::LEAVES, 15);
            $chunk->setBlock(12, 46, 4, Block::LEAVES, 15);
            $chunk->setBlock(12, 46, 4, Block::LEAVES, 15);
            $chunk->setBlock(11, 46, 4, Block::LEAVES, 15);
            $chunk->setBlock(11, 46, 5, Block::LEAVES, 15);
            $chunk->setBlock(8, 46, 3, Block::LEAVES, 15);
            $chunk->setBlock(8, 46, 4, Block::LEAVES, 15);
            $chunk->setBlock(8, 46, 5, Block::LEAVES, 15);
            $chunk->setBlock(8, 46, 6, Block::LEAVES, 15);
            $chunk->setBlock(4, 46, 4, Block::LEAVES, 15);
            $chunk->setBlock(4, 46, 5, Block::LEAVES, 15);
            $chunk->setBlock(5, 46, 5, Block::LEAVES, 15);
            $chunk->setBlock(4, 46, 8, Block::LEAVES, 15);
            $chunk->setBlock(5, 46, 8, Block::LEAVES, 15);
            $chunk->setBlock(6, 46, 8, Block::LEAVES, 15);
            $chunk->setBlock(5, 46, 11, Block::LEAVES, 15);
            $chunk->setBlock(5, 46, 10, Block::LEAVES, 15);
            $chunk->setBlock(6, 46, 10, Block::LEAVES, 15);
            $chunk->setBlock(8, 46, 12, Block::LEAVES, 15);
            $chunk->setBlock(8, 46, 11, Block::LEAVES, 15);
            $chunk->setBlock(8, 46, 10, Block::LEAVES, 15);
            $chunk->setBlock(12, 46, 12, Block::LEAVES, 15);
            $chunk->setBlock(12, 46, 4, Block::LEAVES, 15);
            $chunk->setBlock(12, 46, 11, Block::LEAVES, 15);
            $chunk->setBlock(11, 46, 11, Block::LEAVES, 15);
            $chunk->setBlock(12, 46, 12, Block::LEAVES, 15);
            $chunk->setBlock(12, 46, 8, Block::LEAVES, 15);
            $chunk->setBlock(11, 46, 8, Block::LEAVES, 15);
            $chunk->setBlock(10, 46, 8, Block::LEAVES, 15);
            $chunk->setBlock(8, 46, 8, Block::LOG, 15);
            $chunk->setBlock(12, 45, 4, Block::LEAVES, 15);
            $chunk->setBlock(8, 45, 2, Block::LEAVES, 15);
            $chunk->setBlock(8, 45, 3, Block::LEAVES, 15);
            $chunk->setBlock(4, 45, 4, Block::LEAVES, 15);
            $chunk->setBlock(3, 45, 8, Block::LEAVES, 15);
            $chunk->setBlock(4, 45, 8, Block::LEAVES, 15);
            $chunk->setBlock(5, 45, 11, Block::LEAVES, 15);
            $chunk->setBlock(8, 45, 12, Block::LEAVES, 15);
            $chunk->setBlock(12, 45, 12, Block::LEAVES, 15);
            $chunk->setBlock(13, 45, 8, Block::LEAVES, 15);
            $chunk->setBlock(13, 45, 8, Block::LEAVES, 15);
            $chunk->setBlock(12, 45, 8, Block::LEAVES, 15);
            $chunk->setBlock(7, 45, 8, Block::LOG, 15);
            $chunk->setBlock(8, 44, 2, Block::LEAVES, 15);
            $chunk->setBlock(3, 44, 8, Block::LEAVES, 15);
            $chunk->setBlock(8, 44, 12, Block::LEAVES, 15);
            $chunk->setBlock(7, 44, 7, Block::LOG, 15);
            $chunk->setBlock(6, 43, 7, Block::LOG, 15);
            $chunk->setBlock(6, 42, 7, Block::LOG, 15);
            $chunk->setBlock(6, 41, 8, Block::LOG, 15);
            $chunk->setBlock(6, 40, 8, Block::LOG, 15);
            $chunk->setX($chunkX);
            $chunk->setZ($chunkZ);
            $this->level->setChunk($chunkX, $chunkZ, $chunk);
        }
    }

    public function populateChunk(int $chunkX, int $chunkZ): void {
    }

    public static function getWorldSpawn(): Vector3 {
        return new Vector3(9, 40, 11);
    }

    public static function getChestPosition(): Vector3 {
        return new Vector3(7, 40, 8);
    }

    public function getSpawn(): Vector3 {
        return new Vector3(9, 40, 11);
    }

}