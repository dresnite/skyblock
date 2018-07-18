<?php


namespace SkyBlock\ui;


use jojoe77777\FormAPI\FormAPI;
use pocketmine\Player;
use pocketmine\Server;
use SkyBlock\invitation\Invitation;
use SkyBlock\invitation\InvitationHandler;
use SkyBlock\island\Island;
use SkyBlock\island\IslandManager;
use SkyBlock\SkyBlock;
use SkyBlock\Utils;

class SkyBlockForms{

	const COMMAND_NAME = "is ";
	const BASE_TITLE = "§l§3Sky§4Block";

	/** @var FormAPI */
	private $formAPI;

	/** @var IslandManager */
	private $islandManager;

	/** @var InvitationHandler */
	private $invitationHandler;

	/** @var Invitation[] */
	private $invitationPass;

	/**	@var string[] */
	private $ownerPass;

	/** @var SkyBlock */
	private $plugin;

	public function __construct(){
		$this->plugin = SkyBlock::getInstance();
		$this->formAPI = Server::getInstance()->getPluginManager()->getPlugin("FormAPI");
		$this->invitationHandler = $this->plugin->getInvitationHandler();
		$this->islandManager = $this->plugin->getIslandManager();
	}

	public function sendMessage(Player $sender, $message){
		$sender->sendMessage("§b§l[§aSkyBlockPE§b] §r§2" . $message);
	}

	public function ownerUI(Player $player){
		$form = $this->formAPI->createSimpleForm([$this, "handleOwnerUI"]);
		$form->setTitle(self::BASE_TITLE . "§r§2 - Owner SkyBlock");
		$form->addButton("§1Go to Island Spawn", -1, "", "join");
		$form->addButton("§1Go to Island Home",-1,"","home");
		if($this->islandManager->getIslandByOwner($player->getName())->isLocked()){
			$form->addButton("§1Unlock this Island",-1,"","lock");
		} else {
			$form->addButton("§1Lock this Island",-1,"","lock");
		}
		$form->addButton("§1Invite",-1,"","invite");
		$form->addButton("§1Kick",-1,"","kick");
		$form->addButton("§1Manage",-1,"","manage");
		$form->addButton("§1Go to Spawn",-1,"","spawn");
		$form->sendToPlayer($player);
	}

	public function handleOwnerUI(Player $player, $data){
		$result = $data;
		if($result == null){
			return;
		}
		$command = "";
		switch($result){
            case "join": $command = "join";
                break;
			case "home": $command = "home";
				break;
			case "lock": $command = "lock";
				break;
			case "kick": $this->kickUI($player);
				return;
			case "invite": $this->inviteUI($player);
				return;
			case "manage": $this->manageUI($player);
				return;
			case "spawn": $this->sendToSpawn($player);
				return;
			default: $this->handleSwitchError($player, "handleOwnerUI", $result);
				return;
		}
		$command = self::COMMAND_NAME . $command;
		$this->plugin->getServer()->getCommandMap()->dispatch($player, $command);
		return;
	}

	public function memberUI(Player $player){
		$form = $this->formAPI->createSimpleForm([$this, "handleMemberUI"]);
		$form->setTitle(self::BASE_TITLE . "§r§2 - Member SkyBlock");
		$form->addButton("§1§1Go to Island Spawn", -1, "", "join");
		$form->addButton("§1Go to Island Home",-1,"","home");
		$form->addButton("§1Quit Being a Member",-1,"","leave");
		$form->addButton("§1Go to Spawn",-1,"","spawn");
		$form->sendToPlayer($player);
	}

	public function handleMemberUI(Player $player, $data){
		$result = $data;
		if($result === null){
			return;
		}
		$command = "";
		switch($result){
            case "join": $command = "join";
                break;
			case "home": $command = "home";
				break;

			case "leave": $this->leaveUI($player);
				return;

			case "spawn": $this->sendToSpawn($player);
				return;

			default: $this->handleSwitchError($player, "handleMemberUI", $result);
				return;
		}
		$command = self::COMMAND_NAME . $command;
		$this->plugin->getServer()->getCommandMap()->dispatch($player, $command);
		return;
	}

	public function guestUI(Player $player){
		$form = $this->formAPI->createSimpleForm([$this, "handleGuestUI"]);
		$form->setTitle(self::BASE_TITLE . "§r§2 - SkyBlock");
		$config = $this->plugin->getSkyBlockManager()->getPlayerConfig($player);
		if(empty($config->get("island"))){
			$form->addButton("§1Create an Island",-1,"","create");
		} else {
			$form->addButton("§1Go to Island Spawn",-1,"","join");
            $form->addButton("§1Go to Island Home",-1,"","home");
			if($this->islandManager->getIslandByOwner($player->getName()) !== null){
				$form->addButton("§1Manage Your Island", -1, "", "manage");
			}
		}
		$form->addButton("§1Visit an Island",-1,"","teleport");
		$form->addButton("§1View Invitations",-1,"","view");
		if($player->getLevel()->getName() !== Server::getInstance()->getDefaultLevel()->getName()){
			$form->addButton("§1Go to Spawn", -1, "", "spawn");
		}
		$form->sendToPlayer($player);
	}

