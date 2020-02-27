<?php

declare(strict_types=1);

namespace room17\SkyBlock\island;


use pocketmine\block\Solid;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockFormEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Cancellable;
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
use room17\SkyBlock\session\Session;
use room17\SkyBlock\session\SessionLocator;
use room17\SkyBlock\SkyBlock;
use room17\SkyBlock\utils\message\MessageContainer;
use room17\SkyBlock\utils\Utils;

class IslandListener implements Listener {

    /** @var IslandManager */
    private $manager;

    /** @var SkyBlock */
    private $plugin;

    /**
     * IslandListener constructor.
     * @param IslandManager $manager
     */
    public function __construct(IslandManager $manager) {
        $this->manager = $manager;
        $this->plugin = $manager->getPlugin();
    }

    /**
     * Prevents players from breaking blocks on others property
     * @throws \ReflectionException
     */
    public function onBreak(BlockBreakEvent $event): void {
        $session = SessionLocator::getSession($event->getPlayer());
        if(($island = $session->getIslandByLevel()) == null) {
            return;
        }
        $this->checkPermissionToInteract($island, $session, $event);
        if(!$event->isCancelled() and $event->getBlock() instanceof Solid) {
            $island->destroyBlock();
        }
    }

    private function checkPermissionToInteract(Island $island, Session $session, Cancellable $event) {
        if($island->canInteract($session)) {
            return;
        }
        $session->sendTranslatedPopup(new MessageContainer("MUST_BE_MEMBER"));
        $event->setCancelled();
    }

    /**
     * Prevents players from placing blocks on others property
     * @throws \ReflectionException
     */
    public function onPlace(BlockPlaceEvent $event): void {
        $session = SessionLocator::getSession($event->getPlayer());
        if(($island = $session->getIslandByLevel()) == null) {
            return;
        }
        $this->checkPermissionToInteract($island, $session, $event);
        if(!$event->isCancelled() and $event->getBlock() instanceof Solid) {
            $island->addBlock();
        }
    }

    /**
     * Adds one to the block count if a new block is formed
     */
    public function onBlockForm(BlockFormEvent $event): void {
        $block = $event->getBlock();
        $island = $this->manager->getIsland($block->getLevel()->getName());
        if($island != null and !$block instanceof Solid and $event->getNewState() instanceof Solid) {
            $island->addBlock();
        }
    }

    /**
     * Prevent players from interacting on others property
     * @throws \ReflectionException
     */
    public function onInteract(PlayerInteractEvent $event): void {
        $session = SessionLocator::getSession($event->getPlayer());
        $island = $session->getIslandByLevel();
        if($island != null) {
            $this->checkPermissionToInteract($island, $session, $event);
        }
    }

    /**
     * Prevents players from sleeping on beds inside islands
     * This is known for causing unexpected behavior on the plugin
     * @throws \ReflectionException
     */
    public function onEnterBed(PlayerBedEnterEvent $event): void {
        $session = SessionLocator::getSession($event->getPlayer());
        if($session->getIslandByLevel() != null) {
            $event->setCancelled();
        }
    }

    /**
     * Sends the message to the island private chat if the player is connected to it
     * @throws \ReflectionException
     */
    public function onChat(PlayerChatEvent $event): void {
        $session = SessionLocator::getSession($event->getPlayer());
        if(!$session->hasIsland() or !$session->isInChat()) {
            return;
        }
        $chatFormat = $this->plugin->getSettings()->getChatFormat();
        $chatFormat = str_replace("{username}", $session->getName(), $chatFormat);
        $chatFormat = str_replace("{message}", $event->getMessage(), $chatFormat);
        $chatFormat = Utils::translateColors($chatFormat);
        $event->setFormat($chatFormat);
        $event->setRecipients($session->getIsland()->getChattingPlayers());
    }

    /**
     * Prevent players from sending blocked commands inside islands
     * @throws \ReflectionException
     */
    public function onCommand(PlayerCommandPreprocessEvent $event): void {
        $session = SessionLocator::getSession($event->getPlayer());
        $message = $event->getMessage();
        if($session->getIslandByLevel() == null or $message[0] != "/") {
            return;
        }
        $command = strtolower(substr($message, 1));
        if(in_array($command, $this->plugin->getSettings()->getBlockedCommands())) {
            $session->sendTranslatedMessage(new MessageContainer("BLOCKED_COMMAND"));
            $event->setCancelled();
        }
    }

    /**
     * Makes sure nobody gets void damage if it's not enabled!
     * @throws \ReflectionException
     */
    public function onDamage(EntityDamageEvent $event): void {
        $entity = $event->getEntity();
        $level = $entity->getLevel();

        if($level == null) {
            return; // Basically a hack to prevent SkyBlock from crashing because of shitty poggit plugins
        }

        $island = $this->manager->getIslandByLevel($level);
        if($island == null) {
            return;
        }

        if($event instanceof EntityDamageByEntityEvent) {
            $this->onDamageByEntityInIsland($island, $event);
        } elseif($event->getCause() == EntityDamageByEntityEvent::CAUSE_VOID and $this->plugin->getSettings()->preventVoidDamage()) {
            $entity->teleport($island->getSpawnLocation());
            $event->setCancelled();
        }
    }

    /**
     * Prevents PvP inside islands and makes sure nobody can steal your paintings!
     * @throws \ReflectionException
     */
    private function onDamageByEntityInIsland(Island $island, EntityDamageByEntityEvent $event): void {
        $entity = $event->getEntity();
        $damager = $event->getDamager();

        if($entity instanceof Player) {
            $event->setCancelled();
        } elseif($damager instanceof Player) {
            $this->checkPermissionToInteract($island, SessionLocator::getSession($damager), $event);
        }
    }

    /**
     * Removes the player as cooperator if possible and tries to close their island
     * @throws \ReflectionException
     */
    public function onQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        if(!SessionLocator::isSessionOpen($player)) {
            return;
        }

        $session = SessionLocator::getSession($player);
        foreach($this->manager->getIslands() as $island) {
            $island->removeCooperator($session);
        }

        $island = $session->getIslandByLevel();
        if($island != null) {
            $session->teleportToSpawn();
            $island->tryToClose();
        } else {
            $session->clearInvitations();
        }
    }

    /**
     * Spawns the chest of recently created islands
     */
    public function onChunkLoad(ChunkLoadEvent $event): void {
        $level = $event->getLevel();
        $island = $this->manager->getIsland($level->getName());

        if($island == null) {
            return;
        }

        $generator = $this->plugin->getGeneratorManager()->getGenerator($type = $island->getType());
        $position = $generator::getChestPosition();

        if($level->getChunk($position->x >> 4, $position->z >> 4) === $event->getChunk() and $event->isNewChunk()) {
            /** @var Chest $chest */
            $chest = Tile::createTile(Tile::CHEST, $level, Chest::createNBT($position));
            foreach($this->plugin->getSettings()->getCustomChestContent($type) as $item) {
                $chest->getInventory()->addItem($item);
            }
        }
    }

    /**
     * Teleports players to the spawn when an island is closed
     * Not sure if this is even necessary
     * @throws \ReflectionException
     */
    public function onUnloadLevel(LevelUnloadEvent $event): void {
        foreach($event->getLevel()->getPlayers() as $player) {
            SessionLocator::getSession($player)->teleportToSpawn();
        }
    }

}