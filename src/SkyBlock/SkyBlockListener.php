<?php

namespace SkyBlock;

use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\level\LevelUnloadEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBedEnterEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use SkyBlock\chat\Chat;
use SkyBlock\island\Island;

class SkyBlockListener implements Listener {

    /** @var SkyBlock */
    private $plugin;
    private $cobbleDrops = [];

    /**
     * SkyBlockListener constructor.
     *
     * @param SkyBlock $plugin
     */
    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
		$this->addItemMultipleTimes(3, Item::get(Item::DIAMOND), $this->cobbleDrops);
		$this->addItemMultipleTimes(12, Item::get(Item::IRON_INGOT), $this->cobbleDrops);
		$this->addItemMultipleTimes(9, Item::get(Item::GOLD_INGOT), $this->cobbleDrops);
		$this->addItemMultipleTimes(5, Item::get(Item::LAPIS_ORE), $this->cobbleDrops);
		$this->addItemMultipleTimes(40, Item::get(Item::COAL), $this->cobbleDrops);
		$this->addItemMultipleTimes(500, Item::get(Item::COBBLESTONE), $this->cobbleDrops);
		shuffle($this->cobbleDrops);
    }

    /**
     * Try to register a player
     *
     * @param PlayerLoginEvent $event
     */
    public function onLogin(PlayerLoginEvent $event) {
        $this->plugin->getSkyBlockManager()->tryRegisterUser($event->getPlayer());
    }

    /**
     * Executes onJoin actions
     *
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event) {
        $this->plugin->getIslandManager()->checkPlayerIsland($event->getPlayer());
		$playerLevelName = $event->getPlayer()->getLevel()->getName();
        if($this->plugin->getIslandManager()->isOnlineIsland($playerLevelName)){
        	$this->plugin->getIslandManager()->getOnlineIsland($playerLevelName)->addPlayer($event->getPlayer());
		}
    }

    /**
     * Executes onLeave actions
     *
     * @param PlayerQuitEvent $event
     */
    public function onLeave(PlayerQuitEvent $event) {
        $this->plugin->getIslandManager()->unloadByPlayer($event->getPlayer());
    }

	/**
	 * @param       $times
	 * @param Item  $item
	 * @param array $array
	 * @return Item[]
	 */
    public function addItemMultipleTimes($times, Item $item, array &$array){
        for($i = 0; $i <= $times; $i++) {
            $array[] = $item;
        }
        return $array;
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event) {
        $island = $this->plugin->getIslandManager()->getOnlineIsland($event->getPlayer()->getLevel()->getName());
        if($island instanceof Island) {
            if(!$event->getPlayer()->isOp() and !in_array(strtolower($event->getPlayer()->getName()), $island->getAllMembers())) {
                $event->getPlayer()->sendPopup(TextFormat::RED . "You must be part of this island to break here!");
                $event->setCancelled();
            }
            else  {
                if($event->getBlock()->getId() == Block::COBBLESTONE) {
                    $event->setDrops([$this->cobbleDrops[array_rand($this->cobbleDrops)]]);
                }
            }
        }
    }

    /**
     * @param BlockPlaceEvent $event
     */
    public function onPlace(BlockPlaceEvent $event) {
        $island = $this->plugin->getIslandManager()->getOnlineIsland($event->getPlayer()->getLevel()->getName());
        if($island instanceof Island) {
            if(!$event->getPlayer()->isOp() and !in_array(strtolower($event->getPlayer()->getName()), $island->getAllMembers())) {
                $event->getPlayer()->sendPopup(TextFormat::RED . "You must be part of this island to place here!");
                $event->setCancelled();
            }
        }
    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event) {
        $island = $this->plugin->getIslandManager()->getOnlineIsland($event->getPlayer()->getLevel()->getName());
        if($island instanceof Island) {
            if(!$event->getPlayer()->isOp() and !in_array(strtolower($event->getPlayer()->getName()), $island->getAllMembers())) {
                $event->getPlayer()->sendPopup(TextFormat::RED . "You must be part of this island to place here!");
                $event->setCancelled();
            }
            if($event->getBlock() === Block::BED_BLOCK){
            	$event->setCancelled(true);
            	$event->getPlayer()->sendMessage("Beds cannot be used on islands.");
			}
        }
    }

    /**
     * Tries to remove a player on change event
     *
     * @param EntityLevelChangeEvent $event
     */
    public function onLevelChange(EntityLevelChangeEvent $event) {
        $entity = $event->getEntity();
        $originName = $event->getOrigin()->getName();
        $targetName = $event->getTarget()->getName();
        if($entity instanceof Player) {
            if($this->plugin->getIslandManager()->isOnlineIsland($originName)) {
                $this->plugin->getIslandManager()->getOnlineIsland($originName)->tryRemovePlayer($entity);
            }
           if($this->plugin->getIslandManager()->isOnlineIsland($targetName)) {
                $this->plugin->getIslandManager()->getOnlineIsland($targetName)->addPlayer($entity);
            }
        }
    }

    /**
     * @param PlayerChatEvent $event
     */
    public function onChat(PlayerChatEvent $event) {
        $chat = $this->plugin->getChatHandler()->getPlayerChat($event->getPlayer());
        if($chat instanceof Chat) {
            $recipients = $event->getRecipients();
            foreach($recipients as $key => $recipient) {
                if($recipient instanceof Player) {
                    if(!in_array($recipient, $chat->getMembers())) {
                        unset($recipients[$key]);
                    }
                }
            }
        }
        else {
            $recipients = $event->getRecipients();
            foreach($recipients as $key => $recipient) {
                if($recipient instanceof Player) {
                    if($this->plugin->getChatHandler()->isInChat($recipient)) {
                        unset($recipients[$key]);
                    }
                }
            }
        }
        $event->setRecipients($recipients);
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function onHurt(EntityDamageEvent $event) {
        if($event instanceof EntityDamageByEntityEvent) {
            $entity = $event->getEntity();
            if($entity instanceof Player) {
                if($this->plugin->getIslandManager()->isOnlineIsland($entity->getLevel()->getName())) {
                    $event->setCancelled();
                }
            }
        }
    }

    /**
     * @param LevelUnloadEvent $event
     */
    public function onUnloadLevel(LevelUnloadEvent $event) {
        foreach($event->getLevel()->getPlayers() as $player) {
            $player->teleport($this->plugin->getServer()->getDefaultLevel()->getSafeSpawn());
        }
    }

	/**
	 * Prevent players from using beds on an island.
	 *
	 * @param PlayerBedEnterEvent $event
	 */
    public function onBedInteract(PlayerBedEnterEvent $event){
    	foreach($this->plugin->getIslandManager()->getOnlineIslands() as $island){
			if($island->isOnIsland($event->getPlayer())){
				$event->setCancelled(true);
				$event->getPlayer()->sendMessage("§l§c✖ Using beds on islands is not allowed.");
			}
		}
	}

	/**
	 * @param LevelLoadEvent $event
	 */
	public function onLevelLoad(LevelLoadEvent $event){
		$levelName = $event->getLevel()->getName();
		$islandConfigFile = Utils::getIslandPath($levelName);
		if(!$this->plugin->getIslandManager()->isOnlineIsland($levelName)){
			if(is_file($islandConfigFile)){
				Server::getInstance()->getLogger()->info("onLevelLoad found Island being loaded.");
				$config = new Config($islandConfigFile, Config::JSON);
				$this->plugin->getIslandManager()->addIsland(
					$config,
					$config->get("owner"),
					$levelName,
					$config->get("members"),
					$config->get("locked"),
					$config->get("home"),
					$config->get("generator")
				);
			}
			$island = $this->plugin->getIslandManager()->getOnlineIsland($levelName);
			$island->setPlayersOnline([$event->getLevel()->getPlayers()]);
		}
	}

}