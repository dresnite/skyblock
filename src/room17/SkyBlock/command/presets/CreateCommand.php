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

namespace room17\SkyBlock\command\presets;


use room17\SkyBlock\command\IsleCommand;
use room17\SkyBlock\command\IsleCommandMap;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\SkyBlock;

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
        $minutesSinceLastIsle = $session->getLastIslandCreationTime() !== null
            ? (microtime(true) - $session->getLastIslandCreationTime()) / 60
            : -1;
        $cooldownDuration = $this->plugin->getSettings()->getCooldownDuration();
        if($minutesSinceLastIsle !== -1 and $minutesSinceLastIsle < $cooldownDuration) {
            $session->sendTranslatedMessage("YOU_HAVE_TO_WAIT", [
                "minutes" => ceil($cooldownDuration - $minutesSinceLastIsle),
            ]);
            return;
        }
        $generator = $args[0] ?? "Shelly";
        if($this->plugin->getGeneratorManager()->isGenerator($generator)) {
            $this->plugin->getIsleManager()->createIsleFor($session, $generator);
            $session->sendTranslatedMessage("SUCCESSFULLY_CREATED_A_ISLE");
        } else {
            $session->sendTranslatedMessage("NOT_VALID_GENERATOR", [
                "name" => $generator
            ]);
        }
    }
    
}