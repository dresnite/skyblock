<?php

namespace SkyBlock\skyblock;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\generator\Generator;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\tile\Chest;
use pocketmine\tile\Tile;
use pocketmine\utils\Config;
use SkyBlock\SkyBlock;

class SkyBlockManager {

    /** @var SkyBlock */
    private $plugin;

    /**
     * SkyBlockManager constructor.
     *
     * @param SkyBlock $plugin
     */
    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
    }

    public function generateIsland(Player $player, $generatorName = "basic") {
        $this->plugin->getIslandManager()->createIsland($player, $generatorName);
        $server = $this->plugin->getServer();
        $island = $this->getPlayerConfig($player)->get("island");
        $server->generateLevel($island, null, Generator::getGenerator($generatorName));
        $server->loadLevel($island);
        $this->spawnDefaultChest($island);
        $level = $server->getLevelByName($island);
        $level->setSpawnLocation(new Vector3(7,64,9));
    }

    public function spawnDefaultChest($islandName) {
    	$chestX = 7;
    	$chestY = 64;
    	$chestZ = 6;
    	$chestVector = new Vector3($chestX, $chestY, $chestZ);
        $level = $this->plugin->getServer()->getLevelByName($islandName);
        $level->setBlock($chestVector, new Block(0, 0));
        $level->loadChunk(10, 4, true);
        /** @var Chest $chest */
		$nbt = Chest::createNBT($chestVector);
        $chest = Tile::createTile(Tile::CHEST, $level, $nbt);
        $inventory = $chest->getInventory();
        //TODO: Use a kit config for user-friendliness.
        $inventory->addItem(Item::get(Item::BUCKET, 10, 1));
        $inventory->addItem(Item::get(Item::ICE, 0, 2));
        $inventory->addItem(Item::get(Item::MELON_BLOCK, 0, 1));
        $inventory->addItem(Item::get(Item::BONE, 0, 1));
        $inventory->addItem(Item::get(Item::PUMPKIN_SEEDS, 0, 1));
        //$inventory->addItem(Item::get(Item::CACTUS, 0, 1));
        $inventory->addItem(Item::get(Item::SUGARCANE_BLOCK, 0, 1));
        $inventory->addItem(Item::get(Item::BREAD, 0, 1));
        $inventory->addItem(Item::get(Item::WHEAT, 0, 1));
		$level->setBlock($chestVector, new Block(54, 3));
    }

    /**
     * Return player data
     *
     * @param Player $player
     * @return string
     */
    public function getPlayerDataPath(Player $player) {
        return $this->plugin->getDataFolder() . "users" . DIRECTORY_SEPARATOR . strtolower($player->getName()) . ".json";
    }

    /**
     * Register a user
     *
     * @param Player $player
     */
    public function registerUser(Player $player) {
        new Config($this->getPlayerDataPath($player), Config::JSON, [
            "island" => ""
        ]);
    }

    /**
     * Tries to register a player
     *
     * @param Player $player
     */
    public function tryRegisterUser(Player $player) {
        if(!is_file($this->getPlayerDataPath($player))) {
            $this->registerUser($player);
        }
    }

    /**
     * Return player config
     *
     * @param Player $player
     * @return Config
     */
    public function getPlayerConfig(Player $player) {
        return new Config($this->getPlayerDataPath($player), Config::JSON);
    }

}