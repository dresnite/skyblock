<?php
/*
 * Copyright (C) PrimeGames - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
*/

namespace SkyBlock\command\defaults;


use SkyBlock\command\IsleCommand;
use SkyBlock\session\Session;

class SetSpawnCommand extends IsleCommand {
    
    /**
     * SetSpawnCommand constructor.
     */
    public function __construct() {
        parent::__construct(["setspawn"], "SET_SPAWN_USAGE", "SET_SPAWN_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkOfficer($session)) {
            return;
        } elseif($session->getPlayer()->getLevel() !== $session->getIsle()->getLevel()) {
            $session->sendTranslatedMessage("MUST_BE_IN_YOUR_ISLE");
        } else {
            $session->getIsle()->setSpawnLocation($session->getPlayer());
            $session->sendTranslatedMessage("SUCCESSFULLY_SET_SPAWN");
        }
    }
    
}