	public function handleGuestUI(Player $player, $data){
		$result = $data;
		if($result === null){
			return;
		}
		if(is_array($result)){
			$result = $result[0];
		}
		switch($result){
			case "join":  $command = "join";
				break;
            case "home": $command = "home";
                break;
			case "create":  $command = "create";
				break;
			case "manage": $this->manageUI($player);
				return;
			case "teleport": $this->teleportUI($player);
				return;
			case "view": $this->viewInvitationsUI($player);
				return;
			case "spawn": $this->sendToSpawn($player);
				return;
			default: $this->handleSwitchError($player, "handleGuestUI", $result);
				return;
		}
		$command = self::COMMAND_NAME . $command;
		$this->plugin->getServer()->getCommandMap()->dispatch($player, $command);
		return;
	}

	public function manageUI(Player $player){
		$form = $this->formAPI->createSimpleForm([$this, "handleManageUI"]);
		$form->setTitle(self::BASE_TITLE . "§r§2 - Manage Island");
		$form->addButton("§1Invite", -1, "", "invite");
		$island = $this->islandManager->getIslandByOwner($player->getName());
		if($island !== null){
			if($island->isOnIsland($player)){
				$form->addButton("§1SetHome", -1, "", "sethome");
				$form->addButton("§1Remove Member", -1, "", "remove");
				$form->addButton("§1Change Owner", -1, "", "changeowner");
			}
		}
		$form->addButton("§1Reset", -1, "", "reset");
		$form->addButton("§1Delete", -1, "", "delete");
		$form->sendToPlayer($player);
	}

	public function handleManageUI(Player $player, $data){
		$result = $data;
		if($result === null){
			return;
		}
		if(is_array($result)){
			$result = $data[0];
		}
		switch($result){
			case "invite":$this->inviteUI($player);
				break;
			case "sethome": $command = self::COMMAND_NAME . "sethome";
				$this->plugin->getServer()->getCommandMap()->dispatch($player, $command);
				break;
			case "remove": $this->removeMemberUI($player);
				return;
			case "changeowner": $this->changeOwnerUI($player);
				return;
			case "reset": $this->resetUI($player);
				return;
			case "delete": $this->deleteUI($player);
				return;
		}
		return;
	}

	public function inviteUI($player){
		$form = $this->formAPI->createCustomForm([$this, "handleInviteUI"]);
		$form->setTitle(self::BASE_TITLE . "§r§2 - Invite");
		$form->addInput("§aInvite a player", "name here");
		$form->sendToPlayer($player);
	}

	public function handleInviteUI(Player $player, $data){
		$result = $data[0];
		if($result === null){
			return;
		}
		$playerToInvite = $this->plugin->getServer()->getPlayer($result);
		if(!($playerToInvite instanceof Player)){
			$this->sendMessage($player, "§c✖ $playerToInvite not found.  Please try again.");
			return;
		}
		$command = self::COMMAND_NAME . "invite " . $result;
		$config = $this->plugin->getSkyBlockManager()->getPlayerConfig($playerToInvite);
		if(empty($config->get("island"))){
			$this->plugin->getServer()->getCommandMap()->dispatch($player, $command);
		}else{
			$this->sendMessage($player, "§l§c✖§f §c$result is already in a island!");
		}

	}

	public function generateModalForm(Player $player, string $title, string $handler, string $content){
		$form = $this->formAPI->createModalForm([$this, $handler]);
		$form->setTitle(self::BASE_TITLE . "§r§2 - " . $title);
		$form->setContent($content);
		$form->setButton1("§l§2✔ Confirm");
		$form->setButton2("§l§c✖ Cancel");
		$form->sendToPlayer($player);
	}

	public function viewInvitationsUI(Player $player){
		$invitations =$this->invitationHandler->getInvitationByReceiver($player);
		if($invitations === null){
			$this->sendMessage($player, "§c✖§fYou don't currently have any invitations.");
			return;
		}
		$form = $this->formAPI->createSimpleForm([$this, "handleViewInvitationsUI"]);
		$form->setTitle(self::BASE_TITLE . "§r§2 - Invitations");
		foreach($invitations as $invite){
			$name = $invite->getSender()->getName();
			$form->addButton("§1" . $name, -1, "", $name);
		}
		$form->addButton("§1Cancel", -1, "", "cancel");
		$form->sendToPlayer($player);
	}

