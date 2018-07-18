<?php
/*
 * Copyright (C) Andrés Arias - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

namespace SkyBlock\command;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
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
        parent::__construct("isle", "SkyBlock command", "Usage: /is", [
            "isle",
            "island",
            "sb",
            "skyblock"
        ]);
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
        foreach($this->commands as $key => $subcommand) {
            if(in_array(strtolower($alias), $subcommand->getAliases())) {
                return $subcommand;
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
            // $session->sendMessage("TRY_USING_HELP");
        }
    }
    
}