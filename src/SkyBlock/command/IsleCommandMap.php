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

namespace SkyBlock\command;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use SkyBlock\command\defaults\AcceptCommand;
use SkyBlock\command\defaults\ChatCommand;
use SkyBlock\command\defaults\CreateCommand;
use SkyBlock\command\defaults\DemoteCommand;
use SkyBlock\command\defaults\DenyCommand;
use SkyBlock\command\defaults\DisbandCommand;
use SkyBlock\command\defaults\FireCommand;
use SkyBlock\command\defaults\HelpCommand;
use SkyBlock\command\defaults\InviteCommand;
use SkyBlock\command\defaults\JoinCommand;
use SkyBlock\command\defaults\KickCommand;
use SkyBlock\command\defaults\LeaveCommand;
use SkyBlock\command\defaults\LockCommand;
use SkyBlock\command\defaults\MembersCommand;
use SkyBlock\command\defaults\PromoteCommand;
use SkyBlock\command\defaults\SetSpawnCommand;
use SkyBlock\command\defaults\TransferCommand;
use SkyBlock\command\defaults\VisitCommand;
use SkyBlock\SkyBlock;

class IsleCommandMap extends Command {
    
    /** @var SkyBlock */
    private $plugin;
    
    /** @var IsleCommand[] */
    private $commands = [];
    
    /**
     * IsleCommandMap constructor.
     * @param SkyBlock $plugin
     */
    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
        $this->registerCommand(new HelpCommand($this));
        $this->registerCommand(new CreateCommand($this));
        $this->registerCommand(new JoinCommand());
        $this->registerCommand(new LockCommand());
        $this->registerCommand(new ChatCommand());
        $this->registerCommand(new VisitCommand($this));
        $this->registerCommand(new LeaveCommand());
        $this->registerCommand(new MembersCommand());
        $this->registerCommand(new InviteCommand($this));
        $this->registerCommand(new AcceptCommand());
        $this->registerCommand(new DenyCommand());
        $this->registerCommand(new DisbandCommand($this));
        $this->registerCommand(new KickCommand($this));
        $this->registerCommand(new FireCommand($this));
        $this->registerCommand(new PromoteCommand($this));
        $this->registerCommand(new DemoteCommand($this));
        $this->registerCommand(new SetSpawnCommand());
        $this->registerCommand(new TransferCommand($this));
        parent::__construct("isle", "SkyBlock command", "Usage: /is", [
            "island",
            "is",
            "isle",
            "sb",
            "skyblock"
        ]);
        $plugin->getServer()->getCommandMap()->register("isle", $this);
    }
    
    /**
     * @return SkyBlock
     */
    public function getPlugin(): SkyBlock {
        return $this->plugin;
    }
    
    /**
     * @return IsleCommand[]
     */
    public function getCommands(): array {
        return $this->commands;
    }
    
    /**
     * @param string $alias
     * @return null|IsleCommand
     */
    public function getCommand(string $alias): ?IsleCommand {
        foreach($this->commands as $key => $command) {
            if(in_array(strtolower($alias), $command->getAliases()) or $alias == $command->getName()) {
                return $command;
            }
        }
        return null;
    }
    
    /**
     * @param IsleCommand $command
     */
    public function registerCommand(IsleCommand $command) {
        $this->commands[] = $command;
    }
    
    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender instanceof Player) {
            $sender->sendMessage("Please, run this command in game");
            return;
        }
        
        $session = $this->plugin->getSessionManager()->getSession($sender);
        if(isset($args[0]) and $this->getCommand($args[0]) != null){
            $this->getCommand(array_shift($args))->onCommand($session, $args);
        } else {
            $session->sendTranslatedMessage("TRY_USING_HELP");
        }
    }
    
}