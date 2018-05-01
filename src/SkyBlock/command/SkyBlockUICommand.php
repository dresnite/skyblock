<?php

namespace SkyBlock\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use SkyBlock\island\IslandManager;
use SkyBlock\SkyBlock;
use SkyBlock\ui\SkyBlockForms;

class SkyBlockUICommand extends Command{

	/**
	 * @var IslandManager
	 */
	private $islandManager;

	/** @var SkyBlock */
	private $plugin;

	/** @var SkyBlockForms */
	private $ui;

	/**
	 * SkyBlockCommand constructor.
	 *
	 * @param SkyBlock $plugin
	 */
	public function __construct(SkyBlock $plugin){
		$this->plugin = $plugin;
		$this->islandManager = $plugin->getIslandManager();
		$this->ui = $plugin->getUserInterface();
		parent::__construct("skyblock", "SkyBlock SkyBlock command", "§cUsage: /sb", ["sb"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if($sender instanceof Player){
			if(empty($args)){
				$onIsland = false;
				foreach($this->islandManager->getOnlineIslands() as $testIsland){
					if($testIsland->isOnIsland($sender)){
						$onIsland = true;
						$island = $testIsland;
						$islandOwner = strtolower($island->getOwnerName());
						break;
					}
				}
				if($onIsland){
					$playerName = strtolower($sender->getName());
					if(strcmp($playerName, $islandOwner) === 0){
						$this->ui->ownerUI($sender);
						return;
					}
					if($island->isMember($sender)){
						$this->ui->memberUI($sender);
						return;
					}
				}
				$this->ui->guestUI($sender);
				return;
			}
		}else{
			$sender->sendMessage("Please run this command in game, not via console.");
		}
		return;
	}
}