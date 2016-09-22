<?php

/**
 * This is GiantQuartz property.
 *
 * Copyright (C) 2016 GiantQuartz
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author GiantQuartz
 *
 */

namespace SkyBlock;

use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\chat\Chat;
use SkyBlock\island\Island;

class EventListener implements Listener {

    /** @var Main */
    private $plugin;

    /**
     * EventListener constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
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
    }

    /**
     * Executes onLeave actions
     *
     * @param PlayerQuitEvent $event
     */
    public function onLeave(PlayerQuitEvent $event) {
        $this->plugin->getIslandManager()->unloadByPlayer($event->getPlayer());
    }

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
                    $items = [];
                    $items[] = Item::get(264);
                    $this->addItemMultipleTimes(3, Item::get(265), $items);
                    $this->addItemMultipleTimes(10, Item::get(266), $items);
                    $this->addItemMultipleTimes(20, Item::get(Item::LAPIS_ORE), $items);
                    $this->addItemMultipleTimes(40, Item::get(Item::COAL), $items);
                    $this->addItemMultipleTimes(74, Item::get(Item::COBBLESTONE), $items);
                    $event->setDrops([$items[array_rand($items)]]);
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
        }
    }

    /**
     * Tries to remove a player on change event
     *
     * @param EntityLevelChangeEvent $event
     */
    public function onLevelChange(EntityLevelChangeEvent $event) {
        $entity = $event->getEntity();
        if($entity instanceof Player) {
            if($this->plugin->getIslandManager()->isOnlineIsland($event->getOrigin()->getName())) {
                $this->plugin->getIslandManager()->getOnlineIsland($event->getOrigin()->getName())->tryRemovePlayer($entity);
            }
            else if($this->plugin->getIslandManager()->isOnlineIsland($event->getTarget()->getName())) {
                $this->plugin->getIslandManager()->getOnlineIsland($event->getTarget()->getName())->addPlayer($entity);
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

}