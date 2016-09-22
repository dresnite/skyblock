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
use pocketmine\utils\TextFormat;
use SkyBlock\island\Island;

class Invitation {

    /** @var InvitationHandler */
    private $handler;

    /** @var Player */
    private $sender;

    /** @var Player */
    private $receiver;

    /** @var Island */
    private $island;

    /** @var int */
    private $time = 30;

    /**
     * Invitation constructor.
     *
     * @param InvitationHandler $handler
     * @param Player $sender
     * @param Player $receiver
     * @param Island $island
     */
    public function __construct(InvitationHandler $handler, Player $sender, Player $receiver, Island $island) {
        $this->handler = $handler;
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->island = $island;
    }

    /**
     * Return invitation sender
     *
     * @return Player
     */
    public function getSender() {
        return $this->sender;
    }

    /**
     * Return invitation receiver
     *
     * @return Player
     */
    public function getReceiver() {
        return $this->receiver;
    }

    public function accept() {
        $config = $this->handler->getPlugin()->getSkyBlockManager()->getPlayerConfig($this->receiver);
        if(empty($config->get("island"))) {
            $config->set("island", $this->island->getIdentifier());
            $config->save();
            $this->island->addMember($this->receiver);
            $this->sender->sendMessage(TextFormat::RED . "* " . TextFormat::YELLOW . "{$this->receiver->getName()} accepted your invitation!");
            $this->receiver->sendMessage(TextFormat::RED . "* " . TextFormat::YELLOW . "You joined {$this->sender->getName()} island!");
        }
        else {
            $this->sender->sendMessage(TextFormat::RED . "* " . TextFormat::YELLOW . "{$this->receiver->getName()} is already in island!");
        }
        $this->handler->removeInvitation($this);
    }

    public function deny() {
        $this->sender->sendMessage(TextFormat::RED . "* " . TextFormat::YELLOW . "{$this->receiver->getName()} denied your invitation!");
        $this->receiver->sendMessage(TextFormat::RED . "* " . TextFormat::YELLOW . "You denied {$this->sender->getName()}'s invitation!");
        $this->handler->removeInvitation($this);
    }

    public function expire() {
        $this->sender->sendMessage(TextFormat::RED . "* " . TextFormat::YELLOW . "The invitation to {$this->receiver->getName()} expired!");
        $this->handler->removeInvitation($this);
    }

    public function tick() {
        if($this->time <= 0) {
            $this->expire();
        }
        else {
            $this->time--;
            $this->sender->sendPopup(TextFormat::RED . "> " . TextFormat::YELLOW . "The invitation to {$this->receiver->getName()} will expire in {$this->time} seconds" . TextFormat::RED . " <");
        }
    }

}