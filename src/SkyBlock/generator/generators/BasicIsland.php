<?php

namespace SkyBlock\generator\generators;

use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\Generator;
use pocketmine\level\generator\object\Tree;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class BasicIsland extends Generator {

    /** @var array */
    private $settings;

    /** @var string */
    private $name;

    /** @var ChunkManager */
    protected $level;

    /** @var Random */
    protected $random;

    /**
     * BasicIsland constructor.
     *
     * @param array $settings
     */
    public function __construct(array $settings = []) {
        $this->settings = $settings;
    }

    /**
     * Initialize BasicIsland
     *
     * @param ChunkManager $level
     * @param Random $random
     */
	public function init(ChunkManager $level, Random $random) : void{
        $this->level = $level;
        $this->random = $random;
        $this->name = "Basic";
    }

    /**
     * Return generator name
     *
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }
    
    /**
     * @return array
     */
    public function getSettings(): array {
        return $this->settings;
    }
    
    /**
     * @param int $chunkX
     * @param int $chunkZ
     */
    public function generateChunk(int $chunkX, int $chunkZ) : void {
        $chunk = $this->level->getChunk($chunkX, $chunkZ);
        $chunk->setGenerated();
        //SkyBlock Island
        if ($chunkX == 0 && $chunkZ == 0) {
            for ($x = 6; $x < 12; $x++) {
                for ($z = 6; $z < 12; $z++) {
					$chunk->setBlock($x, 61, $z, Block::DIRT);
                    $chunk->setBlock($x, 62, $z, Block::DIRT);
                    $chunk->setBlock($x, 63, $z, Block::GRASS);
                }
            }
            for($airX = 9; $airX < 12; $airX++){
            	for($airZ = 9; $airZ < 12; $airZ++) {
					$chunk->setBlock($airX, 61, $airZ, Block::AIR);
					$chunk->setBlock($airX, 62, $airZ, Block::AIR);
					$chunk->setBlock($airX, 63, $airZ, Block::AIR);
				}
			}
			Tree::growTree($this->level, $chunkX * 16 + 11 , 64, $chunkZ * 16 + 6, $this->random, 0);
            $chunk->setX($chunkX);
            $chunk->setZ($chunkZ);
            $this->level->setChunk($chunkX, $chunkZ, $chunk);
        }

        // Sand Island
		if($chunkX == 4 and $chunkZ == 0) {
        	for($x = 6; $x < 11; $x++){
        		for($z = 6; $z < 11; $z++) {
        			for($y = 60; $y < 65; $y++){
						$chunk->setBlock($x, $y, $z, Block::SAND);
					}
				}
			}
			$chunk->setBlock(8, 65, 8, BlockIds::CACTUS);
		}
    }
    
    /**
     * @param int $chunkX
     * @param int $chunkZ
     */
    public function populateChunk(int $chunkX, int $chunkZ) : void {
        return;
    }

    /**
     * Return BasicIsland spawn
     *
     * @return Vector3
     */
    public function getSpawn(): Vector3{
        return new Vector3(7, 66, 7);
    }

}