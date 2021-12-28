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
use pocketmine\world\generator\object\OakTree;
use room17\SkyBlock\island\generator\IslandGenerator;

class OPIsland extends IslandGenerator {

    public function getName(): string {
        return "OP";
    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {
        $chunk = $world->getChunk($chunkX, $chunkZ);
        if($chunkX == 0 && $chunkZ == 0) {
            for($x = 0; $x < 16; $x++) {
                for($z = 0; $z < 16; $z++) {
                    $chunk->setFullBlock($x, 0, $z, VanillaBlocks::BEDROCK()->getId());
                    for($y = 1; $y <= 3; $y++) {
                        $chunk->setFullBlock($x, $y, $z, VanillaBlocks::STONE()->getId());
                    }
                    $chunk->setFullBlock($x, 4, $z, VanillaBlocks::DIRT()->getId());
                    $chunk->setFullBlock($x, 5, $z, VanillaBlocks::GRASS()->getId());
                }
            }
            $tree = new OakTree();
            $transaction = $tree->getBlockTransaction($world, 8, 6, 8, $this->random);
            $transaction->apply();

            $chunk->setFullBlock(10, 6, 8, VanillaBlocks::CHEST()->getId());
        }
        $world->setChunk($chunkX, $chunkZ, $chunk);
    }

    public static function getWorldSpawn(): Vector3 {
        return new Vector3(8, 7, 10);
    }

    public static function getChestPosition(): Vector3 {
        return new Vector3(10, 6, 8);
    }

}