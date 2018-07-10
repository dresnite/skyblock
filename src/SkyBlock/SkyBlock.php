<?php

namespace SkyBlock;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use SkyBlock\chat\ChatHandler;
use SkyBlock\command\SkyBlockCleanup;
use SkyBlock\command\SkyBlockCommand;
use SkyBlock\command\SkyBlockUICommand;
use SkyBlock\generator\SkyBlockGeneratorManager;
use SkyBlock\invitation\InvitationHandler;
use SkyBlock\island\IslandManager;
use SkyBlock\reset\ResetHandler;
use SkyBlock\skyblock\SkyBlockManager;
use SkyBlock\ui\SkyBlockForms;

class SkyBlock extends PluginBase {

    /** @var SkyBlock */
    private static $object = null;

    /** @var SkyBlockGeneratorManager */
    private $skyBlockGeneratorManager;

    /** @var SkyBlockManager */
    private $skyBlockManager;

    /** @var IslandManager */
    private $islandManager;

    /** @var InvitationHandler */
    private $invitationHandler;

    /** @var ResetHandler */
    private $resetHandler;

    /** @var ChatHandler */
    private $chatHandler;

    /** @var SkyBlockListener */
    private $eventListener;

    /** @var SkyBlockForms */
    private $ui;

    public function onLoad() {
        if(!self::$object instanceof SkyBlock) {
            self::$object = $this;
        }
    }

    public function onEnable() {
        $this->initialize();
        $this->setSkyBlockGeneratorManager();
        $this->setSkyBlockManager();
        $this->setIslandManager();
        $this->setEventListener();
        $this->setInvitationHandler();
        $this->setChatHandler();
        $this->setResetHandler();
        $this->setUserInterface();
        $this->setPluginHearbeat();
        $this->registerCommand();
        $this->getLogger()->info(TextFormat::AQUA . TextFormat::BOLD . "[" . TextFormat::GREEN . "SkyBlockPE" . TextFormat::AQUA . "] " . TextFormat::RESET . TextFormat::DARK_GREEN . "Skyblock by xXSirGamesXx has been Enabled");
    }

    public function onDisable() {
        $this->getLogger()->info(TextFormat::AQUA . TextFormat::BOLD . "[" . TextFormat::GREEN . "SkyBlockPE" . TextFormat::AQUA . "] " . TextFormat::RESET . TextFormat::DARK_GREEN . "Skyblock by xXSirGamesXx has been Disabled");
    }

    /**
     * Return DynamicShopUI instance
     *
     * @return SkyBlock
     */
    public static function getInstance() {
        return self::$object;
    }

    /**
     * Return SkyBlockGeneratorManager instance
     *
     * @return SkyBlockGeneratorManager
     */
    public function getSkyBlockGeneratorManager() {
        return $this->skyBlockGeneratorManager;
    }

    /**
     * Return SkyBlockManager instance
     *
     * @return SkyBlockManager
     */
    public function getSkyBlockManager() {
        return $this->skyBlockManager;
    }

    /**
     * Return island manager
     *
     * @return IslandManager
     */
    public function getIslandManager() {
        return $this->islandManager;
    }

    /**
     * Return SkyBlockListener instance
     *
     * @return SkyBlockListener
     */
    public function getEventListener() {
        return $this->eventListener;
    }

    /**
     * Return InvitationHandler instance
     *
     * @return InvitationHandler
     */
    public function getInvitationHandler() {
        return $this->invitationHandler;
    }

    /**
     * Return ResetHandler instance
     *
     * @return ResetHandler
     */
    public function getResetHandler() {
        return $this->resetHandler;
    }

    /**
     * Return ChatHandler instance
     *
     * @return ChatHandler
     */
    public function getChatHandler() {
        return $this->chatHandler;
    }

	/**
	 * Returns SkyBlockForms
	 *
	 *
	 * @return SkyBlockForms
	 */

    public function getUserInterface(){
    	return $this->ui;
	}

    /**
     * Register SkyBlockGeneratorManager instance
     */
    public function setSkyBlockGeneratorManager() {
        $this->skyBlockGeneratorManager = new SkyBlockGeneratorManager($this);
    }

    /**
     * Register SkyBlockManager instance
     */
    public function setSkyBlockManager() {
        $this->skyBlockManager = new SkyBlockManager($this);
    }

    /**
     * Register IslandManager instance
     */
    public function setIslandManager() {
        $this->islandManager = new IslandManager($this);
    }

    /**
     * Register SkyBlockListener instance
     */
    public function setEventListener() {
        $this->eventListener = new SkyBlockListener($this);
    }

    /**
     * Schedule the PluginHearbeat
     */
    public function setPluginHearbeat() {
        $this->getScheduler()->scheduleRepeatingTask(new PluginHearbeat($this), 20);
    }

    /**
     * Register InvitationHandler instance
     */
    public function setInvitationHandler() {
        $this->invitationHandler = new InvitationHandler($this);
    }

    /**
     * Register ResetHandler instance
     */
    public function setResetHandler() {
        $this->resetHandler = new ResetHandler();
    }

    /**
     * Register ChatHandler instance
     */
    public function setChatHandler() {
        $this->chatHandler = new ChatHandler();
    }

    public function setUserInterface() {
    	$this->ui = new SkyBlockForms();
	}

    /**
     * Register SkyBlock commands
     */
    public function registerCommand() {
        $this->getServer()->getCommandMap()->register("island", new SkyBlockCommand($this));
        if($this->getServer()->getPluginManager()->getPlugin("FormAPI"))
		$this->getServer()->getCommandMap()->register("skyblock", new SkyBlockUICommand($this));
		$this->getServer()->getCommandMap()->register("sbcleanup", new SkyBlockCleanup($this));
    }

    public function initialize() {
        if(!is_dir($this->getDataFolder())) {
            @mkdir($this->getDataFolder());
        }
        if(!is_dir($this->getDataFolder() . "islands")) {
            @mkdir($this->getDataFolder() . "islands");
        }
        if(!is_dir($this->getDataFolder() . "users")) {
            @mkdir($this->getDataFolder() . "users");
        }
        $this->saveDefaultConfig();
    }

}