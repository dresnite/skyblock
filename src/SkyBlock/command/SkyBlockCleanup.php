<?php

namespace SkyBlock\command;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\Level;
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
		$validWorlds = ["ender", "nether", "pvp", "pvpisland", "skyblock", "world"];
		$userConfigs = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->usersDir, \RecursiveDirectoryIterator::SKIP_DOTS),
			\RecursiveIteratorIterator::CHILD_FIRST);
		foreach($userConfigs as $configFile) {
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
			if(!in_array($worldName, $validWorlds)){
				$world = $this->plugin->getServer()->getLevelByName($worldName);
				if($world instanceof Level) {
					$this->plugin->getServer()->unloadLevel($world);
				}
				$config = $this->islandsDir . $worldName . ".json";

				$files = new \RecursiveIteratorIterator(
					new \RecursiveDirectoryIterator($worldFolder . DIRECTORY_SEPARATOR, \RecursiveDirectoryIterator::SKIP_DOTS),
					\RecursiveIteratorIterator::CHILD_FIRST
				);

				foreach ($files as $fileinfo) {
					$todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
					$todo($fileinfo->getRealPath());
				}

				rmdir($worldFolder . DIRECTORY_SEPARATOR);
				if(is_file($config)){
					unlink($config);
				}
			}
		}

	}
}