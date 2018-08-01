<?php
/**
 * Copyright (C) Andrés Arias - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

namespace SkyBlock\command\defaults;


use SkyBlock\command\IsleCommand;
use SkyBlock\command\IsleCommandMap;
use SkyBlock\isle\IsleManager;
use SkyBlock\session\Session;

class DisbandCommand extends IsleCommand {
    
    /** @var IsleManager */
    private $isleManager;
    
    /**
     * DisbandCommand constructor.
     * @param IsleCommandMap $map
     */
    public function __construct(IsleCommandMap $map) {
        $this->isleManager = $map->getPlugin()->getIsleManager();
        parent::__construct(["disband"], "DISBAND_USAGE", "DISBAND_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkLeader($session)) {
            return;
        }
        $this->isleManager->disbandIsle($session->getIsle());
    }
    
}