	public function handleViewInvitationsUI(Player $player, $data){
		$result = $data;
		if($result === null or $result === "cancel"){
			return;
		}
		$this->acceptUI($player, $result);
	}

	public function acceptUI(Player $player, $sender){

		$invitations = $this->invitationHandler->getInvitationByReceiver($player);
		$invitation = null;
		if($invitations !== null){
			foreach ($invitations as $invite){
				if ($invite->getSender()->getName() == $sender){
					$invitation = $invite;
				}
			}
		}
		if($invitation !== null){
			$this->invitationPass[$player->getUniqueId()->toString()] = $invitation;
			$handler = "handleAcceptUI";
			$title = "Accept Invitation";
			$content = "§3$sender §r has invited you to their Island. Would you like to join?";
			$this->generateModalForm($player, $title, $handler, $content);
		}else{
			$this->sendMessage($player, "§l§c✖ The invitation from $sender no longer valid");
		}
	}

	public function handleAcceptUI(Player $player, $data){
		$result = $data;
		if($result === null){
			return;
		}
		if($result){
			$this->invitationPass[$player->getUniqueId()->toString()]->accept();
		}
		return;
	}

	public function deleteUI($player){
		$title = "Delete Island";
		$handler = "handleDeleteUI";
		$content = "Deleting your island will cause you to lose everything on the island.";
		$content = $content . "\nIt will be 10 minutes before you can create another island.";
		$content = $content . "\nThis cannot be undone.\n Continue?";
		$this->generateModalForm($player, $title, $handler, $content);
	}

	public function handleDeleteUI(Player $player, $data){
		$result = $data;
		if($data === null){
			return;
		}
		if($result){
			$command = self::COMMAND_NAME . "disband";
			$this->plugin->getServer()->getCommandMap()->dispatch($player, $command);
		}
		return;
	}

	public function removeMemberUI(Player $player){
		// Probably a good idea to check this.
		$island = $this->islandManager->getIslandByOwner($player->getName());
		if($island instanceof Island){
			$form = $this->formAPI->createSimpleForm([$this, "handleRemoveMemberUI"]);
			$form->setTitle(self::BASE_TITLE . "§r§2 - Remove Member");
			$members = $island->getMembers();
			foreach($members as $member){
				$form->addButton("§l§c✖ Kick \n" . $member, -1, "", $member);
			}
			$form->addButton("Cancel",-1, "", "cancel");
			$form->sendToPlayer($player);
		} else {
			$this->sendMessage($player, "§l§c✖§f §cYou don't have an island!");
			Server::getInstance()->getLogger()->error("removeMemberUI called with invalid Player argument.");
		}
		return;
	}

	public function handleRemoveMemberUI(Player $player, $data){
		$result = $data;
		if($result != null and $result !== "cancel"){
			$command = self::COMMAND_NAME . "remove " . $result;
			$this->plugin->getServer()->getCommandMap()->dispatch($player, $command);
		}
		return;
	}

	public function changeOwnerUI(Player $player){
		$form = $this->formAPI->createCustomForm([$this, "handleChangeOwnerUI"]);
		$form->setTitle(self::BASE_TITLE . "§r§2 - Change Owner");
		$form->addInput("§aNew Owner's Name", "name here", null, "newOwner");
		$form->sendToPlayer($player);
	}

	public function handleChangeOwnerUI(Player $player, $data){
		$result = $data;
		if($result === null){
			return;
		}
		$newOwner = Server::getInstance()->getPlayer($result["newOwner"]);
		if(!($newOwner instanceof Player)){
			$this->sendMessage($player, "{$result["newOwner"]} is not a valid player.  Please try again.");
		}
		$islandID = $this->islandManager->getIslandByOwner($player->getName())->getIdentifier();
		$newOwnerConfig = Utils::getUserConfig($newOwner->getName());
		if($newOwnerConfig !== null){
			$newOwnerIsland = $newOwnerConfig->get("island");
			if($newOwnerIsland !== "" and $newOwnerIsland !== $islandID){
				$this->sendMessage($player, "{$newOwner->getName()} is already in an different island.");
				$this->sendMessage($newOwner, "{$player->getName()} wants to give you an island but you're already a member on another island.");
				return;
			}
		}
		$this->ownerPass[$newOwner->getName()] = $player->getName();
		$this->sendMessage($player, "Owner change request sent to {$newOwner->getName()}");
		$this->confirmChangeOwnerUI($player, $newOwner);
		return;

	}

	public function confirmChangeOwnerUI(Player $currentOwner, Player $newOwner){
		$title = "Confirm Ownership";
		$handler = "handleConfirmChangeOwnerUI";
		$currentOwnerName = $currentOwner->getName();
		$content = "$currentOwnerName would like to make you the owner of their island.  If you accept, they will stay a member until you remove them but will not be the owner anymore.";
		$this->generateModalForm($newOwner, $title, $handler, $content);
	}

