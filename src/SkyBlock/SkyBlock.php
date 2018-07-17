<?php

namespace SkyBlock;

use pocketmine\plugin\PluginBase;
use SkyBlock\chat\ChatHandler;
use SkyBlock\command\SkyBlockCleanup;
use SkyBlock\command\SkyBlockCommand;
use SkyBlock\command\SkyBlockUICommand;
use SkyBlock\generator\SkyBlockGeneratorManager;
use SkyBlock\invitation\InvitationHandler;
use SkyBlock\island\IslandManager;
use SkyBlock\reset\ResetHandler;
use SkyBlock\session\SessionManager;
use SkyBlock\skyblock\SkyBlockManager;
use SkyBlock\ui\SkyBlockForms;

class SkyBlock extends PluginBase {

    /** @var SkyBlock */
    private static $object = null;

    /** @var SessionManager */
    private $sessionManager;
    
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
    
    public function initialize(): void {
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
    
    public function onLoad(): void {
        if(!self::$object instanceof SkyBlock) {
            self::$object = $this;
        }
    }

    public function onEnable(): void {
        $this->initialize();
        $this->sessionManager = new SessionManager($this);
        $this->skyBlockGeneratorManager = new SkyBlockGeneratorManager($this);
        $this->skyBlockManager = new SkyBlockManager($this);
        $this->islandManager = new IslandManager($this);
        $this->eventListener = new SkyBlockListener($this);
        $this->invitationHandler = new InvitationHandler($this);
        $this->chatHandler = new ChatHandler();
        $this->resetHandler = new ResetHandler();
        $this->ui = new SkyBlockForms();
        $this->getScheduler()->scheduleRepeatingTask(new SkyBlockHeart($this), 20);
        $this->registerCommands();
        $this->getLogger()->info("Enabled");
    }

    public function onDisable(): void {
        $this->getLogger()->info("Disabled");
    }

    /**
     * @return SkyBlock
     */
    public static function getInstance(): SkyBlock {
        return self::$object;
    }

    /**
     * @return SkyBlockGeneratorManager
     */
    public function getSkyBlockGeneratorManager(): SkyBlockGeneratorManager {
        return $this->skyBlockGeneratorManager;
    }

    /**
     * @return SkyBlockManager
     */
    public function getSkyBlockManager(): SkyBlockManager {
        return $this->skyBlockManager;
    }

    /**
     * @return IslandManager
     */
    public function getIslandManager(): IslandManager {
        return $this->islandManager;
    }

    /**
     * Return InvitationHandler instance
     *
     * @return InvitationHandler
     */
    public function getInvitationHandler(): InvitationHandler {
        return $this->invitationHandler;
    }

    /**
     * Return ResetHandler instance
     *
     * @return ResetHandler
     */
    public function getResetHandler(): ResetHandler {
        return $this->resetHandler;
    }

    /**
     * Return ChatHandler instance
     *
     * @return ChatHandler
     */
    public function getChatHandler(): ChatHandler {
        return $this->chatHandler;
    }

	/**
	 * Returns SkyBlockForms
	 *
	 *
	 * @return SkyBlockForms
	 */
    public function getUserInterface(): SkyBlockForms {
    	return $this->ui;
	}
    
    /**
     * Register SkyBlock commands
     */
    public function registerCommands(): void {
        $this->getServer()->getCommandMap()->register("island", new SkyBlockCommand($this));
        if($this->getServer()->getPluginManager()->getPlugin("FormAPI"))
		$this->getServer()->getCommandMap()->register("skyblock", new SkyBlockUICommand($this));
		$this->getServer()->getCommandMap()->register("sbcleanup", new SkyBlockCleanup($this));
    }

}