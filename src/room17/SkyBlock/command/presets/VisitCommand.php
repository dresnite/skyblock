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


use room17\SkyBlock\command\IsleCommand;
use room17\SkyBlock\command\IsleCommandMap;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\SkyBlock;

class VisitCommand extends IsleCommand {
    
    /** @var SkyBlock */
    private $plugin;
    
    /**
     * VisitCommand constructor.
     * @param IsleCommandMap $map
     */
    public function __construct(IsleCommandMap $map) {
        $this->plugin = $map->getPlugin();
        parent::__construct(["visit", "teleport", "tp"], "VISIT_USAGE", "VISIT_DESCRIPTION");
    }

    /**
     * @param Session $session
     * @param array $args
     * @throws \ReflectionException
     */
    public function onCommand(Session $session, array $args): void {
        if(!isset($args[0])) {
            $session->sendTranslatedMessage("VISIT_USAGE");
            return;
        }
        $offline = $this->plugin->getSessionManager()->getOfflineSession($args[0]);
        $isleId = $offline->getIsleId();
        if($isleId == null) {
            $session->sendTranslatedMessage("HE_DO_NOT_HAVE_AN_ISLE", [
                "name" => $args[0]
            ]);
            return;
        }
        $this->plugin->getProvider()->loadIsle($isleId);
        $isle = $this->plugin->getIsleManager()->getIsle($isleId);
        if($isle->isLocked() and !($session->getPlayer()->isOp())) {
            $session->sendTranslatedMessage("HIS_ISLE_IS_LOCKED", [
                "name" => $args[0]
            ]);
            $isle->tryToClose();
            return;
        }
        $session->getPlayer()->teleport($isle->getLevel()->getSpawnLocation());
        $session->sendTranslatedMessage("VISITING_ISLE", [
            "name" => $args[0]
        ]);
    }
    
}