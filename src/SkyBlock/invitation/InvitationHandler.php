<?php

namespace SkyBlock\invitation;

use pocketmine\Player;
use SkyBlock\island\Island;
use SkyBlock\SkyBlock;

class InvitationHandler {

    /** @var SkyBlock */
    private $plugin;

    /** @var Invitation[] */
    private $invitations = [];

    /**
     * InvitationManager constructor.
     *
     * @param SkyBlock $plugin
     */
    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * Return SkyBlockPE instance
     *
     * @return SkyBlock
     */
    public function getPlugin() {
        return $this->plugin;
    }

    /**
     * Return all invitations
     *
     * @return Invitation[]
     */
    public function getInvitations() {
        return $this->invitations;
    }

    /**
     * Returns an array of invitations based on the sender of the invitation.
     *
     * @param Player $sender
     * @return null|Invitation[]
     */
    public function getInvitationBySender(Player $sender) {
    	$invitations = [];
    	foreach($this->invitations as $invite){
    		if($invite->getSender()->getUniqueId() === $sender->getUniqueId()){
    			$invitations[] = $invite;
			}
		}
		if(empty($invitations)){
    		return null;
		}
		return $invitations;
    }

	/**
	 * Returns an array of invitations based on the receiver of the invitation.
	 *
	 * @param Player $receiver
	 * @return null|Invitation[]
	 */

    public function getInvitationByReceiver(Player $receiver) {
		$invitations = [];
		foreach($this->invitations as $invite){
			if($invite->getReceiver()->getUniqueId() === $receiver->getUniqueId()){
				$invitations[] = $invite;
			}
		}
		if(empty($invitations)){
			return null;
		}
		return $invitations;
	}

    /**
     * Create a new invitation
     *
     * @param Player $sender
     * @param Player $receiver
     * @param Island $island
     */
    public function addInvitation(Player $sender, Player $receiver, Island $island) {
        $this->invitations[] = new Invitation($this, $sender, $receiver, $island);
    }

    /**
     * Remove an invitation
     *
     * @param Invitation $invitation
     */
    public function removeInvitation(Invitation $invitation) {
        if(in_array($invitation, $this->invitations)) {
            unset($this->invitations[array_search($invitation, $this->invitations)]);
        }
    }

    public function tick() {
        foreach($this->invitations as $invitation) {
            $invitation->tick();
        }
    }

}