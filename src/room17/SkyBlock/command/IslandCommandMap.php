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

namespace room17\SkyBlock\command;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use room17\SkyBlock\command\presets\AcceptCommand;
use room17\SkyBlock\command\presets\BlocksCommand;
use room17\SkyBlock\command\presets\CategoryCommand;
use room17\SkyBlock\command\presets\ChatCommand;
use room17\SkyBlock\command\presets\CooperateCommand;
use room17\SkyBlock\command\presets\CreateCommand;
use room17\SkyBlock\command\presets\DemoteCommand;
use room17\SkyBlock\command\presets\DenyCommand;
use room17\SkyBlock\command\presets\DisbandCommand;
use room17\SkyBlock\command\presets\FireCommand;
use room17\SkyBlock\command\presets\HelpCommand;
use room17\SkyBlock\command\presets\InviteCommand;
use room17\SkyBlock\command\presets\JoinCommand;
use room17\SkyBlock\command\presets\KickCommand;
use room17\SkyBlock\command\presets\LeaveCommand;
use room17\SkyBlock\command\presets\LockCommand;
use room17\SkyBlock\command\presets\MembersCommand;
use room17\SkyBlock\command\presets\PromoteCommand;
use room17\SkyBlock\command\presets\SetSpawnCommand;
use room17\SkyBlock\command\presets\TransferCommand;
use room17\SkyBlock\command\presets\VisitCommand;
use room17\SkyBlock\SkyBlock;
use room17\SkyBlock\utils\MessageContainer;

class IslandCommandMap extends Command implements PluginIdentifiableCommand {
    
    /** @var SkyBlock */
    private $plugin;
    
    /** @var IslandCommand[] */
    private $commands = [];
    
    /**
     * IslandCommandMap constructor.
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
        $this->registerCommand(new CategoryCommand());
        $this->registerCommand(new BlocksCommand());
        $this->registerCommand(new CooperateCommand($this));
        parent::__construct("isle", "SkyBlock command", "Usage: /is", [
            "island",
            "is",
            "isle",
            "sb",
            "skyblock"
        ]);
        $plugin->getServer()->getCommandMap()->register("skyblock", $this);
    }
    
    /**
     * @return SkyBlock|Plugin
     */
    public function getPlugin(): Plugin {
        return $this->plugin;
    }
    
    /**
     * @return IslandCommand[]
     */
    public function getCommands(): array {
        return $this->commands;
    }
    
    /**
     * @param string $alias
     * @return null|IslandCommand
     */
    public function getCommand(string $alias): ?IslandCommand {
        foreach($this->commands as $key => $command) {
            if(in_array(strtolower($alias), $command->getAliases()) or $alias == $command->getName()) {
                return $command;
            }
        }
        return null;
    }
    
    /**
     * @param IslandCommand $command
     */
    public function registerCommand(IslandCommand $command) {
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
        if(isset($args[0]) and $this->getCommand($args[0]) != null) {
            $this->getCommand(array_shift($args))->onCommand($session, $args);
        } else {
            $session->sendTranslatedMessage(new MessageContainer("TRY_USING_HELP"));
        }
    }
    
}