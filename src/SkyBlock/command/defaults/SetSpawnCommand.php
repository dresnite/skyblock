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