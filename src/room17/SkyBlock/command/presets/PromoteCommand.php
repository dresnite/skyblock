<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace room17\SkyBlock\command\presets;


use room17\SkyBlock\island\RankIds;
use room17\SkyBlock\session\SessionLocator;
use ReflectionException;
use room17\SkyBlock\command\IslandCommand;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\utils\message\MessageContainer;

class PromoteCommand extends IslandCommand {

    public function getName(): string {
        return "promote";
    }

    public function getUsageMessageContainer(): MessageContainer {
        return new MessageContainer("PROMOTE_USAGE");
    }

    public function getDescriptionMessageContainer(): MessageContainer {
        return new MessageContainer("PROMOTE_DESCRIPTION");
    }

    /**
     * @throws ReflectionException
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkLeader($session)) {
            return;
        } elseif(!isset($args[0])) {
            $session->sendTranslatedMessage(new MessageContainer("PROMOTE_USAGE"));
            return;
        }

        $offlineSession = SessionLocator::getOfflineSession($args[0]);
        if($this->checkClone($session, $offlineSession->getOnlineSession())) {
            return;
        } elseif($offlineSession->getIslandId() != $session->getIslandId()) {
            $session->sendTranslatedMessage(new MessageContainer("MUST_BE_PART_OF_YOUR_ISLAND", [
                "name" => $args[0]
            ]));
        } else {
            $rank = null;
            $rankName = "";
            switch($offlineSession->getRank()) {
                case RankIds::MEMBER:
                    $rank = RankIds::OFFICER;
                    $rankName = "OFFICER";
                    break;
                case RankIds::OFFICER:
                    $rank = RankIds::LEADER;
                    $rankName = "LEADER";
                    break;
            }
            if($rank == null) {
                $session->sendTranslatedMessage(new MessageContainer("CANNOT_PROMOTE_LEADER", [
                    "name" => $args[0]
                ]));
                return;
            }
            $onlineSession = $offlineSession->getOnlineSession();
            if($onlineSession != null) {
                $onlineSession->setRank($rank);
                $onlineSession->sendTranslatedMessage(new MessageContainer("YOU_HAVE_BEEN_PROMOTED"));
                $onlineSession->save();
            } else {
                $offlineSession->setRank($rank);
                $offlineSession->save();
            }
            $session->sendTranslatedMessage(new MessageContainer("SUCCESSFULLY_PROMOTED_PLAYER", [
                "name" => $args[0],
                "to" => $session->getMessage(new MessageContainer("$rankName"))
            ]));
        }

    }

}