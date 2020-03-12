<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace GiantQuartz\SkyBlock\command\presets;


use GiantQuartz\SkyBlock\session\SessionLocator;
use ReflectionException;
use GiantQuartz\SkyBlock\command\IslandCommand;
use GiantQuartz\SkyBlock\session\Session;
use GiantQuartz\SkyBlock\utils\message\MessageContainer;

class DemoteCommand extends IslandCommand {

    public function getName(): string {
        return "demote";
    }

    public function getUsageMessageContainer(): MessageContainer {
        return new MessageContainer("DEMOTE_USAGE");
    }

    public function getDescriptionMessageContainer(): MessageContainer {
        return new MessageContainer("DEMOTE_DESCRIPTION");
    }

    /**
     * @throws ReflectionException
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkLeader($session)) {
            return;
        } elseif(!isset($args[0])) {
            $session->sendTranslatedMessage(new MessageContainer("DEMOTE_USAGE"));
            return;
        }

        $offlineSession = SessionLocator::getOfflineSession($args[0]);
        if($this->checkClone($session, $offlineSession->getSession())) {
            return;
        } elseif($offlineSession->getIslandId() != $session->getIslandId()) {
            $session->sendTranslatedMessage(new MessageContainer("MUST_BE_PART_OF_YOUR_ISLAND", [
                "name" => $args[0]
            ]));
        } else {
            $rank = null;
            $rankName = "";
            switch($offlineSession->getRank()) {
                case Session::RANK_OFFICER:
                    $rank = Session::RANK_DEFAULT;
                    $rankName = "MEMBER";
                    break;
                case Session::RANK_LEADER:
                    $rank = Session::RANK_OFFICER;
                    $rankName = "OFFICER";
                    break;
                case Session::RANK_FOUNDER:
                    $rank = false;
            }
            if($rank == null) {
                $session->sendTranslatedMessage(new MessageContainer("CANNOT_DEMOTE_MEMBER", [
                    "name" => $args[0]
                ]));
                return;
            } elseif($rank == false) {
                $session->sendTranslatedMessage(new MessageContainer("CANNOT_DEMOTE_FOUNDER"));
                return;
            }
            $onlineSession = $offlineSession->getSession();
            if($onlineSession != null) {
                $onlineSession->setRank($rank);
                $onlineSession->sendTranslatedMessage(new MessageContainer("YOU_HAVE_BEEN_DEMOTED"));
                $onlineSession->save();
            } else {
                $offlineSession->setRank($rank);
                $offlineSession->save();
            }
            $session->sendTranslatedMessage(new MessageContainer("SUCCESSFULLY_DEMOTED_PLAYER", [
                "name" => $args[0],
                "to" => $session->getMessage(new MessageContainer("$rankName"))
            ]));
        }

    }

}