<?php

/**
 * This is GiantQuartz property.
 *
 * Copyright (C) 2016 GiantQuartz
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author GiantQuartz
 *
 */

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