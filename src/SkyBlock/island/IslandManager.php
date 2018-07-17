<?php
namespace SkyBlock\island;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use SkyBlock\SkyBlock;
use SkyBlock\Utils;

class IslandManager {

    /** @var SkyBlock */
    private $plugin;

    /** @var Island[] */
    private $onlineIslands = [];

    /**
     * IslandManager constructor.
     *
     * @param SkyBlock $plugin
     */
    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * Return if a island is online
     *
     * @param $id
     * @return bool
     */
    public function isOnlineIsland($id) {
        return isset($this->onlineIslands[$id]);
    }

    /**
     * Return all online islands
     *
     * @return Island[]
     */
    public function getOnlineIslands() {
        return $this->onlineIslands;
    }

    /**
     * Return an online island
     *
     * @param $id
     * @return Island
     */
    public function getOnlineIsland($id) {
        return isset($this->onlineIslands[$id]) ? $this->onlineIslands[$id] : null;
    }

    /**
     * Return an island by his owner name
     *
     * @param $ownerName
     * @return null|Island
     */
    public function getIslandByOwner($ownerName) {
        foreach($this->onlineIslands as $island) {
        	$islandOwner = $island->getOwnerName();
            if(strtolower($islandOwner) === strtolower($ownerName)) {
                return $island;
            }
        }
        return null;
    }

    /**
     * Add a new island
     *
     * @param Config $config
     * @param $ownerName
     * @param $id
     * @param $members
     * @param $locked
     * @param $home
     * @param $generator
     */
    public function addIsland(Config $config, $ownerName, $id, $members, $locked, $home, $generator) {
        $this->onlineIslands[$id] = new Island($config, $ownerName, $id, $members, $locked, $home, $generator);
    }

    /**
     * Create a new island
     *
     * @param Player $owner
     * @param string $generator
     */
    public function createIsland(Player $owner, $generator) {
        $id = Utils::genIslandId();
        $name = strtolower($owner->getName());
        $config = new Config(Utils::getIslandPath($id), Config::JSON, [
            "owner" => $name,
            "members" => [],
            "locked" => false,
            "home" => "",
            "generator" => $generator
        ]);
        $this->addIsland($config, $name, $id, [], false, "", $generator);
        $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($owner);
        $config->set("island", $id);
        $config->save();
    }

    /**
     * Set a island offline
     *
     * @param $id
     */
    public function setIslandOffline($id) {
        if(isset($this->onlineIslands[$id])) {
            unset($this->onlineIslands[$id]);
        }
    }

    /**
     * Check island by player
     *
     * @param Player $player
     */
    public function checkPlayerIsland(Player $player) {
        $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($player);
        if(!empty($config->get("island"))) {
            $path = Utils::getIslandPath($id = $config->get("island"));
            if(is_file($path)) {
            	if(!$this->isOnlineIsland($id)){
					$config = new Config($path, Config::JSON);
					$this->addIsland($config, $config->get("owner"), $id, $config->get("members"), $config->get("locked"), $config->get("home"), $config->get("generator"));
					if(!Server::getInstance()->isLevelLoaded($id)){
						Server::getInstance()->loadLevel($id);
					}
				}
            }
        }
    }

    public function removeIsland(Island $island) {
        if(in_array($island, $this->onlineIslands)) {
            unset($this->onlineIslands[$island->getIdentifier()]);
        }
        $server = $this->plugin->getServer();
        if($server->isLevelLoaded($island->getIdentifier())) {
            $level = $server->getLevelByName($island->getIdentifier());
            foreach($level->getPlayers() as $player) {
                $player->teleport($server->getDefaultLevel()->getSafeSpawn());
            }
            foreach($level->getEntities() as $entity) {
                $entity->kill();
            }
            $server->unloadLevel($level);
			$worldDir = $server->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . $level->getFolderName() . DIRECTORY_SEPARATOR;
			$config = $this->plugin->getDataFolder() . "islands" . DIRECTORY_SEPARATOR . $level->getName() . ".json";

			$files = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator($worldDir, \RecursiveDirectoryIterator::SKIP_DOTS),
				\RecursiveIteratorIterator::CHILD_FIRST
			);

			foreach ($files as $fileinfo) {
				$todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
				$todo($fileinfo->getRealPath());
			}

			rmdir($worldDir);
			unlink($config);
        }
    }

    /**
     * Try to unload a island if there isn't online players
     *
     * @param Player $player
     */
    public function unloadByPlayer(Player $player) {
        $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($player);
        if(!empty($config->get("island"))) {
            $id = $config->get("island");
            if($this->isOnlineIsland($id)) {
                $island = $this->getOnlineIsland($id);
                $online = false;
                foreach($island->getAllMembers() as $member) {
                    if($member != strtolower($player->getName())) {
                        $user = $this->plugin->getServer()->getPlayerExact($member);
                        if($user instanceof Player and $user->isOnline()) {
                            $online = true;
                            break;
                        }
                    }
                }
                if(!$online) {
                    $level = $this->plugin->getServer()->getLevelByName($id);
                    $island->update();
                    $this->setIslandOffline($id);
                    $this->plugin->getServer()->unloadLevel($level);
                }
            }
        }
    }

    public function update() {
        foreach($this->onlineIslands as $island) {
            $island->update();
        }
    }
}