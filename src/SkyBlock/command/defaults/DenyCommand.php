<?php
/*
 * Copyright (C) PrimeGames - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
*/

namespace SkyBlock\command\defaults;


use SkyBlock\command\IsleCommand;
use SkyBlock\session\Session;

class DenyCommand extends IsleCommand {
    
    /**
     * DenyCommand constructor.
     */
    public function __construct() {
        parent::__construct(["deny", "d"], "DENY_USAGE", "DENY_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if(!isset($args[0]) and !$session->hasLastInvitation()) {
            $session->sendTranslatedMessage("DENY_USAGE");
            return;
        }
        $isleName = $args[0] ?? $session->getLastInvitation();
        $isle = $session->getInvitation($isleName);
        if($isle == null) {
            return;
        }
        $session->removeInvitation($isleName);
        $isle->broadcastTranslatedMessage("PLAYER_INVITATION_DENIED", [
            "name" => $session->getUsername()
        ]);
    }
    
}