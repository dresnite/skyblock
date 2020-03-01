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


use ReflectionException;
use room17\SkyBlock\command\IslandCommand;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\utils\message\MessageContainer;

class DenyCommand extends IslandCommand {

    /**
     * @return string
     */
    public function getName(): string {
        return "deny";
    }

    /**
     * @return array
     */
    public function getAliases(): array {
        return ["den", "d"];
    }

    /**
     * @return MessageContainer
     */
    public function getUsageMessageContainer(): MessageContainer {
        return new MessageContainer("DENY_USAGE");
    }

    /**
     * @return MessageContainer
     */
    public function getDescriptionMessageContainer(): MessageContainer {
        return new MessageContainer("DENY_DESCRIPTION");
    }

    /**
     * @param Session $session
     * @param array $args
     * @throws ReflectionException
     */
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