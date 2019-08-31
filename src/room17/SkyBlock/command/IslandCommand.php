<?php
/**
 *  _____    ____    ____   __  __  __  ______
 * |  __ \  / __ \  / __ \ |  \/  |/_ ||____  |
 * | |__) || |  | || |  | || \  / | | |    / /
 * |  _  / | |  | || |  | || |\/| | | |   / /
 * | | \ \ | |__| || |__| || |  | | | |  / /
 * |_|  \_\ \____/  \____/ |_|  |_| |_| /_/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 */

declare(strict_types=1);

namespace room17\SkyBlock\command;


use room17\SkyBlock\session\BaseSession;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\utils\message\MessageContainer;

abstract class IslandCommand {

    /**
     * @return array
     */
    public function getAliases(): array {
        return [];
    }

    /**
     * @param Session $session
     * @return bool
     */
    public function checkIsland(Session $session): bool {
        if($session->hasIsland()) {
            return false;
        }
        $session->sendTranslatedMessage(new MessageContainer("NEED_ISLAND"));
        return true;
    }

    /**
     * @param Session $session
     * @return bool
     */
    public function checkFounder(Session $session): bool {
        if($this->checkIsland($session)) {
            return true;
        } elseif($session->getRank() == BaseSession::RANK_FOUNDER) {
            return false;
        }
        $session->sendTranslatedMessage(new MessageContainer("MUST_BE_FOUNDER"));
        return true;
    }

    /**
     * @param Session $session
     * @return bool
     */
    public function checkLeader(Session $session): bool {
        if($this->checkIsland($session)) {
            return true;
        } elseif($session->getRank() == BaseSession::RANK_FOUNDER or $session->getRank() == BaseSession::RANK_LEADER) {
            return false;
        }
        $session->sendTranslatedMessage(new MessageContainer("MUST_BE_LEADER"));
        return true;
    }

    /**
     * @param Session $session
     * @return bool
     */
    public function checkOfficer(Session $session): bool {
        if($this->checkIsland($session)) {
            return true;
        } elseif($session->getRank() != BaseSession::RANK_DEFAULT) {
            return false;
        }
        $session->sendTranslatedMessage(new MessageContainer("MUST_BE_OFFICER"));
        return true;
    }

    /**
     * @param null|Session $session
     * @param null|Session $ySession
     * @return bool
     */
    public function checkClone(?Session $session, ?Session $ySession): bool {
        if($session === $ySession) {
            $session->sendTranslatedMessage(new MessageContainer("CANT_BE_YOURSELF"));
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public abstract function getName(): string;

    /**
     * @return MessageContainer
     */
    public abstract function getUsageMessageContainer(): MessageContainer;

    /**
     * @return MessageContainer
     */
    public abstract function getDescriptionMessageContainer(): MessageContainer;

    /**
     * @param Session $session
     * @param array $args
     * @return void
     */
    public abstract function onCommand(Session $session, array $args): void;

}