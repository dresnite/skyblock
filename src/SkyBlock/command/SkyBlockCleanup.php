<?php

namespace SkyBlock\command;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\level\Level;
use pocketmine\Server;
use pocketmine\utils\Config;
use SkyBlock\SkyBlock;

class SkyBlockCleanup extends Command{
	private $plugin;
	private $worldsDir;
	private $usersDir;
	private $islandsDir;

	public function __construct(SkyBlock $plugin){
		$this->plugin = $plugin;
		$this->worldsDir = $plugin->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR;
		$this->usersDir = $plugin->getDataFolder() . "users" . DIRECTORY_SEPARATOR;
		$this->islandsDir = $plugin->getDataFolder() . "islands" . DIRECTORY_SEPARATOR;
		parent::__construct("sbcleanup", "Cleans unused worlds and configuration files.", "§cUsage: /sbcleanup");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if($sender instanceof ConsoleCommandSender){
			$validWorlds = $this->plugin->getConfig()->get("protected-worlds");
			if(!empty($validWorlds)){
				$counter = 0;
				$userConfigs = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->usersDir, \RecursiveDirectoryIterator::SKIP_DOTS),
					\RecursiveIteratorIterator::CHILD_FIRST);
				foreach($userConfigs as $configFile){
					if(!$configFile->isDir()){
						$config = new Config($configFile->getRealPath(), Config::JSON);
						$island = $config->get("island");
						array_push($validWorlds, $island);
					}
				}
				$worldFolders = glob($this->worldsDir . "*");
				foreach($worldFolders as $worldFolder){
					$string = $this->worldsDir;
					$worldName = str_replace($string, "", $worldFolder);
					if($worldName !== Server::getInstance()->getDefaultLevel()->getName() and !in_array($worldName, $validWorlds)){
						$world = $this->plugin->getServer()->getLevelByName($worldName);
						if($world instanceof Level){
							$this->plugin->getServer()->unloadLevel($world);
						}
						$config = $this->islandsDir . $worldName . ".json";

						$files = new \RecursiveIteratorIterator(
							new \RecursiveDirectoryIterator($worldFolder . DIRECTORY_SEPARATOR, \RecursiveDirectoryIterator::SKIP_DOTS),
							\RecursiveIteratorIterator::CHILD_FIRST
						);

						foreach($files as $fileinfo){
							$todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
							$todo($fileinfo->getRealPath());
							$counter++;
						}

						rmdir($worldFolder . DIRECTORY_SEPARATOR);
						if(is_file($config)){
							unlink($config);
						}
					}
				}
				$sender->sendMessage("sbcleanup: Completed!  Removed $counter files and directories.");
			}else{
				$sender->sendMessage("The configuration file needs to be updated before using the sbcleanup command.");
			}
		} else {
			$sender->sendMessage("This command must be run from the console.");
		}
	}
}