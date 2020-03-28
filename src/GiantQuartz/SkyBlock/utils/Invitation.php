<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace GiantQuartz\SkyBlock\utils;


use GiantQuartz\SkyBlock\island\RankIds;
use ReflectionException;
use GiantQuartz\SkyBlock\island\Island;
use GiantQuartz\SkyBlock\session\Session;
use GiantQuartz\SkyBlock\utils\message\MessageContainer;

class Invitation {

    /** @var Session */
    private $sender;

    /** @var Session */
    private $target;

    /** @var Island */
    private $island;

    /** @var float */
    private $creationTime;

    public static function send(Session $sender, Session $target): void {
        $target->sendInvitation(new Invitation($sender, $target));
        $target->sendTranslatedMessage(new MessageContainer("YOU_WERE_INVITED_TO_AN_ISLAND", [
            "name" => $sender->getName()
        ]));
        $sender->sendTranslatedMessage(new MessageContainer("SUCCESSFULLY_INVITED", [
            "name" => $target->getName()
        ]));
    }

    public function __construct(Session $sender, Session $target) {
        $this->sender = $sender;
        $this->target = $target;
        $this->island = $sender->getIsland();
        $this->creationTime = microtime(true);
    }

    public function getSender(): Session {
        return $this->sender;
    }

    public function getTarget(): Session {
        return $this->target;
    }

    public function getIsland(): Island {
        return $this->island;
    }

    public function getCreationTime(): float {
        return $this->creationTime;
    }

    /**
     * @throws ReflectionException
     */
    public function accept(): void {
        $this->target->removeInvitation($this);
        $this->target->setIsland($this->island);
        $this->target->setRank(RankIds::MEMBER);
        $this->target->clearInvitations();
        $this->island->broadcastTranslatedMessage(new MessageContainer("PLAYER_JOINED_THE_ISLAND", [
            "name" => $this->target->getName()
        ]));
    }

    /**
     * @throws ReflectionException
     */
    public function deny(): void {
        $this->target->removeInvitation($this);
        $this->target->sendTranslatedMessage(new MessageContainer("INVITATION_REFUSED"));
        $this->island->broadcastTranslatedMessage(new MessageContainer("PLAYER_INVITATION_DENIED", [
            "name" => $this->target->getName()
        ]));
    }

    public function cancel(): void {
        $this->sender->removeInvitation($this);

        $messageContainer = new MessageContainer("INVITATION_CANCELLED", [
            "sender" => $this->sender->getName(),
            "target" => $this->target->getName()
        ]);

        $this->sender->sendTranslatedMessage($messageContainer);
        $this->target->sendTranslatedMessage($messageContainer);
    }

}