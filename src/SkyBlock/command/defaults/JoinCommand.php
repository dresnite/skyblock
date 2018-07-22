<?php
/*
 * Copyright (C) Andrés Arias - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

namespace SkyBlock\command\defaults;


use SkyBlock\command\IsleCommand;
use SkyBlock\session\Session;

class JoinCommand extends IsleCommand {
    
    /**
     * JoinCommand constructor.
     */
    public function __construct() {
        parent::__construct(["join", "tp"], "JOIN_USAGE", "JOIN_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkIsle($session)) {
            return;
        }
        $session->getPlayer()->teleport($session->getIsle()->getLevel()->getSpawnLocation());
        $session->sendTranslatedMessage("TELEPORTED_TO_ISLE");
    }
    
}