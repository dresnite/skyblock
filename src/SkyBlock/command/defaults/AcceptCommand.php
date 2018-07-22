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

namespace SkyBlock\command\defaults;


use SkyBlock\command\IsleCommand;
use SkyBlock\session\iSession;
use SkyBlock\session\Session;

class AcceptCommand extends IsleCommand {
    
    /**
     * AcceptCommand constructor.
     */
    public function __construct() {
        parent::__construct(["accept", "acc"], "ACCEPT_USAGE", "ACCEPT_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($session->hasIsle()) {
            $session->sendTranslatedMessage("NEED_TO_BE_FREE");
            return;
        } else if(!isset($args[0]) and !$session->hasLastInvitation()) {
            $session->sendTranslatedMessage("ACCEPT_USAGE");
            return;
        }
        $isle = $session->getInvitation($args[0] ?? $session->getLastInvitation());
        if($isle == null) {
            return;
        }
        $session->setRank(iSession::RANK_DEFAULT);
        $session->setIsle($isle);
        $isle->broadcastTranslatedMessage("PLAYER_JOINED_THE_ISLE", [
            "name" => $session->getPlayer()->getName()
        ]);
    }
    
}