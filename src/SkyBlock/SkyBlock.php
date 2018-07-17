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
        $this->skyBlockGeneratorManager = new SkyBlockGeneratorManager($this);
        $this->skyBlockManager = new SkyBlockManager($this);
        $this->islandManager = new IslandManager($this);
        $this->eventListener = new SkyBlockListener($this);
        $this->invitationHandler = new InvitationHandler($this);
        $this->chatHandler = new ChatHandler();
        $this->resetHandler = new ResetHandler();
        $this->ui = new SkyBlockForms();
        $this->getScheduler()->scheduleRepeatingTask(new PluginHearbeat($this), 20);
        $this->registerCommand();
        $this->getLogger()->info("Enabled");
    }

    public function onDisable() {
        $this->getLogger()->info("Disabled");
    }

    /**
     * Return SkyBlock instance
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