<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace GiantQuartz\SkyBlock\command;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use ReflectionException;
use GiantQuartz\SkyBlock\command\presets\AcceptCommand;
use GiantQuartz\SkyBlock\command\presets\BlocksCommand;
use GiantQuartz\SkyBlock\command\presets\CategoryCommand;
use GiantQuartz\SkyBlock\command\presets\ChatCommand;
use GiantQuartz\SkyBlock\command\presets\CooperateCommand;
use GiantQuartz\SkyBlock\command\presets\CreateCommand;
use GiantQuartz\SkyBlock\command\presets\DemoteCommand;
use GiantQuartz\SkyBlock\command\presets\DenyCommand;
use GiantQuartz\SkyBlock\command\presets\DisbandCommand;
use GiantQuartz\SkyBlock\command\presets\FireCommand;
use GiantQuartz\SkyBlock\command\presets\HelpCommand;
use GiantQuartz\SkyBlock\command\presets\InviteCommand;
use GiantQuartz\SkyBlock\command\presets\JoinCommand;
use GiantQuartz\SkyBlock\command\presets\BanishCommand;
use GiantQuartz\SkyBlock\command\presets\LeaveCommand;
use GiantQuartz\SkyBlock\command\presets\LockCommand;
use GiantQuartz\SkyBlock\command\presets\MembersCommand;
use GiantQuartz\SkyBlock\command\presets\PromoteCommand;
use GiantQuartz\SkyBlock\command\presets\SetSpawnCommand;
use GiantQuartz\SkyBlock\command\presets\TransferCommand;
use GiantQuartz\SkyBlock\command\presets\VisitCommand;
use GiantQuartz\SkyBlock\session\SessionLocator;
use GiantQuartz\SkyBlock\SkyBlock;
use GiantQuartz\SkyBlock\utils\message\MessageContainer;

class IslandCommandMap extends Command implements PluginIdentifiableCommand {

    /** @var SkyBlock */
    private $plugin;

    /** @var IslandCommand[] */
    private $commands = [];

    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
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

    public function getCommand(string $alias): ?IslandCommand {
        foreach($this->commands as $key => $command) {
            if(in_array(strtolower($alias), $command->getAliases()) or $alias == $command->getName()) {
                return $command;
            }
        }
        return null;
    }

    public function registerCommand(IslandCommand $command): void {
        $this->commands[$command->getName()] = $command;
    }

    public function unregisterCommand(string $commandName): void {
        if(isset($this->commands[$commandName])) {
            unset($this->commands[$commandName]);
        }
    }

    public function registerDefaultCommands(): void {
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
        $this->registerCommand(new BanishCommand());
        $this->registerCommand(new FireCommand($this));
        $this->registerCommand(new PromoteCommand($this));
        $this->registerCommand(new DemoteCommand($this));
        $this->registerCommand(new SetSpawnCommand());
        $this->registerCommand(new TransferCommand($this));
        $this->registerCommand(new CategoryCommand());
        $this->registerCommand(new BlocksCommand());
        $this->registerCommand(new CooperateCommand($this));
    }

    /**
     * @throws ReflectionException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender instanceof Player) {
            $sender->sendMessage("Please, run this command in game");
            return;
        }

        $session = SessionLocator::getSession($sender);
        if(isset($args[0]) and $this->getCommand($args[0]) != null) {
            $this->getCommand(array_shift($args))->onCommand($session, $args);
        } else {
            $session->sendTranslatedMessage(new MessageContainer("TRY_USING_HELP"));
        }
    }

}