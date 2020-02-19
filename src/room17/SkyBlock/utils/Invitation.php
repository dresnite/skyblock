<?php

declare(strict_types=1);

namespace room17\SkyBlock\utils;


use room17\SkyBlock\island\Island;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\utils\message\MessageContainer;

class Invitation {

    /** @var Session */
    private $sender;

    /** @var Session */
    private $target;

    /** @var Island */
    private $island;

    /** @var float */
    private $creationTime;

    /**
     * @param Session $sender
     * @param Session $target
     */
    public static function send(Session $sender, Session $target): void {
        $target->sendInvitation(new Invitation($sender, $target));
    }

    /**
     * Invitation constructor.
     * @param Session $sender
     * @param Session $target
     */
    public function __construct(Session $sender, Session $target) {
        $this->sender = $sender;
        $this->target = $target;
        $this->island = $sender->getIsland();
        $this->creationTime = microtime(true);
    }

    /**
     * @return Session
     */
    public function getSender(): Session {
        return $this->sender;
    }

    /**
     * @return Session
     */
    public function getTarget(): Session {
        return $this->target;
    }

    /**
     * @return Island
     */
    public function getIsland(): Island {
        return $this->island;
    }

    /**
     * @return float
     */
    public function getCreationTime(): float {
        return $this->creationTime;
    }

    /**
     * @throws \ReflectionException
     */
    public function accept(): void {
        $this->target->setIsland($this->island);
        $this->target->setRank(Session::RANK_DEFAULT);
        $this->target->clearInvitations();
        $this->island->broadcastTranslatedMessage(new MessageContainer("PLAYER_JOINED_THE_ISLAND", [
            "name" => $this->target->getName()
        ]));
    }

    /**
     * @throws \ReflectionException
     */
    public function deny(): void {
        $this->target->removeInvitation($this);
        $this->target->sendTranslatedMessage(new MessageContainer("INVITATION_REFUSED"));
        $this->island->broadcastTranslatedMessage(new MessageContainer("PLAYER_INVITATION_DENIED", [
            "name" => $this->target->getName()
        ]));
    }

}