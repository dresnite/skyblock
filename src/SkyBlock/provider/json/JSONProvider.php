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

namespace SkyBlock\provider\json;


use pocketmine\utils\Config;
use SkyBlock\isle\Isle;
use SkyBlock\provider\Provider;
use SkyBlock\session\iSession;
use SkyBlock\session\Session;
use SkyBlock\SkyBlock;

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
                "isle" => null,
                "rank" => Session::RANK_DEFAULT
            ]);
    }
    
    /**
     * @param string $isleId
     * @return Config
     */
    private function getIsleConfig(string $isleId): Config {
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
        $spawn = SkyBlock::parsePosition($config->get("spawn"));
        
        $members = [];
        foreach($config->get("members", []) as $username) {
            $members[$username] = $this->plugin->getSessionManager()->getOfflineSession($username);
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
        $config->set("spawn", SkyBlock::writePosition($isle->getSpawn()));
        
        $members = [];
        foreach($isle->getMembers() as $member) {
            $members[] = $member->getUsername();
        }
        $config->set("members", $members);
        
        $config->save();
    }
    
}