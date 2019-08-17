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

namespace room17\SkyBlock\command\presets;


use room17\SkyBlock\command\IslandCommand;
use room17\SkyBlock\session\BaseSession;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\utils\MessageContainer;

class LeaveCommand extends IslandCommand {

    /**
     * @return string
     */
    public function getName(): string {
        return "leave";
    }

    /**
     * @return MessageContainer
     */
    public function getUsageMessageContainer(): MessageContainer {
        return new MessageContainer("LEAVE_USAGE");
    }

    /**
     * @return MessageContainer
     */
    public function getDescriptionMessageContainer(): MessageContainer {
        return new MessageContainer("LEAVE_DESCRIPTION");
    }

    /**
     * @param Session $session
     * @param array $args
     * @throws \ReflectionException
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkIsland($session)) {
            return;
        } elseif($session->getRank() == BaseSession::RANK_FOUNDER) {
            $session->sendTranslatedMessage(new MessageContainer("FOUNDER_CANNOT_LEAVE"));
            return;
        }
        $session->setRank(BaseSession::RANK_DEFAULT);
        $session->setIsland(null);
        $session->setInChat(false);
        $session->sendTranslatedMessage(new MessageContainer("LEFT_ISLAND"));
    }

}