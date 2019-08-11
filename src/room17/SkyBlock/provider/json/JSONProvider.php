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

namespace room17\SkyBlock\provider\json;


use pocketmine\utils\Config;
use room17\SkyBlock\isle\Isle;
use room17\SkyBlock\provider\Provider;
use room17\SkyBlock\session\BaseSession;
use room17\SkyBlock\session\Session;

class JSONProvider extends Provider {
    
    public function initialize(): void {
        $dataFolder = $this->plugin->getDataFolder();
        if(!is_dir($dataFolder . "isles")) {
            mkdir($dataFolder . "isles");
        }
        if(!is_dir($dataFolder . "users")) {
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
     * @param BaseSession $session
     */
    public function loadSession(BaseSession $session): void {
        $config = $this->getUserConfig($session->getUsername());
        $session->setIsleId($config->get("isle", null) ?? null);
        $session->setRank($config->get("rank", null) ?? Session::RANK_DEFAULT);
        $session->setLastIslandCreationTime($config->get("lastIsle", null) ?? null);
    }
    
    /**
     * @param BaseSession $session
     */
    public function saveSession(BaseSession $session): void {
        $config = $this->getUserConfig($session->getUsername());
        $config->set("isle", $session->getIsleId());
        $config->set("rank", $session->getRank());
        $config->set("lastIsle", $session->getLastIslandCreationTime());
        $config->save();
    }

    /**
     * @param string $identifier
     * @throws \ReflectionException
     */
    public function loadIsle(string $identifier): void {
        if($this->plugin->getIsleManager()->getIsle($identifier) != null) {
            return;
        }
        $config = $this->getIsleConfig($identifier);
        $server = $this->plugin->getServer();
        if(!$server->isLevelLoaded($identifier)) {
            $server->loadLevel($identifier);
        }
        
        $members = [];
        foreach($config->get("members", []) as $username) {
            $members[] = $this->plugin->getSessionManager()->getOfflineSession($username);
        }
        
        $this->plugin->getIsleManager()->openIsle(
            $identifier,
            $members,
            $config->get("locked"),
            $config->get("type", null) ?? "basic",
            $server->getLevelByName($identifier),
            $config->get("blocks") ?? 0
        );
    }
    
    /**
     * @param Isle $isle
     */
    public function saveIsle(Isle $isle): void {
        $config = $this->getIsleConfig($isle->getIdentifier());
        $config->set("identifier", $isle->getIdentifier());
        $config->set("locked", $isle->isLocked());
        $config->set("type", $isle->getType());
        $config->set("blocks", $isle->getBlocksBuilt());
        
        $members = [];
        foreach($isle->getMembers() as $member) {
            $members[] = $member->getUsername();
        }
        $config->set("members", $members);
        
        $config->save();
    }
    
}