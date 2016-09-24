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

namespace SkyBlock\command;

use SkyBlock\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use SkyBlock\invitation\Invitation;
use SkyBlock\island\Island;
use SkyBlock\Main;
use SkyBlock\reset\Reset;

class SkyBlockCommand extends Command {

    /** @var Main */
    private $plugin;

    /**
     * SkyBlockCommand constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        parent::__construct("skyblock", "Main SkyBlock command", "Usage: /skyblock", ["sb"]);
    }

    public function sendMessage(Player $sender, $message) {
        $sender->sendMessage(TextFormat::GREEN . "- " . TextFormat::WHITE . $message);
    }

    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if($sender instanceof Player) {
            if(isset($args[0])) {
                switch($args[0]) {
                    case "join":
                        $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                        if(empty($config->get("island"))) {
                            $this->sendMessage($sender, "You haven't a island!");
                        }
                        else {
                            $island = $this->plugin->getIslandManager()->getOnlineIsland($config->get("island"));
                            if($island instanceof Island) {
                                $island->addPlayer($sender);
                                $sender->teleport(new Position(15, 7, 10, $this->plugin->getServer()->getLevelByName($island->getIdentifier())));
                                $this->sendMessage($sender, "You were teleported to your island home");
                            }
                            else {
                                $this->sendMessage($sender, "You haven't a island!!");
                            }
                        }
                        break;
                    case "create":
                        $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                        if(empty($config->get("island"))) {
                            $reset = $this->plugin->getResetHandler()->getResetTimer($sender);
                            if($reset instanceof Reset) {
                                $minutes = Utils::printSeconds($reset->getTime());
                                $this->sendMessage($sender, "You'll be able to create a new island in {$minutes} minutes");
                            }
                            else {
                                $skyBlockManager = $this->plugin->getSkyBlockGeneratorManager();
                                if(isset($args[1])) {
                                    if($skyBlockManager->isGenerator($args[1])) {
                                        $this->plugin->getSkyBlockManager()->generateIsland($sender, $args[1]);
                                        $this->sendMessage($sender, "You successfully created a {$skyBlockManager->getGeneratorIslandName($args[1])} island!");
                                    }
                                    else {
                                        $this->sendMessage($sender, "That isn't a valid SkyBlock generator!");
                                    }
                                }
                                else {
                                    $this->plugin->getSkyBlockManager()->generateIsland($sender, "basic");
                                    $this->sendMessage($sender, "You successfully created a island!");
                                }
                            }
                        }
                        else {
                            $this->sendMessage($sender, "You already got a skyblock island!");
                        }
                        break;
                    case "home":
                        $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                        if(empty($config->get("island"))) {
                            $this->sendMessage($sender, "You haven't a island!");
                        }
                        else {
                            $island = $this->plugin->getIslandManager()->getOnlineIsland($config->get("island"));
                            if($island instanceof Island) {
                                $home = $island->getHomePosition();
                                if($home instanceof Position) {
                                    $sender->teleport($home);
                                    $this->sendMessage($sender, "You have been teleported to your island home");
                                }
                                else {
                                    $this->sendMessage($sender, "Your island haven't a home position set!");
                                }
                            }
                            else {
                                $this->sendMessage($sender, "You haven't a island!!");
                            }
                        }
                        break;
                    case "sethome":
                        $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                        if(empty($config->get("island"))) {
                            $this->sendMessage($sender, "You haven't a island!");
                        }
                        else {
                            $island = $this->plugin->getIslandManager()->getOnlineIsland($config->get("island"));
                            if($island instanceof Island) {
                                if($island->getOwnerName() == strtolower($sender->getName())) {
                                    if($sender->getLevel()->getName() == $config->get("island")) {
                                        $island->setHomePosition($sender->getPosition());
                                        $this->sendMessage($sender, "You set your island home successfully!");
                                    }
                                    else {
                                        $this->sendMessage($sender, "You must be in your island to set home!");
                                    }
                                }
                                else {
                                    $this->sendMessage($sender, "You must be the island leader to do this!");
                                }
                            }
                            else {
                                $this->sendMessage($sender, "You haven't a island!!");
                            }
                        }
                        break;
                    case "kick":
                    case "expel":
                        $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                        if(empty($config->get("island"))) {
                            $this->sendMessage($sender, "You haven't a island!");
                        }
                        else {
                            $island = $this->plugin->getIslandManager()->getOnlineIsland($config->get("island"));
                            if($island instanceof Island) {
                                if($island->getOwnerName() == strtolower($sender->getName())) {
                                    if(isset($args[1])) {
                                        $player = $this->plugin->getServer()->getPlayer($args[1]);
                                        if($player instanceof Player and $player->isOnline()) {
                                            if($player->getLevel()->getName() == $island->getIdentifier()) {
                                                $player->teleport($this->plugin->getServer()->getDefaultLevel()->getSafeSpawn());
                                                $this->sendMessage($sender, "{$player->getName()} has been kicked from your island!");
                                            }
                                            else {
                                                $this->sendMessage($sender, "The player isn't in your island!");
                                            }
                                        }
                                        else {
                                            $this->sendMessage($sender, "That isn't a valid player");
                                        }
                                    }
                                    else {
                                        $this->sendMessage($sender, "Usage: /skyblock expel <name>");
                                    }
                                }
                                else {
                                    $this->sendMessage($sender, "You must the island owner to expel anyone");
                                }
                            }
                            else {
                                $this->sendMessage($sender, "You haven't a island!");
                            }
                        }
                        break;
                    case "lock":
                        $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                        if(empty($config->get("island"))) {
                            $this->sendMessage($sender, "You haven't a island!");
                        }
                        else {
                            $island = $this->plugin->getIslandManager()->getOnlineIsland($config->get("island"));
                            if($island instanceof Island) {
                                if($island->getOwnerName() == strtolower($sender->getName())) {
                                    $island->setLocked(!$island->isLocked());
                                    $locked = ($island->isLocked()) ? "locked" : "unlocked";
                                    $this->sendMessage($sender, "Your island has been {$locked}!");
                                }
                                else {
                                    $this->sendMessage($sender, "You must be the island owner to do this!");
                                }
                            }
                            else {
                                $this->sendMessage($sender, "You haven't a island!");
                            }
                        }
                        break;
                    case "invite":
                        $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                        if(empty($config->get("island"))) {
                            $this->sendMessage($sender, "You haven't a island!");
                        }
                        else {
                            $island = $this->plugin->getIslandManager()->getOnlineIsland($config->get("island"));
                            if($island instanceof Island) {
                                if($island->getOwnerName() == strtolower($sender->getName())) {
                                    if(isset($args[1])) {
                                        $player = $this->plugin->getServer()->getPlayer($args[1]);
                                        if($player instanceof Player and $player->isOnline()) {
                                            $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($player);
                                            if(empty($config->get("island"))) {
                                                $this->plugin->getInvitationHandler()->addInvitation($sender, $player, $island);
                                                $this->sendMessage($sender, "You sent a invitation to {$player->getName()}!");
                                                $this->sendMessage($player, "{$sender->getName()} invited you to his island! Do /skyblock <accept/reject> {$sender->getName()}");
                                            }
                                            else {
                                                $this->sendMessage($sender, "This player is already in a island!");
                                            }
                                        }
                                        else {
                                            $this->sendMessage($sender, "{$args[1]} isn't a valid player!");
                                        }
                                    }
                                    else {
                                        $this->sendMessage($sender, "Usage: /skyblock invite <player>");
                                    }
                                }
                                else {
                                    $this->sendMessage($sender, "You must be the island owner to do this!");
                                }
                            }
                            else {
                                $this->sendMessage($sender, "You haven't a island!!");
                            }
                        }
                        break;
                    case "accept":
                        if(isset($args[1])) {
                            $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                            if(empty($config->get("island"))) {
                                $player = $this->plugin->getServer()->getPlayer($args[1]);
                                if($player instanceof Player and $player->isOnline()) {
                                    $invitation = $this->plugin->getInvitationHandler()->getInvitation($player);
                                    if($invitation instanceof Invitation) {
                                        if($invitation->getSender() == $player) {
                                            $invitation->accept();
                                        }
                                        else {
                                            $this->sendMessage($sender, "You haven't a invitation from {$player->getName()}!");
                                        }
                                    }
                                    else {
                                        $this->sendMessage($sender, "You haven't a invitation from {$player->getName()}");
                                    }
                                }
                                else {
                                    $this->sendMessage($sender, "{$args[1]} is not a valid player");
                                }
                            }
                            else {
                                $this->sendMessage($sender, "You cannot be in a island if you want join another island!");
                            }
                        }
                        else {
                            $this->sendMessage($sender, "Usage: /skyblock accept <sender name>");
                        }
                        break;
                    case "deny":
                    case "reject":
                        if(isset($args[1])) {
                            $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                            if(empty($config->get("island"))) {
                                $player = $this->plugin->getServer()->getPlayer($args[1]);
                                if($player instanceof Player and $player->isOnline()) {
                                    $invitation = $this->plugin->getInvitationHandler()->getInvitation($player);
                                    if($invitation instanceof Invitation) {
                                        if($invitation->getSender() == $player) {
                                            $invitation->deny();
                                        }
                                        else {
                                            $this->sendMessage($sender, "You haven't a invitation from {$player->getName()}!");
                                        }
                                    }
                                    else {
                                        $this->sendMessage($sender, "You haven't a invitation from {$player->getName()}");
                                    }
                                }
                                else {
                                    $this->sendMessage($sender, "{$args[1]} is not a valid player");
                                }
                            }
                            else {
                                $this->sendMessage($sender, "You cannot be in a island if you want reject another island!");
                            }
                        }
                        else {
                            $this->sendMessage($sender, "Usage: /skyblock accept <sender name>");
                        }
                        break;
                    case "members":
                        $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                        if(empty($config->get("island"))) {
                            $this->sendMessage($sender, "You must be in a island to use this command!");
                        }
                        else {
                            $island = $this->plugin->getIslandManager()->getOnlineIsland($config->get("island"));
                            if($island instanceof Island) {
                                $this->sendMessage($sender, "____| {$island->getOwnerName()}'s Members |____");
                                $i = 1;
                                foreach($island->getAllMembers() as $member) {
                                    $this->sendMessage($sender, "{$i}. {$member}");
                                    $i++;
                                }
                            }
                            else {
                                $this->sendMessage($sender, "You must be in a island to use this command!!");
                            }
                        }
                        break;
                    case "disband":
                        $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                        if(empty($config->get("island"))) {
                            $this->sendMessage($sender, "You must be in a island to disband it!");
                        }
                        else {
                            $island = $this->plugin->getIslandManager()->getOnlineIsland($config->get("island"));
                            if($island instanceof Island) {
                                if($island->getOwnerName() == strtolower($sender->getName())) {
                                    foreach($island->getAllMembers() as $member) {
                                        $memberConfig = new Config($this->plugin->getDataFolder() . "users" . DIRECTORY_SEPARATOR . $member . ".json", Config::JSON);
                                        $memberConfig->set("island", "");
                                        $memberConfig->save();
                                    }
                                    $this->plugin->getIslandManager()->removeIsland($island);
                                    $this->plugin->getResetHandler()->addResetTimer($sender);
                                    $this->sendMessage($sender, "You successfully deleted the island!");
                                }
                                else {
                                    $this->sendMessage($sender, "You must be the owner to disband the island!");
                                }
                            }
                            else {
                                $this->sendMessage($sender, "You must be in a island to disband it!!");
                            }
                        }
                        break;
                    case "makeleader":
                        $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                        if(empty($config->get("island"))) {
                            $this->sendMessage($sender, "You must be in a island to set a new leader!");
                        }
                        else {
                            $island = $this->plugin->getIslandManager()->getOnlineIsland($config->get("island"));
                            if($island instanceof Island) {
                                if($island->getOwnerName() == strtolower($sender->getName())) {
                                    if(isset($args[1])) {
                                        $player = $this->plugin->getServer()->getPlayer($args[1]);
                                        if($player instanceof Player and $player->isOnline()) {
                                            $playerConfig = $this->plugin->getSkyBlockManager()->getPlayerConfig($player);
                                            $playerIsland = $this->plugin->getIslandManager()->getOnlineIsland($playerConfig->get("island"));
                                            if($island == $playerIsland) {
                                                $island->setOwnerName($player);
                                                $island->addPlayer($player);
                                                $this->sendMessage($sender, "You sent the ownership to {$player->getName()}");
                                                $this->sendMessage($player, "You get your island ownership by {$sender->getName()}");
                                            }
                                            else {
                                                $this->sendMessage($sender, "The player should be on your island!");
                                            }
                                        }
                                        else {
                                            $this->sendMessage($sender, "{$args[1]} isn't a valid player!");
                                        }
                                    }
                                    else {
                                        $this->sendMessage($sender, "Usage: /skyblock makeleader <player>");
                                    }
                                }
                                else {
                                    $this->sendMessage($sender, "You must be the island leader to do this!");
                                }
                            }
                            else {
                                $this->sendMessage($sender, "You must be in a island to set a new leader!!");
                            }
                        }
                        break;
                    case "leave":
                        $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                        if(empty($config->get("island"))) {
                            $this->sendMessage($sender, "You must be in a island to leave it!");
                        }
                        else {
                            $island = $this->plugin->getIslandManager()->getOnlineIsland($config->get("island"));
                            if($island instanceof Island) {
                                if($island->getOwnerName() == strtolower($sender->getName())) {
                                    $this->sendMessage($sender, "You cannot leave a island if your the owner! Maybe you can try use /skyblock disband");
                                }
                                else {
                                    $this->plugin->getChatHandler()->removePlayerFromChat($sender);
                                    $config->set("island", "");
                                    $config->save();
                                    $island->removeMember(strtolower($sender->getName()));
                                    $this->sendMessage($sender, "You leave the island!!");
                                }
                            }
                            else {
                                $this->sendMessage($sender, "You must be in a island to leave it!!");
                            }
                        }
                        break;
                    case "remove":
                        $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                        if(empty($config->get("island"))) {
                            $this->sendMessage($sender, "You must be in a island to leave it!");
                        }
                        else {
                            $island = $this->plugin->getIslandManager()->getOnlineIsland($config->get("island"));
                            if($island instanceof Island) {
                                if($island->getOwnerName() == strtolower($sender->getName())) {
                                    if(isset($args[1])) {
                                        if(in_array(strtolower($args[1]), $island->getMembers())) {
                                            $island->removeMember(strtolower($args[1]));
                                            $player = $this->plugin->getServer()->getPlayerExact($args[1]);
                                            if($player instanceof Player and $player->isOnline()) {
                                                $this->plugin->getChatHandler()->removePlayerFromChat($player);
                                            }
                                            $this->sendMessage($sender, "{$args[1]} was removed from your team!");
                                        }
                                        else {
                                            $this->sendMessage($sender, "{$args[1]} isn't a player of your island!");
                                        }
                                    }
                                    else {
                                        $this->sendMessage($sender, "Usage: /skyblock remove <player>");
                                    }
                                }
                                else {
                                    $this->sendMessage($sender, "You must be the island owner to do this!");
                                }
                            }
                            else {
                                $this->sendMessage($sender, "You must be in a island to leave it!!");
                            }
                        }
                        break;
                    case "tp":
                        if(isset($args[1])) {
                            $island = $this->plugin->getIslandManager()->getIslandByOwner($args[1]);
                            if($island instanceof Island) {
                                if($island->isLocked()) {
                                    $this->sendMessage($sender, "This island is locked, you cannot join it!");
                                }
                                else {
                                    $sender->teleport(new Position(15, 7, 10, $this->plugin->getServer()->getLevelByName($island->getIdentifier())));
                                    $this->sendMessage($sender, "You joined the island successfully");
                                }
                            }
                            else {
                                $this->sendMessage($sender, "At least one island member must be active if you want see the island!");
                            }
                        }
                        else {
                            $this->sendMessage($sender, "Usage: /skyblock tp <owner name>");
                        }
                        break;
                    case "reset":
                        $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                        if(empty($config->get("island"))) {
                            $this->sendMessage($sender, "You must be in a island to reset it!");
                        }
                        else {
                            $island = $this->plugin->getIslandManager()->getOnlineIsland($config->get("island"));
                            if($island instanceof Island) {
                                if($island->getOwnerName() == strtolower($sender->getName())) {
                                    $reset = $this->plugin->getResetHandler()->getResetTimer($sender);
                                    if($reset instanceof Reset) {
                                        $minutes = Utils::printSeconds($reset->getTime());
                                        $this->sendMessage($sender, "You'll be able to reset your island again in {$minutes} minutes");
                                    }
                                    else {
                                        foreach($island->getAllMembers() as $member) {
                                            $memberConfig = new Config($this->plugin->getDataFolder() . "users" . DIRECTORY_SEPARATOR . $member . ".json", Config::JSON);
                                            $memberConfig->set("island", "");
                                            $memberConfig->save();
                                        }
                                        $generator = $island->getGenerator();
                                        $this->plugin->getIslandManager()->removeIsland($island);
                                        $this->plugin->getResetHandler()->addResetTimer($sender);
                                        $this->plugin->getSkyBlockManager()->generateIsland($sender, $generator);
                                        $this->sendMessage($sender, "You successfully reset the island!");
                                    }
                                }
                                else {
                                    $this->sendMessage($sender, "You must be the owner to reset the island!");
                                }
                            }
                            else {
                                $this->sendMessage($sender, "You must be in a island to reset it!!");
                            }
                        }
                        break;
                    case "info":
                        $commands = [
                            "info" => "Show skyblock command info",
                            "create" => "Create a new island",
                            "join" => "Teleport you to your island",
                            "expel" => "Kick someone from your island",
                            "lock" => "Lock/unlock your island, then nobody/everybody will be able to join",
                            "sethome" => "Set your island home",
                            "home" => "Teleport you to your island home",
                            "members" => "Show all members of your island",
                            "tp <ownerName>" => "Teleport you to a island that isn't yours",
                            "invite" => "Invite a player to be member of your island",
                            "accept/reject <sender name>" => "Accept/reject an invitation",
                            "leave" => "Leave your island",
                            "remove" => "Remove your island",
                            "makeleader" => "Transfer island ownership",
                            "teamchat" => "Change your chat to your island chat"
                        ];
                        foreach($commands as $command => $description) {
                            $sender->sendMessage(TextFormat::RED . "/skyblock {$command}: " . TextFormat::YELLOW . $description);
                        }
                        break;
                    case "teamchat":
                        if($this->plugin->getChatHandler()->isInChat($sender)) {
                            $this->plugin->getChatHandler()->removePlayerFromChat($sender);
                            $this->sendMessage($sender, "You successfully left your team chat!");
                        }
                        else {
                            $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                            if(empty($config->get("island"))) {
                                $this->sendMessage($sender, "You must be in a island to use this command!");
                            }
                            else {
                                $island = $this->plugin->getIslandManager()->getOnlineIsland($config->get("island"));
                                if($island instanceof Island) {
                                    $this->plugin->getChatHandler()->addPlayerToChat($sender, $island);
                                    $this->sendMessage($sender, "You joined your team chat room");
                                }
                                else {
                                    $this->sendMessage($sender, "You must be in a island to use this command!!");
                                }
                            }
                        }
                        break;
                    default:
                        $this->sendMessage($sender, "Use /skyblock info if you don't know how to use the command!");
                        break;
                }
            }
            else {
                $this->sendMessage($sender, "Use /skyblock info if you don't know how to use the command!");
            }
        }
        else {
            $sender->sendMessage("Please, run this command in game.");
        }
    }

}