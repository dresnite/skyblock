<?php

namespace SkyBlock;

use pocketmine\level\Position;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use SkyBlock\command\IsleCommandMap;
use SkyBlock\generator\GeneratorManager;
use SkyBlock\isle\IsleManager;
use SkyBlock\provider\json\JSONProvider;
use SkyBlock\provider\Provider;
use SkyBlock\session\SessionManager;

class SkyBlock extends PluginBase {

    /** @var SkyBlock */
    private static $object = null;

    /** @var Provider */
    private $provider;
    
    /** @var SessionManager */
    private $sessionManager;
    
    /** @var IsleManager */
    private $isleManager;
    
    /** @var IsleCommandMap */
    private $commandMap;
    
    /** @var GeneratorManager */
    private $generatorManager;
    
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
        $this->generatorManager = new GeneratorManager($this);
        $this->commandMap = new IsleCommandMap($this);
        $this->eventListener = new SkyBlockListener($this);
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
    public function getGeneratorManager(): GeneratorManager {
        return $this->generatorManager;
    }
    
    /**
     * @param int $seconds
     * @return string
     */
    public static function printSeconds(int $seconds): string {
        $m = floor($seconds / 60);
        $s = floor($seconds % 60);
        return (($m < 10 ? "0" : "") . $m . ":" . ($s < 10 ? "0" : "") . (string) $s);
    }
    
    /**
     * @param Position $position
     * @return string
     */
    public static function writePosition(Position $position): string {
        return "{$position->getLevel()->getName()},{$position->getX()},{$position->getY()},{$position->getZ()}";
    }
    
    /**
     * @param string $position
     * @return null|Position
     */
    public static function parsePosition(string $position): ?Position {
        $array = explode(",", $position);
        if(isset($array[3])) {
            $level = Server::getInstance()->getLevelByName($array[0]);
            if($level != null) {
                return new Position((float) $array[1],(float) $array[2],(float) $array[3], $level);
            }
        }
        return null;
    }
    
}