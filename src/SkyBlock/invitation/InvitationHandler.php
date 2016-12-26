<?php

namespace SkyBlock\invitation;

use pocketmine\Player;
use SkyBlock\island\Island;
use SkyBlock\Main;

class InvitationHandler {

    /** @var Main */
    private $plugin;

    /** @var Invitation[] */
    private $invitations = [];

    /**
     * InvitationManager constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * Return Main instance
     *
     * @return Main
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
     * Return an invitation
     *
     * @param Player $player
     * @return null|Invitation
     */
    public function getInvitation(Player $player) {
        if(isset($this->invitations[strtolower($player->getName())])) {
            return $this->invitations[strtolower($player->getName())];
        }
        else {
            return null;
        }
    }

    /**
     * Create a new invitation
     *
     * @param Player $sender
     * @param Player $receiver
     * @param Island $island
     */
    public function addInvitation(Player $sender, Player $receiver, Island $island) {
        $this->invitations[strtolower($sender->getName())] = new Invitation($this, $sender, $receiver, $island);
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