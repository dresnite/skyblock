<?php
/*
 * Copyright (C) Andrés Arias - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

namespace SkyBlock\provider\json;


use pocketmine\utils\Config;
use SkyBlock\isle\Isle;
use SkyBlock\provider\Provider;
use SkyBlock\session\iSession;
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
     * @param string $username
     * @return Config
     */
    private function getUserConfig(string $username): Config {
        return new Config($this->plugin->getDataFolder() . "users/$username.json", Config::JSON, [
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
     * @param iSession $session
     */
    public function openSession(iSession $session): void {
        $config = $this->getUserConfig($session->getUsername());
        $session->setIsleId($config->get("isle"));
        $session->setRank($config->get("rank"));
    }
    
    /**
     * @param iSession $session
     */
    public function saveSession(iSession $session): void {
        $config = $this->getUserConfig($session->getUsername());
        $config->set("isle", $session->getIsleId());
        $config->set("rank", $session->getRank());
        $config->save();
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
        $spawn = SkyBlockUtils::parsePosition($config->get("spawn"));
        
        $members = [];
        foreach($config->get("members", []) as $username) {
            $members[] = $this->plugin->getSessionManager()->getOfflineSession($username);
        }
        $this->plugin->getIsleManager()->openIsle($identifier, $members, $locked, $type, $level, $spawn);
    }
    
    /**
     * @param Isle $isle
     */
    public function saveIsle(Isle $isle): void {
        $config = $this->getIsleConfig($isle->getIdentifier());
        $config->set("identifier", $isle->getIdentifier());
        $config->set("locked", $isle->isLocked());
        $config->set("type", $isle->getType());
        $config->set("spawn", SkyBlockUtils::createPositionString($isle->getSpawn()));
        
        $members = [];
        foreach($isle->getMembers() as $member) {
            $members[] = $member->getUsername();
        }
        $config->set("members", $members);
        
        $config->save();
    }
    
}