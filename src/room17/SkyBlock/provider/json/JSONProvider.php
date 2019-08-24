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
use room17\SkyBlock\island\Island;
use room17\SkyBlock\provider\Provider;
use room17\SkyBlock\session\BaseSession;
use room17\SkyBlock\session\Session;
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

    /**
     * @param BaseSession $session
     */
    public function loadSession(BaseSession $session): void {
        $config = $this->getUserConfig($session->getLowerCaseName());
        $session->setIslandId($config->get("isle", null) ?? null);
        $session->setRank($config->get("rank", null) ?? Session::RANK_DEFAULT);
        $session->setLastIslandCreationTime($config->get("lastIsle", null) ?? null);
    }

    /**
     * @param BaseSession $session
     */
    public function saveSession(BaseSession $session): void {
        $config = $this->getUserConfig($session->getLowerCaseName());
        $config->set("isle", $session->getIslandId());
        $config->set("rank", $session->getRank());
        $config->set("lastIsle", $session->getLastIslandCreationTime());
        $config->save();
    }

    /**
     * @param string $identifier
     * @throws \ReflectionException
     */
    public function loadIsland(string $identifier): void {
        $islandManager = $this->plugin->getIslandManager();
        if($islandManager->getIsland($identifier) != null) {
            return;
        }

        $config = $this->getIslandConfig($identifier);
        $server = $this->plugin->getServer();
        $server->loadLevel($identifier);

        $members = [];
        foreach($config->get("members", []) as $username) {
            $members[] = SessionLocator::getOfflineSession($username);
        }

        $islandManager->openIsland($identifier, $members, $config->get("locked"), $config->get("type") ?? "basic",
            $server->getLevelByName($identifier), $config->get("blocks") ?? 0
        );
    }

    /**
     * @param Island $island
     */
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
     * @param string $islandId
     * @return Config
     */
    private function getIslandConfig(string $islandId): Config {
        return new Config($this->plugin->getDataFolder() . "isles/$islandId.json", Config::JSON);
    }

}