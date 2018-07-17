<?php

namespace SkyBlock;

use pocketmine\plugin\PluginBase;
use SkyBlock\command\SkyBlockCleanup;
use SkyBlock\command\SkyBlockCommand;
use SkyBlock\generator\GeneratorManager;
use SkyBlock\isle\IsleManager;
use SkyBlock\provider\json\JSONProvider;
use SkyBlock\provider\Provider;
use SkyBlock\session\SessionManager;
use SkyBlock\skyblock\SkyBlockManager;

class SkyBlock extends PluginBase {

    /** @var SkyBlock */
    private static $object = null;

    /** @var Provider */
    private $provider;
    
    /** @var SessionManager */
    private $sessionManager;
    
    /** @var IsleManager */
    private $isleManager;
    
    /** @var GeneratorManager */
    private $skyBlockGeneratorManager;

    /** @var SkyBlockManager */
    private $skyBlockManager;

    /** @var SkyBlockListener */
    private $eventListener;
    
    public function onLoad(): void {
        if(!self::$object instanceof SkyBlock) {
            self::$object = $this;
        }
        $this->saveDefaultConfig();
    }

    public function onEnable(): void {
        $this->provider = new JSONProvider($this);
        $this->sessionManager = new SessionManager($this);
        $this->isleManager = new IsleManager($this);
        $this->skyBlockGeneratorManager = new GeneratorManager($this);
        $this->skyBlockManager = new SkyBlockManager($this);
        $this->eventListener = new SkyBlockListener($this);
        $this->registerCommands();
        $this->getLogger()->info("SkyBlock was enabled");
    }

    public function onDisable(): void {
        $this->getLogger()->info("SkyBlock was disabled");
    }

    /**
     * @return SkyBlock
     */
    public static function getInstance(): SkyBlock {
        return self::$object;
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
     * @return IsleManager
     */
    public function getIsleManager(): IsleManager {
        return $this->isleManager;
    }

    /**
     * @return GeneratorManager
     */
    public function getSkyBlockGeneratorManager(): GeneratorManager {
        return $this->skyBlockGeneratorManager;
    }

    /**
     * @return SkyBlockManager
     */
    public function getSkyBlockManager(): SkyBlockManager {
        return $this->skyBlockManager;
    }
    
    /**
     * Register SkyBlock commands
     */
    public function registerCommands(): void {
        $this->getServer()->getCommandMap()->register("island", new SkyBlockCommand($this));
		$this->getServer()->getCommandMap()->register("sbcleanup", new SkyBlockCleanup($this));
    }

}