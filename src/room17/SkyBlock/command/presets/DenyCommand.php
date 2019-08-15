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
use room17\SkyBlock\session\Session;
use room17\SkyBlock\utils\MessageContainer;

class DenyCommand extends IslandCommand {
    
    /**
     * DenyCommand constructor.
     */
    public function __construct() {
        parent::__construct([
            "deny",
            "d"
        ], new MessageContainer("DENY_USAGE"), new MessageContainer("DENY_DESCRIPTION"));
    }

    /**
     * @param Session $session
     * @param array $args
     * @throws \ReflectionException
     */
    public function onCommand(Session $session, array $args): void {
        if(!isset($args[0]) and !$session->hasLastInvitation()) {
            $session->sendTranslatedMessage(new MessageContainer("DENY_USAGE"));
            return;
        }
        $islandName = $args[0] ?? $session->getLastInvitation();
        $island = $session->getInvitation($islandName);
        if($island == null) {
            return;
        }
        $session->removeInvitation($islandName);
        $session->sendTranslatedMessage(new MessageContainer("INVITATION_REFUSED"));
        $island->broadcastTranslatedMessage(new MessageContainer("PLAYER_INVITATION_DENIED", [
            "name" => $session->getName()
        ]));
    }
    
}