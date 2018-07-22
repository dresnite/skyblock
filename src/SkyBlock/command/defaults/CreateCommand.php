<?php
/*
 * Copyright (C) Andrés Arias - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

namespace SkyBlock\command\defaults;


use SkyBlock\command\IsleCommand;
use SkyBlock\command\IsleCommandMap;
use SkyBlock\session\Session;
use SkyBlock\SkyBlock;

class CreateCommand extends IsleCommand {
    
    /** @var SkyBlock */
    private $plugin;
    
    /**
     * CreateCommand constructor.
     * @param IsleCommandMap $map
     */
    public function __construct(IsleCommandMap $map) {
        $this->plugin = $map->getPlugin();
        parent::__construct(["create"], "CREATE_USAGE", "CREATE_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($session->hasIsle()) {
            $session->sendTranslatedMessage("NEED_TO_BE_FREE");
            return;
        }
        $this->plugin->getIsleManager()->createIsleFor($session, "Basic");
        $session->sendTranslatedMessage("SUCCESSFULLY_CREATED_A_ISLE");
    }
    
}