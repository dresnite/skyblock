<?php
/*
 * Copyright (C) Andrés Arias - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

namespace SkyBlock\provider\json;


use pocketmine\Player;
use pocketmine\utils\Config;
use SkyBlock\provider\Provider;
use SkyBlock\session\Session;
use SkyBlock\SkyBlockUtils;

class JSONProvider extends Provider {
    
    public function initialize(): void {
        $dataFolder = $this->plugin->getDataFolder();
        if(!is_dir($dataFolder)) {
            mkdir($dataFolder);
        } else if(!is_dir($dataFolder . "isles")) {
            mkdir($dataFolder . "isles");
        } else if(!is_dir($dataFolder . "users")) {
            mkdir($dataFolder . "users");
        }
    }
    
    /**
     * @param Player $player
     * @return Config
     */
    private function getUserConfig(Player $player): Config {
        return new Config($this->plugin->getDataFolder() . "users/{$player->getLowerCaseName()}.json", Config::JSON, [
                "isle" => null
            ]);
    }
    
    /**
     * @param string $isleId
     * @return Config
     */
    private function getIsleConfig(string $isleId) {
        return new Config($this->plugin->getDataFolder() . "isles/$isleId.json", Config::JSON);
    }
    
    /**
     * @param Session $session
     */
    public function openSession(Session $session): void {
        $config = $this->getUserConfig($session->getPlayer());
        $isleId = $config->get("isle");
        if($isleId != null) {
            $this->checkIsle($isleId);
            $session->setIsle($this->plugin->getIsleManager()->getIsle($isleId));
        }
    }
    
    /**
     * @param Session $session
     */
    public function saveSession(Session $session): void {
        //
    }
    
    /**
     * @param string $identifier
     */
    public function checkIsle(string $identifier): void {
        $config = $this->getIsleConfig($identifier);
        $server = $this->plugin->getServer();
        if(!$server->isLevelLoaded($identifier)) {
            $server->loadLevel($identifier);
        }
        
        $level = $server->getLevelByName($identifier);
        $locked = $config->get("locked");
        $type = $config->get("type");
        $position = SkyBlockUtils::parsePosition($config->get("position"));
        
        $members = [];
        // parse members puta
    }
    
}