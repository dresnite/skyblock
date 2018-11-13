<?php
/**
 * Copyright (C) Andrés Arias - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

namespace room17\SkyBlock\command\defaults;


use room17\SkyBlock\command\IsleCommand;
use room17\SkyBlock\command\IsleCommandMap;
use room17\SkyBlock\isle\IsleManager;
use room17\SkyBlock\session\Session;

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