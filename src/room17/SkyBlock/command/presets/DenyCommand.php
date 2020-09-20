<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace room17\SkyBlock\command\presets;


use room17\SkyBlock\command\IslandCommand;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\utils\message\MessageContainer;

class DenyCommand extends IslandCommand {

    public function getName(): string {
        return "deny";
    }

    public function getAliases(): array {
        return ["den", "d"];
    }

    public function getUsageMessageContainer(): MessageContainer {
        return new MessageContainer("DENY_USAGE");
    }

    public function getDescriptionMessageContainer(): MessageContainer {
        return new MessageContainer("DENY_DESCRIPTION");
    }

    public function onCommand(Session $session, array $args): void {
        $invitation = null;
        if(isset($args[0]) and $session->hasInvitationFrom($args[0])) {
            $invitation = $session->getInvitationFrom($args[0]);
        } else {
            $invitation = $session->getLastInvitation();
        }

        if($invitation != null) {
            $invitation->deny();
        } else {
            $session->sendTranslatedMessage(new MessageContainer("DENY_USAGE"));
        }
    }

}