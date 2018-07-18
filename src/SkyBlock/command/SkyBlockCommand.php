<?php
namespace SkyBlock\command;

use SkyBlock\SkyBlockUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use SkyBlock\island\Island;
use SkyBlock\SkyBlock;
use SkyBlock\reset\Reset;

class SkyBlockCommand extends Command {
    /** @var SkyBlock */
    private $plugin;

    /**
     * SkyBlockCommand constructor.
     *
     * @param SkyBlock $plugin
     */
    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
        parent::__construct("island", "SkyBlock command", "§cUsage: /skyblock", ["is"]);
    }

    public function sendMessage(Player $sender, $message) {
        $sender->sendMessage(TextFormat::AQUA . TextFormat::BOLD . "[" . TextFormat::GREEN . "SkyBlockPE" . TextFormat::AQUA . "] " . TextFormat::RESET . TextFormat::DARK_GREEN . $message);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if($sender instanceof Player) {
            if(isset($args[0])) {
                switch($args[0]){
                    case "join":
                        if($sender->hasPermission('sbpe.cmd.join') or $sender->hasPermission('sbpe')) {
                            $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                            if(empty($config->get("island"))) {
                                $this->sendMessage($sender, "§l§c✖§f §cYou Don't have an island!");
                            }
                            else {
                                $island = $this->plugin->getIslandManager()->getOnlineIsland($config->get("island"));
                                if($island instanceof Island) {
                                    $island->addPlayer($sender);
                                    $level = $this->plugin->getServer()->getLevelByName($island->getIdentifier());
                                    $spawnPoint = $level->getSpawnLocation();
                                    $sender->teleport($spawnPoint);
                                    $this->sendMessage($sender, "§l§a✔§fYou were teleported to your island home succesfully");
                                }
                                else {
                                    $this->sendMessage($sender, "§l§c✖§f §cYou Don't have an island!!");
                                }
                            }
                        }
                        break;
                    case "create":
                        if($sender->hasPermission('sbpe.cmd.create') or $sender->hasPermission('sbpe')) {
                            $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                            if(empty($config->get("island"))) {
                                $reset = $this->plugin->getResetHandler()->getResetTimer($sender);
                                if($reset instanceof Reset) {
                                    $minutes = SkyBlockUtils::printSeconds($reset->getTime());
                                    $this->sendMessage($sender, "§l§l§a✔§f➡§fYou'll be able to create a new island in §4{$minutes} §cminutes");
                                }
                                else {
                                    $skyBlockManager = $this->plugin->getGeneratorManager();
                                    if(isset($args[1])) {
                                        if($skyBlockManager->isGenerator($args[1])) {
                                            $this->plugin->getSkyBlockManager()->generateIsland($sender, $args[1]);
                                            $this->sendMessage($sender, "§l§a✔§fYou successfully created a {$skyBlockManager->getGeneratorIslandName($args[1])} island!");
                                        }
                                        else {
                                            $this->sendMessage($sender, "§l§c✖§f §cThat isn't a valid SkyBlock generator!");
                                        }
                                    }
                                    else {
                                        $this->plugin->getSkyBlockManager()->generateIsland($sender, "basic");
                                        $this->sendMessage($sender, "§l§a✔§fYou successfully created a island!");
                                    }
                                }
                            }
                            else {
                                $this->sendMessage($sender, "§l§c✖§f §cYou already have a SkyBlock island!");
                            }
                        }
                        break;
                    case "home":
                        $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                        if(empty($config->get("island"))) {
                            $this->sendMessage($sender, "§c✖§f You do not have an island!");
                        }
                        else {
                            $island = $this->plugin->getIslandManager()->getOnlineIsland($config->get("island"));
                            if($island instanceof Island) {
                                $home = $island->getHomePosition();
                                if($home instanceof Position) {
                                    $sender->teleport($home);
                                    $this->sendMessage($sender, "§a✔§f You have been teleported to your island home");
                                }
                                else {
                                    $this->sendMessage($sender, "§c➡§f Your island is not home yet!");
                                }
                            }
                            else {
                                $this->sendMessage($sender, "§c✖§f You do not have an island!");
                            }
                        }
                        break;
                    case "sethome":
                        $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                        if(empty($config->get("island"))) {
                            $this->sendMessage($sender, "§c✖§f You do not have an island!");
                        }
                        else {
                            $island = $this->plugin->getIslandManager()->getOnlineIsland($config->get("island"));
                            if($island instanceof Island) {
                                if($island->getOwnerName() == strtolower($sender->getName())) {
                                    if($sender->getLevel()->getName() == $config->get("island")) {
                                        $island->setHomePosition($sender->getPosition());
                                        $this->sendMessage($sender, "§a✔§f You have successfully set up your island home!");
                                    }
                                    else {
                                        $this->sendMessage($sender, "§c✖§f You have to stay in your island to set home!");
                                    }
                                }
                                else {
                                    $this->sendMessage($sender, "§c➡§f You have to be the island's leader to do this!");
                                }
                            }
                            else {
                                $this->sendMessage($sender, "§c✖§f You do not have an island!");
                            }
                        }
                        break;

                    case "kick":
                    case "expel":
                        if($sender->hasPermission('sbpe.cmd.kick') or $sender->hasPermission('sbpe')) {
                            $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                            if(empty($config->get("island"))) {
                                $this->sendMessage($sender, "§l§c✖§f §cYou Don't have an island");
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
                                                    $this->sendMessage($sender, "{$player->getName()} §chas been kicked from your island!");
                                                }
                                                else {
                                                    $this->sendMessage($sender, "§l§c✖§f §cThe player isn't in your island!");
                                                }
                                            }
                                            else {
                                                $this->sendMessage($sender, "§l§c✖§f §cThat isn't a valid player");
                                            }
                                        }
                                        else {
                                            $this->sendMessage($sender, "§cUsage: /skyblock expel <name>");
                                        }
                                    }
                                    else {
                                        $this->sendMessage($sender, "§l§c✖§f §cYou must be the island owner to expel/kick anyone");
                                    }
                                }
                                else {
                                    $this->sendMessage($sender, "§l§c✖§f §cYou Don't have an island");
                                }
                            }
                        }
                        break;
                    case "lock":
                        if($sender->hasPermission('sbpe.cmd.lock') or $sender->hasPermission('sbpe')) {
                            $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                            if(empty($config->get("island"))) {
                                $this->sendMessage($sender, "§l§c✖§f §cYou Don't have an island");
                            }
                            else {
                                $island = $this->plugin->getIslandManager()->getOnlineIsland($config->get("island"));
                                if($island instanceof Island) {
                                    if($island->getOwnerName() == strtolower($sender->getName())) {
                                        $island->setLocked(!$island->isLocked());
                                        $locked = ($island->isLocked()) ? "locked" : "unlocked";
                                        $this->sendMessage($sender, "§l§a✔§fYour island has been {$locked}!");
                                    }
                                    else {
                                        $this->sendMessage($sender, "§l§c✖§f §cYou must be the island owner to do this!");
                                    }
                                }
                                else {
                                    $this->sendMessage($sender, "§l§c✖§f §cYou haven't got a island!");
                                }
                            }
                        }
                        break;
                    case "invite":
                        if($sender->hasPermission('sbpe.cmd.invite') or $sender->hasPermission('sbpe')) {
                            $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                            if(empty($config->get("island"))) {
                                $this->sendMessage($sender, "§l§c✖§f §cYou Don't have an island");
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
                                                    $this->sendMessage($sender, "§l§a✔§fYou sent a invitation to §2{$player->getName()} §l§a✔§fsuccesfully!");
                                                    $this->sendMessage($player, "{$sender->getName()} §l§a✔§finvited you to his island! §2Do /skyblock <accept/reject> {$sender->getName()}");
                                                }
                                                else {
                                                    $this->sendMessage($sender, "§l§c✖§f §cThis player is already in a island!");
                                                }
                                            }
                                            else {
                                                $this->sendMessage($sender, "§l§c✖§f §c{$args[1]} isn't a valid player!");
                                            }
                                        }
                                        else {
                                            $this->sendMessage($sender, "§cUsage: /skyblock invite <player>");
                                        }
                                    }
                                    else {
                                        $this->sendMessage($sender, "§l§c✖§f §cYou must be the island owner to do this!");
                                    }
                                }
                                else {
                                    $this->sendMessage($sender, "§l§c✖§f §cYou Don't have an island");
                                }
                            }
                        }
                        break;
                    case "accept":
                        if($sender->hasPermission('sbpe.cmd.invite.accept') or $sender->hasPermission('sbpe')) {
                            if(isset($args[1])) {
                                $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                                if(empty($config->get("island"))) {
                                    $player = $this->plugin->getServer()->getPlayer($args[1]);
                                    if($player instanceof Player and $player->isOnline()) {
                                        $invitation = $this->plugin->getInvitationHandler()->getInvitationBySender($player);
                                        if($invitation !== null) {
                                            foreach($invitation as $invite) {
                                                if($invite->getReceiver()->getUniqueId() === $sender->getUniqueId()) {
                                                    $invite->accept();
                                                }
                                            }
                                        }
                                        else {
                                            $this->sendMessage($sender, "§l§c✖§f §cYou don't have an invitation from {$player->getName()}");
                                        }
                                    }
                                    else {
                                        $this->sendMessage($sender, "§l§c✖§f §c{$args[1]} is not a valid player");
                                    }
                                }
                                else {
                                    $this->sendMessage($sender, "§l§c✖§f §cYou cannot be in a island if you want join another island!");
                                }
                            }
                            else {
                                $this->sendMessage($sender, "§cUsage: /skyblock accept <sender name>");
                            }
                        }
                        break;
                    case "deny":
                    case "reject":
                        if($sender->hasPermission('sbpe.cmd.invite.deny') or $sender->hasPermission('sbpe')) {
                            if(isset($args[1])) {
                                $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                                if(empty($config->get("island"))) {
                                    $player = $this->plugin->getServer()->getPlayer($args[1]);
                                    if($player instanceof Player and $player->isOnline()) {
                                        $invitation = $this->plugin->getInvitationHandler()->getInvitationBySender($player);
                                        if($invitation !== null) {
                                            foreach($invitation as $invite) {
                                                if($invite->getReceiver()->getUniqueId() === $sender->getUniqueId()) {
                                                    $invite->deny();
                                                }
                                            }
                                        }
                                        else {
                                            $this->sendMessage($sender, "§l§c✖§f §cYou haven't got a invitation from {$player->getName()}");
                                        }
                                    }
                                    else {
                                        $this->sendMessage($sender, "§l§c✖§f §c{$args[1]} is not a valid player");
                                    }
                                }
                                else {
                                    $this->sendMessage($sender, "§l§c✖§f §cYou cannot be in a island if you want reject another island!");
                                }
                            }
                            else {
                                $this->sendMessage($sender, "§cUsage: /skyblock accept <sender name>");
                            }
                        }
                        break;
                    case "members":
                        if($sender->hasPermission('sbpe.cmd.members') or $sender->hasPermission('sbpe')) {
                            $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                            if(empty($config->get("island"))) {
                                $this->sendMessage($sender, "§l§c✖§f §cYou must be in a island to use this command!");
                            }
                            else {
                                $island = $this->plugin->getIslandManager()->getOnlineIsland($config->get("island"));
                                if($island instanceof Island) {
                                    $this->sendMessage($sender, "____| {$island->getOwnerName()}'s §l§a✔§fIsland Members |____");
                                    $i = 1;
                                    foreach($island->getAllMembers() as $member) {
                                        $this->sendMessage($sender, "{$i}. {$member}");
                                        $i++;
                                    }
                                }
                                else {
                                    $this->sendMessage($sender, "§l§c✖§f §cYou must be in a island to use this command!");
                                }
                            }
                        }
                        break;
                    case "disband":
                        if($sender->hasPermission('sbpe.cmd.disband') or $sender->hasPermission('sbpe')) {
                            $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                            if(empty($config->get("island"))) {
                                $this->sendMessage($sender, "§l§c✖§f §cYou must be in a island to disband it!");
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
                                        $this->sendMessage($sender, "§l§a✔§fYou successfully deleted the island!");
                                    }
                                    else {
                                        $this->sendMessage($sender, "§l§c✖§f §cYou must be the owner to disband the island!");
                                    }
                                }
                                else {
                                    $this->sendMessage($sender, "§l§c✖§f §cYou must be in a island to disband it!");
                                }
                            }
                        }
                        break;
                    case "leave":
                        if($sender->hasPermission('sbpe.cmd.leave') or $sender->hasPermission('sbpe')) {
                            $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                            if(empty($config->get("island"))) {
                                $this->sendMessage($sender, "§l§c✖§f §cYou must be in a island to leave it!");
                            }
                            else {
                                $island = $this->plugin->getIslandManager()->getOnlineIsland($config->get("island"));
                                if($island instanceof Island) {
                                    if($island->getOwnerName() == strtolower($sender->getName())) {
                                        $this->sendMessage($sender, "§l§c✖§f §cYou cannot leave an island if you're the owner! Maybe you can try using /skyblock disband");
                                    }
                                    else {
                                        $sender->sendMessage("We Got There!");
                                        $this->plugin->getChatHandler()->removePlayerFromChat($sender);
                                        $config->set("island", "");
                                        $config->save();
                                        $island->removeMember(strtolower($sender->getName()));
                                        $sender->teleport($sender->getServer()->getDefaultLevel()->getSafeSpawn());
                                        $this->sendMessage($sender, "§l§a✔§fYou left the island successfully!");
                                    }
                                }
                                else {
                                    $this->sendMessage($sender, "§l§c✖§f §cYou must be in a island to leave it!");
                                }
                            }
                        }
                        break;
                    case "remove":
                        if($sender->hasPermission('sbpe.cmd.remove') or $sender->hasPermission('sbpe')) {
                            $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                            if(empty($config->get("island"))) {
                                $this->sendMessage($sender, "§l§c✖§f §cYou must be in a island to leave it!");
                            }
                            else {
                                $island = $this->plugin->getIslandManager()->getOnlineIsland($config->get("island"));
                                if($island instanceof Island) {
                                    if($island->getOwnerName() == strtolower($sender->getName())) {
                                        if(isset($args[1])) {
                                            if(in_array(strtolower($args[1]), $island->getMembers())) {
                                                $island->removeMember(strtolower($args[1]));
                                                $config = SkyBlockUtils::getUserConfig($args[1]);
                                                $config->set("island", "");
                                                $config->save();
                                                $player = $this->plugin->getServer()->getPlayerExact($args[1]);
                                                if($player instanceof Player and $player->isOnline()) {
                                                    $this->plugin->getChatHandler()->removePlayerFromChat($player);
                                                }
                                                $this->sendMessage($sender, "§2{$args[1]} §l§a✔§fwas removed from your team/island successfully!");
                                            }
                                            else {
                                                $this->sendMessage($sender, "§l§c✖§f §c{$args[1]} isn't a player of your island!");
                                            }
                                        }
                                        else {
                                            $this->sendMessage($sender, "§cUsage: /skyblock remove <player>");
                                        }
                                    }
                                    else {
                                        $this->sendMessage($sender, "§l§c✖§f §cYou must be the island owner to do this!");
                                    }
                                }
                                else {
                                    $this->sendMessage($sender, "§l§c✖§f §cYou must be in a island to leave it!");
                                }
                            }
                        }
                        break;
                    case "tp":
                        if($sender->hasPermission('sbpe.cmd.tp') or $sender->hasPermission('sbpe')) {
                            if(isset($args[1])) {
                                $island = $this->plugin->getIslandManager()->getIslandByOwner($args[1]);
                                if($island instanceof Island) {
                                    if($island->isLocked()) {
                                        $this->sendMessage($sender, "§l§c✖§f §cThis island is locked, you cannot join it!");
                                    }
                                    else {
                                        $safeSpawn = $this->plugin->getServer()->getLevelByName($island->getIdentifier())->getSafeSpawn();
                                        $sender->teleport($safeSpawn);
                                        $this->sendMessage($sender, "§l§a✔§fYou joined the island successfully");
                                    }
                                }
                                else {
                                    $this->sendMessage($sender, "§l§c✖§f §cAt least one island member must be active if you want see the island!");
                                }
                            }
                            else {
                                $this->sendMessage($sender, "§cUsage: /skyblock tp <owner name>");
                            }
                        }
                        break;
                    case "reset":
                        if($sender->hasPermission('sbpe.cmd.reset') or $sender->hasPermission('sbpe')) {
                            $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                            if(empty($config->get("island"))) {
                                $this->sendMessage($sender, "§l§c✖§f §cYou must be in a island to reset it!");
                            }
                            else {
                                $island = $this->plugin->getIslandManager()->getOnlineIsland($config->get("island"));
                                if($island instanceof Island) {
                                    if($island->getOwnerName() == strtolower($sender->getName())) {
                                        $reset = $this->plugin->getResetHandler()->getResetTimer($sender);
                                        if($reset instanceof Reset) {
                                            $minutes = SkyBlockUtils::printSeconds($reset->getTime());
                                            $this->sendMessage($sender, "§l§l§a✔§f➡§fYou'll be able to reset your island again in §d{$minutes} §l§l§a✔§f➡§fminutes");
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
                                            $this->sendMessage($sender, "§l§a✔§fYou successfully reset the island!");
                                        }
                                    }
                                    else {
                                        $this->sendMessage($sender, "§l§c✖§f §cYou must be the owner to reset the island!");
                                    }
                                }
                                else {
                                    $this->sendMessage($sender, "§l§c✖§f §cYou must be in a island to reset it!");
                                }
                            }
                        }
                        break;
                    case "version":
                    case "ver":
                        if($sender->hasPermission('sbpe.cmd.ver') or $sender->hasPermission('sbpe')) {
                            $this->sendMessage($sender, "§cNot showing infomation due to self-leak plugin.");
                        }
                        break;
                    case "help":
                        if($sender->hasPermission('sbpe.cmd.home') or $sender->hasPermission('sbpe')) {
                            $commands = [
                                "§dhelp" => "§l§b➡§fShow skyblock command info",
                                "§dcreate" => "§l§b➡§fCreate a new island",
                                "§djoin" => "§l§b➡§fTeleport you to your island",
                                "§dexpel" => "§l§b➡§fKick someone from your island",
                                "§dlock" => "§l§b➡§fLock/unlock your island, then nobody/everybody will be able to join",
                                "§dsethome" => "§l§b➡§fSet your island home",
                                "§dhome" => "§l§b➡§fTeleport you to your island home",
                                "§dmembers" => "§l§b➡§fShow all members of your island",
                                "§dtp <ownerName>" => "§l§b➡§fTeleport you to an island that isn't yours",
                                "§dinvite" => "§l§b➡§fInvite a player to be member of your island",
                                "§daccept/reject <sender name>" => "§l§l§a✔§f➡§fAccept/reject an invitation",
                                "§dleave" => "§l§b➡§fLeave your island",
                                "§ddisband" => "§l§b➡§fDelete your island",
                                "§dteamchat" => "§l§b➡§fChange the style of chat on your island",
                                "§dremove" => "§l§b➡§fRemove a player from your island",
                                "§dreset" => "§l§b➡§fReset's your island.",
                                "§dversion" => "§l§b➡§fGets Skyblock version.",

                            ];
                            $sender->sendMessage(TextFormat::DARK_GREEN . "-----------" . TextFormat::BOLD . TextFormat::AQUA . " [" . TextFormat::GREEN . "SkyBlockPE Help" . TextFormat::AQUA . "] " . TextFormat::RESET . TextFormat::DARK_GREEN . "-----------");
                            foreach($commands as $command => $description) {

                                $sender->sendMessage(TextFormat::AQUA . "/" . TextFormat::GREEN . "island {$command}: " . TextFormat::RESET . TextFormat::DARK_GREEN . $description);
                            }
                        }
                        break;
                    case "teamchat":
                        if($this->plugin->getChatHandler()->isInChat($sender)) {
                            $this->plugin->getChatHandler()->removePlayerFromChat($sender);
                            $this->sendMessage($sender, "You left the chat group successfully!");
                        }
                        else {
                            $config = $this->plugin->getSkyBlockManager()->getPlayerConfig($sender);
                            if(empty($config->get("island"))) {
                                $this->sendMessage($sender, "§c➡§f You must be in an island to use this command!");
                            }
                            else {
                                $island = $this->plugin->getIslandManager()->getOnlineIsland($config->get("island"));
                                if($island instanceof Island) {
                                    $this->plugin->getChatHandler()->addPlayerToChat($sender, $island);
                                    $this->sendMessage($sender, "§a✔§fYou joined the chat group");
                                }
                                else {
                                    $this->sendMessage($sender, "§c➡§f You must be in an island to use this command!");
                                }
                            }
                        }
                        break;
                    default:
                        $this->sendMessage($sender, "§2Use /is help for a list of skyblock commands.");

                        break;
                }
            }
            else {
                $this->sendMessage($sender, "§dUse §l§b➡§f/is help §dfor a list of skyblock commands.");
            }
        }
        else {
            $sender->sendMessage("Please run this command in game, not via console.");
        }
    }
}
