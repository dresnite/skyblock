<?php
namespace SkyBlock\island;

use pocketmine\Player;
use pocketmine\utils\Config;
use SkyBlock\Main;
use SkyBlock\Utils;

class IslandManager {

    /** @var Main */
    private $plugin;

    /** @var Island[] */
    private $islands = [];

    /**
     * IslandManager constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * Return if a island is online
     *
     * @param $id
     * @return bool
     */
    public function isOnlineIsland($id) {
        return isset($this->islands[$id]);
    }

    /**
     * Return all online islands
     *
     * @return Island[]
     */
    public function getOnlineIslands() {
        return $this->islands;
    }

    /**
     * Return an online island
     *
     * @param $id
     * @return Island
     */
    public function getOnlineIsland($id) {
        return isset($this->islands[$id]) ? $this->islands[$id] : null;
    }

    /**
     * Return an island by his owner name
     *
     * @param $ownerName
     * @return null|Island
     */
    public function getIslandByOwner($ownerName) {
        foreach($this->islands as $island) {
            if($island->getOwnerName() == $ownerName) {
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
        $this->islands[$id] = new Island($config, $ownerName, $id, $members, $locked, $home, $generator);
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
        if(isset($this->islands[$id])) {
            unset($this->islands[$id]);
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
                $config = new Config($path, Config::JSON);
                $this->addIsland($config, $config->get("owner"), $id, $config->get("members"), $config->get("locked"), $config->get("home"), $config->get("generator"));
                $server = $this->plugin->getServer();
                if(!$server->isLevelLoaded($id)) {
                    $server->loadLevel($id);
                }
            }
        }
    }

    public function removeIsland(Island $island) {
        if(in_array($island, $this->islands)) {
            unset($this->islands[$island->getIdentifier()]);
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
        foreach($this->islands as $island) {
            $island->update();
        }
    }
}