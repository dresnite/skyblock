<?php

namespace SkyBlock;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\level\LevelUnloadEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\Player;
use SkyBlock\isle\IsleManager;
use SkyBlock\session\Session;
use SkyBlock\session\SessionManager;

class SkyBlockListener implements Listener {

    /** @var SkyBlock */
    private $plugin;
    
    /** @var SessionManager */
    private $sessionManager;
    
    /** @var IsleManager */
    private $isleManager;

    /**
     * SkyBlockListener constructor.
     *
     * @param SkyBlock $plugin
     */
    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
        $this->sessionManager = $plugin->getSessionManager();
        $this->isleManager = $plugin->getIsleManager();
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }
    
    /**
     * @param Player $player
     * @return Session
     */
    public function getSession(Player $player): Session {
        return $this->plugin->getSessionManager()->getSession($player);
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        $session = $this->getSession($player);
        $isle = $this->isleManager->getIsle($player->getLevel()->getName());
        if($isle != null and $session->getIsle() !== $isle) {
            $session->sendTranslatedPopup("MUST_ME_MEMBER");
            $event->setCancelled();
        }
    }

    /**
     * @param BlockPlaceEvent $event
     */
    public function onPlace(BlockPlaceEvent $event): void {
        $player = $event->getPlayer();
        $session = $this->getSession($player);
        $isle = $this->isleManager->getIsle($player->getLevel()->getName());
        if($isle != null and $session->getIsle() !== $isle) {
            $session->sendTranslatedPopup("MUST_ME_MEMBER");
            $event->setCancelled();
        }
    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $session = $this->getSession($player);
        $isle = $this->plugin->getIsleManager()->getIsle($player->getLevel()->getName());
        if($isle != null and $session->getIsle() !== $isle) {
            $session->sendTranslatedPopup("MUST_ME_MEMBER");
            $event->setCancelled();
        }
    }

    /**
     * @param EntityLevelChangeEvent $event
     */
    public function onLevelChange(EntityLevelChangeEvent $event): void {
        $originIsle = $this->isleManager->getIsle($event->getOrigin()->getName());
        $targetIsle = $this->isleManager->getIsle($event->getTarget()->getName());
        if($originIsle != null) {
            $originIsle->update();
        }
        if($targetIsle != null) {
            $targetIsle->update();
        }
    }

    /**
     * @param PlayerChatEvent $event
     */
    public function onChat(PlayerChatEvent $event): void {
        $sessionManager = $this->plugin->getSessionManager();
        $session = $sessionManager->getSession($event->getPlayer());
        if(!($session->hasIsle()) or !($session->isInChat())) {
            return;
        }
        $recipients = [];
        foreach($sessionManager->getSessions() as $userSession) {
            if($userSession->isInChat() and $userSession->getIsle() === $session->getIsle()) {
                $recipients[] = $userSession->getPlayer();
            }
        }
        $event->setRecipients($recipients);
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function onHurt(EntityDamageEvent $event): void {
        if($event instanceof EntityDamageByEntityEvent) {
            $entity = $event->getEntity();
            if($entity instanceof Player) {
                if($this->isleManager->getIsle($entity->getLevel()->getName()) != null) {
                    $event->setCancelled();
                }
            }
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

}