	public function handleConfirmChangeOwnerUI(Player $newOwner, $data){
		$currentOwnerName = $this->ownerPass[$newOwner->getName()];
		$currentOwner = Server::getInstance()->getPlayer($currentOwnerName);
		if($data !== null and $data){
			$island = $this->islandManager->getIslandByOwner($currentOwnerName);
			if($island instanceof Island){
				$island->setOwnerName($newOwner->getName());
				$island->addMember($currentOwner);
				$island->update();


				$newOwnerConfig = $this->plugin->getSkyBlockManager()->getPlayerConfig($newOwner);
				$newOwnerConfig->set("island", $island->getIdentifier());
				$newOwnerConfig->save();
				$this->sendMessage($currentOwner, "The island has been transferred successfully!");
				$this->sendMessage($newOwner, "The island has been transferred successfully!");
			}else{
				$this->sendMessage($currentOwner, "Something went wrong with the transfer.");
				$this->sendMessage($newOwner, "Something went wrong with the transfer.");
			}
		} else{
			$this->sendMessage($currentOwner, "{$newOwner->getName()} did not accept your offer.");
		}
		unset($this->ownerPass[$newOwner->getName()]);
		return;
	}

	public function resetUI($player){
		$title = "Reset";
		$handler = "handleResetUI";
		$content = "This will remove everything on your island and leave you with only a basic starting island.";
		$content = $content . "\nThis cannot be undone.\n Continue?";
		$this->generateModalForm($player, $title, $handler, $content);
	}

	public function handleResetUI(Player $player, $data){
		$result = $data;
		if($result){
			$command = self::COMMAND_NAME . "reset";
			$this->plugin->getServer()->getCommandMap()->dispatch($player, $command);
		}
	}

	public function teleportUI($player){
		if($this->formAPI === null || $this->formAPI->isDisabled()){
			return;
		}
		$form = $this->formAPI->createCustomForm([$this, "handleTeleportUI"]);
		$form->setTitle(self::BASE_TITLE . "§r§2 - Teleport");
		$form->addInput("§aTeleport to a player island", "name here");
		$form->sendToPlayer($player);
	}

	public function handleTeleportUI(Player $player, $data){
		$result = $data;
		if($result != null){
			$command = self::COMMAND_NAME . "tp " . $result[0];
			$this->plugin->getServer()->getCommandMap()->dispatch($player, $command);
		}
	}

	public function leaveUI(Player $player){
		$handler = "handleLeaveUI";
		$title = "Leave";
		$content = "You will no longer be a member of this island.  Are you sure you want to leave?";
		$this->generateModalForm($player, $title, $handler, $content);
	}

	public function handleLeaveUI(Player $player, $data){
		if($data){
			$command = self::COMMAND_NAME . "leave";
			$this->plugin->getServer()->getCommandMap()->dispatch($player, $command);
		}
	}

	public function kickUI(Player $player){
		$island = $this->islandManager->getIslandByOwner($player->getName());
		if($island instanceof Island){
			$form = $this->formAPI->createSimpleForm([$this, "handleKickUI"]);
			$form->setTitle(self::BASE_TITLE . "§r§2 - Kick");
			$form->setContent("This will temporarily kick a player from your island.");
			$members = $this->plugin->getServer()->getLevelByName($island->getIdentifier())->getPlayers();
			foreach($members as $member){
				if($member !== $player){
					$memberName = $member->getName();
					$form->addButton("§l§c✖ Kick \n" . $memberName, -1, "", $memberName);
				}
			}
			$form->addButton("Cancel",-1, "", "cancel");
			$form->sendToPlayer($player);
		} else {
			$this->sendMessage($player, "§l§c✖§f §cYou don't have an island!");
			Server::getInstance()->getLogger()->error("kickUI called with invalid Player argument.");
		}
		return;
	}

	public function handleKickUI(Player $player, $data){
		$result = $data;
		if($result != null and $result !== "cancel"){
			$command = self::COMMAND_NAME . "kick " . $result;
			$this->plugin->getServer()->getCommandMap()->dispatch($player, $command);
		}
		return;
	}

	public function sendToSpawn(Player $player){
		foreach($this->islandManager->getOnlineIslands() as $island){
			$island->tryRemovePlayer($player);
		}
		$pos = Server::getInstance()->getDefaultLevel()->getSafeSpawn();
		$player->teleport($pos);
		return;
	}

	public function handleSwitchError(Player $player, string $sourceName, string $result){
		Server::getInstance()->getLogger()->error("$sourceName returned unhandled result $result");
		$player->sendMessage("§cOops, SkyBlock had an error processing your request.");
		return;
	}
}