<?php

namespace SkyBlock;

use pocketmine\level\Position;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
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
    
    /** @var string[] */
    private $messages = [];
    
    public function onLoad(): void {
        if(!self::$object instanceof SkyBlock) {
            self::$object = $this;
        }
        $this->saveResource("messages.json");
        $this->saveDefaultConfig();
    }

    public function onEnable(): void {
        $this->provider = new JSONProvider($this);
        $this->sessionManager = new SessionManager($this);
        $this->isleManager = new IsleManager($this);
        $this->generatorManager = new GeneratorManager($this);
        $this->commandMap = new IsleCommandMap($this);
        $this->eventListener = new SkyBlockListener($this);
        $this->messages = json_decode(file_get_contents($this->getDataFolder() . "messages.json"), true);
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
     * @return string[]
     */
    public function getMessages(): array {
        return $this->messages;
    }
    
    /**
     * @param string $identifier
     * @param array $args
     * @return string
     */
    public function getMessage(string $identifier, array $args = []) {
        $message = $this->messages[$identifier] ?? "Message ($identifier) not found";
        $message = self::translateColors($message);
        foreach($args as $arg => $value) {
            $message = str_replace("{" . $arg . "}", $value, $message);
        }
        return $message;
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
    
    /**
     * @param string $message
     * @return string
     */
    public static function translateColors(string $message): string {
        $message = str_replace("{BLACK}", TextFormat::BLACK, $message);
        $message = str_replace("{DARK_BLUE}", TextFormat::DARK_BLUE, $message);
        $message = str_replace("{DARK_GREEN}", TextFormat::DARK_GREEN, $message);
        $message = str_replace("{DARK_AQUA}", TextFormat::DARK_AQUA, $message);
        $message = str_replace("{DARK_RED}", TextFormat::DARK_RED, $message);
        $message = str_replace("{DARK_PURPLE}", TextFormat::DARK_PURPLE, $message);
        $message = str_replace("{ORANGE}", TextFormat::GOLD, $message);
        $message = str_replace("{GRAY}", TextFormat::GRAY, $message);
        $message = str_replace("{DARK_GRAY}", TextFormat::DARK_GRAY, $message);
        $message = str_replace("{BLUE}", TextFormat::BLUE, $message);
        $message = str_replace("{GREEN}", TextFormat::GREEN, $message);
        $message = str_replace("{AQUA}", TextFormat::AQUA, $message);
        $message = str_replace("{RED}", TextFormat::RED, $message);
        $message = str_replace("{LIGHT_PURPLE}", TextFormat::LIGHT_PURPLE, $message);
        $message = str_replace("{YELLOW}", TextFormat::YELLOW, $message);
        $message = str_replace("{WHITE}", TextFormat::WHITE, $message);
        $message = str_replace("{OBFUSCATED}", TextFormat::OBFUSCATED, $message);
        $message = str_replace("{BOLD}", TextFormat::BOLD, $message);
        $message = str_replace("{STRIKETHROUGH}", TextFormat::STRIKETHROUGH, $message);
        $message = str_replace("{UNDERLINE}", TextFormat::UNDERLINE, $message);
        $message = str_replace("{ITALIC}", TextFormat::ITALIC, $message);
        $message = str_replace("{RESET}", TextFormat::RESET, $message);
        return $message;
    }
    
}