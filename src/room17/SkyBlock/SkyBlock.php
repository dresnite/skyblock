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

namespace room17\SkyBlock;

use pocketmine\plugin\PluginBase;
use room17\SkyBlock\command\IslandCommandMap;
use room17\SkyBlock\island\generator\IslandGeneratorManager;
use room17\SkyBlock\island\IslandManager;
use room17\SkyBlock\provider\json\JSONProvider;
use room17\SkyBlock\provider\Provider;
use room17\SkyBlock\session\SessionManager;

class SkyBlock extends PluginBase {

    /** @var SkyBlock */
    private static $instance;

    /** @var SkyBlockSettings */
    private $settings;
    
    /** @var Provider */
    private $provider;
    
    /** @var SessionManager */
    private $sessionManager;
    
    /** @var IslandManager */
    private $islandManager;
    
    /** @var IslandCommandMap */
    private $commandMap;
    
    /** @var IslandGeneratorManager */
    private $generatorManager;

    /**
     * @return SkyBlock
     */
    public static function getInstance(): SkyBlock {
        return self::$instance;
    }
    
    public function onLoad(): void {
        self::$instance = $this;
        if(!is_dir($this->getDataFolder())) {
            mkdir($this->getDataFolder());
        }
        $this->saveResource("messages.json");
        $this->saveResource("settings.yml");
    }

    public function onEnable(): void {
        $this->settings = new SkyBlockSettings($this);
        $this->provider = new JSONProvider($this);
        $this->sessionManager = new SessionManager($this);
        $this->islandManager = new IslandManager($this);
        $this->generatorManager = new IslandGeneratorManager($this);
        $this->commandMap = new IslandCommandMap($this);
        $this->checkSpawnProtection();
        $this->getLogger()->info("SkyBlock was enabled");
    }

    public function onDisable(): void {
        foreach($this->islandManager->getIslands() as $island) {
            $island->save();
        }
        $this->getLogger()->info("SkyBlock was disabled");
    }
    
    /**
     * @return SkyBlockSettings
     */
    public function getSettings(): SkyBlockSettings {
        return $this->settings;
    }
    
    /**
     * @return Provider
     */
    public function getProvider(): Provider {
        return $this->provider;
    }
    
    /**
     * @return SessionManager
     */
    public function getSessionManager(): SessionManager {
        return $this->sessionManager;
    }
    
    /**
     * @return IslandManager
     */
    public function getIslandManager(): IslandManager {
        return $this->islandManager;
    }

    /**
     * @return IslandGeneratorManager
     */
    public function getGeneratorManager(): IslandGeneratorManager {
        return $this->generatorManager;
    }

    private function checkSpawnProtection(): void {
        if ($this->getServer()->getSpawnRadius() > 0) {
            $this->getLogger()->warning("Please, disable the spawn protection on your server.properties, otherwise SkyBlock won't work correctly");
        }
    }

}