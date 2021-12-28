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
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\object\OakTree;
use pocketmine\math\Vector3;
use room17\SkyBlock\island\generator\IslandGenerator;

class BasicIsland extends IslandGenerator {

    public function getName(): string {
        return "Basic";
    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {
        $chunk = $world->getChunk($chunkX, $chunkZ);
        if($chunkX == 0 && $chunkZ == 0) {
            for($x = 6; $x < 12; $x++) {
                for($z = 6; $z < 12; $z++) {
                    $chunk->setFullBlock($x, 61, $z, VanillaBlocks::DIRT()->getId());
                    $chunk->setFullBlock($x, 62, $z, VanillaBlocks::DIRT()->getId());
                    $chunk->setFullBlock($x, 63, $z, VanillaBlocks::GRASS()->getId());
                }
            }
            for($airX = 9; $airX < 12; $airX++) {
                for($airZ = 9; $airZ < 12; $airZ++) {
                    $chunk->setFullBlock($airX, 61, $airZ, VanillaBlocks::AIR()->getId());
                    $chunk->setFullBlock($airX, 62, $airZ, VanillaBlocks::AIR()->getId());
                    $chunk->setFullBlock($airX, 63, $airZ, VanillaBlocks::AIR()->getId());
                }
            }
            $tree = new OakTree();
            $transaction = $tree->getBlockTransaction($world, 11, 64, 6, $this->random);
            $transaction->apply();

            $chunk->setFullBlock(8, 64, 7, VanillaBlocks::CHEST()->getId());
            $world->setChunk($chunkX, $chunkZ, $chunk);
        }
        if($chunkX == 4 and $chunkZ == 0) {
            for($x = 6; $x < 11; $x++) {
                for($z = 6; $z < 11; $z++) {
                    for($y = 60; $y < 65; $y++) {
                        $chunk->setFullBlock($x, $y, $z, VanillaBlocks::SAND()->getId());
                    }
                }
            }
            $chunk->setFullBlock(8, 65, 8, VanillaBlocks::CACTUS()->getId());
        }
    }

    public static function getWorldSpawn(): Vector3 {
        return new Vector3(7, 66, 7);
    }

    public static function getChestPosition(): Vector3 {
        return new Vector3(8, 64, 7);
    }

}
