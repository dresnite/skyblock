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

use pocketmine\block\Solid;
use pocketmine\entity\object\Painting;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockFormEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\level\ChunkLoadEvent;
use pocketmine\event\level\LevelUnloadEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBedEnterEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\tile\Chest;
use pocketmine\tile\Tile;
use room17\SkyBlock\generator\IslandGenerator;
use room17\SkyBlock\island\IslandManager;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\session\SessionManager;
use room17\SkyBlock\utils\MessageContainer;

class SkyBlockListener implements Listener {

    /** @var SkyBlock */
    private $plugin;
    
    /** @var SessionManager */
    private $sessionManager;
    
    /** @var IslandManager */
    private $islandManager;

    /**
     * SkyBlockListener constructor.
     *
     * @param SkyBlock $plugin
     */
    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
        $this->sessionManager = $plugin->getSessionManager();
        $this->islandManager = $plugin->getIslandManager();
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    /**
     * @param Player $player
     * @return Session|null
     */
    public function getSession(Player $player): ?Session {
        return $this->plugin->getSessionManager()->getSession($player);
    }
    
    /**
     * @param ChunkLoadEvent $event
     */
    public function onChunkLoad(ChunkLoadEvent $event): void {
        $level = $event->getLevel();
        $island = $this->plugin->getIslandManager()->getIsland($level->getName());
        if($island == null) {
            return;
        }
        $generator = $this->plugin->getGeneratorManager()->getGenerator($type = $island->getType());
        /** @var IslandGenerator $generator */
        $position = $generator::getChestPosition();
        if($level->getChunk($position->x >> 4, $position->z >> 4) === $event->getChunk() and $event->isNewChunk()) {
            /** @var Chest $chest */
            $chest = Tile::createTile(Tile::CHEST, $level, Chest::createNBT($position));
            foreach($this->plugin->getSettings()->getChestPerGenerator($type) as $item) {
                $chest->getInventory()->addItem($item);
            }
        }
    }
    
    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        $session = $this->getSession($player);
        $island = $this->islandManager->getIsland($player->getLevel()->getName());
        if($island != null) {
            if(!$island->canInteract($session)) {
                $session->sendTranslatedPopup(new MessageContainer("MUST_BE_MEMBER"));
                $event->setCancelled();
            } elseif(!($event->isCancelled()) and $event->getBlock() instanceof Solid) {
                $island->destroyBlock();
            }
        }
    }
    
    /**
     * @param BlockPlaceEvent $event
     */
    public function onPlace(BlockPlaceEvent $event): void {
        $player = $event->getPlayer();
        $session = $this->getSession($player);
        $island = $this->islandManager->getIsland($player->getLevel()->getName());
        if($island != null) {
            if(!$island->canInteract($session)) {
                $session->sendTranslatedPopup(new MessageContainer("MUST_BE_MEMBER"));
                $event->setCancelled();
            } elseif(!($event->isCancelled()) and $event->getBlock() instanceof Solid) {
                $island->addBlock();
            }
        }
    }

    /**
     * @param BlockFormEvent $event
     */
    public function onBlockForm(BlockFormEvent $event): void {
        $block = $event->getBlock();
        $newBlock = $event->getNewState();
        $island = $this->islandManager->getIsland($block->getLevel()->getName());
        if($island != null and !($block instanceof Solid) and $newBlock instanceof Solid) {
            $island->addBlock();
        }
    }
    
    /**
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $session = $this->getSession($player);
        $island = $this->plugin->getIslandManager()->getIsland($player->getLevel()->getName());
        if($island != null and !($island->canInteract($session))) {
            $session->sendTranslatedPopup(new MessageContainer("MUST_BE_MEMBER"));
            $event->setCancelled();
        }
    }

    /**
     * @param PlayerBedEnterEvent $event
     */
    public function onBedEnter(PlayerBedEnterEvent $event): void {
        $player = $event->getPlayer();
        $session = $this->getSession($event->getPlayer());
        if($session->hasIsland() && $session->getIsland()->getLevel() === $player->getLevel()) {
            $event->setCancelled();
        }
    }

    /**
     * @param PlayerChatEvent $event
     */
    public function onChat(PlayerChatEvent $event): void {
        $sessionManager = $this->plugin->getSessionManager();
        $session = $sessionManager->getSession($event->getPlayer());
        if(!($session->hasIsland()) or !($session->isInChat())) {
            return;
        }
        $recipients = [];
        foreach($sessionManager->getSessions() as $userSession) {
            if($userSession->isInChat() and $userSession->getIsland() === $session->getIsland()) {
                $recipients[] = $userSession->getPlayer();
            }
        }
        $event->setRecipients($recipients);
    }
    
    /**
     * @param EntityDamageEvent $event
     */
    public function onHurt(EntityDamageEvent $event): void {
        $entity = $event->getEntity();
        $level = $entity->getLevel();
        if($level == null) return;
        $island = $this->islandManager->getIsland($level->getName());
        if($island == null) return;
        if($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            if(($entity instanceof Player or ($entity instanceof Painting and $damager instanceof Player
                and !$island->canInteract($this->getSession($damager))))) {
                $event->setCancelled();
            }
        } elseif($event->getCause() == EntityDamageEvent::CAUSE_VOID
            and $this->plugin->getSettings()->isPreventVoidDamage()) {
            $entity->teleport($island->getSpawnLocation());
            $event->setCancelled();
        }
    }
    
    
    /**
     * @param LevelUnloadEvent $event
     */
    public function onUnloadLevel(LevelUnloadEvent $event): void {
        foreach($event->getLevel()->getPlayers() as $player) {
            $player->teleport($this->plugin->getServer()->getDefaultLevel()->getSafeSpawn());
        }
    }

    /**
     * @param PlayerCommandPreprocessEvent $event
     */
    public function onCommand(PlayerCommandPreprocessEvent $event): void {
        $message = $event->getMessage();
        $player = $event->getPlayer();
        if($this->islandManager->getIsland($player->getLevel()->getName()) != null and
            $message{0} == "/" and
            in_array(strtolower(substr($message, 1)), $this->plugin->getSettings()->getIslandBlockedCommands())
        ) {
            $this->getSession($player)->sendTranslatedMessage(new MessageContainer("BLOCKED_COMMAND"));
            $event->setCancelled();
        }
    }

    /**
     * @param PlayerQuitEvent $event
     * @throws \ReflectionException
     */
    public function onQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        $session = $this->getSession($player);
        if($session == null) return;
        $islandManager = $this->plugin->getIslandManager();
        foreach($islandManager->getIslands() as $island) {
            if($island->isCooperator($session)) {
                $island->removeCooperator($session);
            }
        }
        $island = $islandManager->getIsland($player->getLevel()->getName());
        if($island != null) {
            $player->teleport($this->plugin->getServer()->getDefaultLevel()->getSafeSpawn());
            $island->tryToClose();
        }
    }

}