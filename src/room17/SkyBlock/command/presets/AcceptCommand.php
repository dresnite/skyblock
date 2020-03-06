<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace room17\SkyBlock\command\presets;


use ReflectionException;
use room17\SkyBlock\command\IslandCommand;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\utils\message\MessageContainer;

class AcceptCommand extends IslandCommand {

    public function getName(): string {
        return "accept";
    }

    public function getAliases(): array {
        return ["acc"];
    }

    public function getUsageMessageContainer(): MessageContainer {
        return new MessageContainer("ACCEPT_USAGE");
    }

    public function getDescriptionMessageContainer(): MessageContainer {
        return new MessageContainer("ACCEPT_DESCRIPTION");
    }

    /**
     * @throws ReflectionException
     */
    public function onCommand(Session $session, array $args): void {
        if($session->hasIsland()) {
            $session->sendTranslatedMessage(new MessageContainer("NEED_TO_BE_FREE"));
            return;
        }

        $invitation = null;
        if(isset($args[0]) and $session->hasInvitationFrom($args[0])) {
            $invitation = $session->getInvitationFrom($args[0]);
        } else {
            $invitation = $session->getLastInvitation();
        }

        if($invitation != null) {
            $invitation->accept();
        } else {
            $session->sendTranslatedMessage(new MessageContainer("ACCEPT_USAGE"));
        }
    }

}