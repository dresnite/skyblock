<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace room17\SkyBlock\provider\sqlite;


use room17\SkyBlock\island\DbType;
use room17\SkyBlock\island\Island;
use room17\SkyBlock\island\IslandCustomProperty;
use room17\SkyBlock\island\IslandCustomValue;
use room17\SkyBlock\island\IslandFactory;
use room17\SkyBlock\island\RankIds;
use room17\SkyBlock\provider\Provider;
use room17\SkyBlock\provider\Validable;
use room17\SkyBlock\session\BaseSession;
use room17\SkyBlock\session\SessionLocator;
use room17\SkyBlock\SkyBlock;

class SQLiteProvider extends Provider implements Validable {

    /** @var \SQLite3 */
    protected $db;

    public static function validate(): bool {
        if(extension_loaded("sqlite3")) {
            return true;
        }

        $plugin = SkyBlock::getInstance();
        $plugin->getLogger()->error("SkyBlock requires the SQLite3 extension to use a SQLite database. Please, install it or update the provider setting to 'json'");
        $plugin->getServer()->getPluginManager()->disablePlugin($plugin);

        return false;
    }

    public function initialize(): void {
        $this->plugin->saveResource("skyblock.db");
        $this->db = new \SQLite3($this->plugin->getDataFolder() . "skyblock.db");
        $this->createTables();
    }

    public function createTables(): void {
        $generated = [];

        /** @var IslandCustomProperty $property */
        foreach(IslandFactory::getInstance()->getProperties() as $property){
            $value = "";
            $value .= $property->getName();
            $value .= " ";

            switch($property->getDbType()){
                case DbType::TYPE_TEXT:
                    $value .= "TEXT";
                    break;
                case DbType::TYPE_INTEGER:
                    $value .= "INTEGER";
                    break;
                case DbType::TYPE_BOOLEAN:
                    $value .= "BOOLEAN";
                    break;
                default:
                    throw new \InvalidArgumentException("Not recognized data type {$property->getDbType()}");
            }

            $generated[] = $value;
        }

        $generated = implode(",", $generated);

        $this->db->query("CREATE TABLE IF NOT EXISTS islands (
            identifier TEXT PRIMARY KEY NOT NULL,
            locked BOOLEAN,
            islandType TEXT,
            members TEXT,
            blocks INTEGER,
            $generated
        )");


        $this->db->query("CREATE TABLE IF NOT EXISTS users (
            username TEXT PRIMARY KEY NOT NULL,
            island TEXT,
            rank INTEGER,
            lastIsland TEXT
        )");
    }

    public function loadSession(BaseSession $session): void {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindValue(":username", $session->getLowerCaseName());

        $res = $stmt->execute();
        $info = $res->fetchArray(SQLITE3_ASSOC);

        if(!is_array($info)) {
            $info = [];
        }

        $session->setIslandId($info["island"] ?? null);
        $session->setRank($info["rank"] ?? RankIds::MEMBER);
        $session->setLastIslandCreationTime((array_key_exists("lastIsland", $info) and strlen($info["lastIsland"] ?? "") > 0) ? (float) $info["lastIsland"] : null);
    }

    public function saveSession(BaseSession $session): void {
        $stmt = $this->db->prepare("INSERT OR REPLACE INTO users (username, island, rank, lastIsland) VALUES (:username, :island, :rank, :lastIsland)");
        $stmt->bindValue(":username", $session->getLowerCaseName());
        $stmt->bindValue(":island", $session->getIslandId());
        $stmt->bindValue(":rank", $session->getRank());
        $stmt->bindValue(":lastIsland", ($session->getLastIslandCreationTime() !== null) ? (string) $session->getLastIslandCreationTime() : null);
        $stmt->execute();
    }

    public function loadIsland(string $identifier): void {
        $stmt = $this->db->prepare("SELECT * FROM islands WHERE identifier = :identifier");
        $stmt->bindValue(":identifier", $identifier);

        $res = $stmt->execute();
        $info = $res->fetchArray(SQLITE3_ASSOC);

        $islandManager = $this->plugin->getIslandManager();
        if($islandManager->getIsland($identifier) != null) {
            return;
        }

        $server = $this->plugin->getServer();
        $server->loadLevel($identifier);

        $members = [];
        foreach((array_key_exists("members", $info) ? explode(",", $info["members"]) : []) as $username) {
            $members[] = SessionLocator::getOfflineSession($username);
        }

        if(!$server->isLevelGenerated($identifier)) {
            IslandFactory::getInstance()->createIslandWorld($identifier, "Basic");
            $this->plugin->getLogger()->warning("Couldn't find island $identifier world - One has been created");
        }

        $customValues = [];
        /** @var IslandCustomProperty $property */
        foreach(IslandFactory::getInstance()->getProperties() as $property){
            $name = $property->getName();
            $customValues[] = new IslandCustomValue($name, $info[$name], $property->getDbType());
        }

        $islandManager->openIsland($identifier, $members, boolval($info["locked"] ?? false), $info["islandType"] ?? "basic",
            $server->getLevelByName($identifier), $info["blocks"] ?? 0, $customValues
        );
    }

    public function saveIsland(Island $island): void {
        $properties = [];
        $values = [];

        $customValues = $island->getCustomValues();
        foreach($customValues as $index => $customValue) {
            if($customValue->hasDbType()) {
                $properties[] = $customValue->getIdentifier();
                $values[] = ":{$customValue->getIdentifier()}";
            } else {
                unset($customValues[$index]);
            }
        }

        $propertiesQuery = implode(",", $properties);
        $valuesQuery = implode(",", $values);

        $stmt = $this->db->prepare("INSERT OR REPLACE INTO islands (identifier, locked, islandType, members, blocks, $propertiesQuery) VALUES (:identifier, :locked, :islandType, :members, :blocks, $valuesQuery)");
        $stmt->bindValue(":identifier", $island->getIdentifier());
        $stmt->bindValue(":locked", $island->isLocked());
        $stmt->bindValue(":islandType", $island->getType());
        $stmt->bindValue(":members", implode(",", $island->getMemberNames()));
        $stmt->bindValue(":blocks", $island->getBlocksBuilt());

        foreach($customValues as $customValue) {
            $stmt->bindValue(":" . $customValue->getIdentifier(), $customValue->getValue());
        }

        $stmt->execute();
    }

}