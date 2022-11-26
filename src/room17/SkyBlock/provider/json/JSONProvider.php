<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace room17\SkyBlock\provider\json;


use room17\SkyBlock\island\RankIds;
use pocketmine\utils\Config;
use room17\SkyBlock\island\Island;
use room17\SkyBlock\island\IslandFactory;
use room17\SkyBlock\provider\Provider;
use room17\SkyBlock\session\BaseSession;
use room17\SkyBlock\session\SessionLocator;

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

    public function loadSession(BaseSession $session): void {
        $config = $this->getUserConfig($session->getLowerCaseName());
        $session->setIslandId($config->get("isle", null) ?? null);
        $session->setRank($config->get("rank", null) ?? RankIds::MEMBER);
        $session->setLastIslandCreationTime($config->get("lastIsle", null) ?? null);
    }

    public function saveSession(BaseSession $session): void {
        $config = $this->getUserConfig($session->getLowerCaseName());
        $config->set("isle", $session->getIslandId());
        $config->set("rank", $session->getRank());
        $config->set("lastIsle", $session->getLastIslandCreationTime());
        $config->save();
    }

    public function loadIsland(string $identifier): void {
        $islandManager = $this->plugin->getIslandManager();
        if($islandManager->getIsland($identifier) != null) {
            return;
        }

        $config = $this->getIslandConfig($identifier);
        $worldManager = $this->plugin->getServer()->getWorldManager();
        $worldManager->loadWorld($identifier, true);

        $members = [];
        foreach($config->get("members", []) as $username) {
            $members[] = SessionLocator::getOfflineSession($username);
        }

        if(!$worldManager->isWorldGenerated($identifier)) {
            IslandFactory::createIslandWorld($identifier, "Basic");
            $this->plugin->getLogger()->warning("Couldn't find island $identifier world - One has been created");
        }

        $islandManager->openIsland($identifier, $members, $config->get("locked"), $config->get("type") ?? "basic",
            $worldManager->getWorldByName($identifier), $config->get("blocks") ?? 0
        );
    }

    public function saveIsland(Island $island): void {
        $config = $this->getIslandConfig($island->getIdentifier());
        $config->set("identifier", $island->getIdentifier());
        $config->set("locked", $island->isLocked());
        $config->set("type", $island->getType());
        $config->set("blocks", $island->getBlocksBuilt());

        $members = [];
        foreach($island->getMembers() as $member) {
            $members[] = $member->getLowerCaseName();
        }
        $config->set("members", $members);

        $config->save();
    }

    private function getUserConfig(string $username): Config {
        return new Config($this->plugin->getDataFolder() . "users/$username.json", Config::JSON, [
            "isle" => null,
            "rank" => RankIds::MEMBER
        ]);
    }

    private function getIslandConfig(string $islandId): Config {
        return new Config($this->plugin->getDataFolder() . "isles/$islandId.json", Config::JSON);
    }

}