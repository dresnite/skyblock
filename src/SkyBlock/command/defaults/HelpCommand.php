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

class HelpCommand extends IsleCommand {
    
    /** @var IsleCommandMap */
    private $map;
    
    /**
     * HelpCommand constructor.
     * @param IsleCommandMap $map
     */
    public function __construct(IsleCommandMap $map) {
        $this->map = $map;
        parent::__construct([], "HELP_USAGE", "HELP_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        $session->sendTranslatedMessage("HELP_HEADER", ["amount" => count($this->map->getCommands())]);
        foreach($this->map->getCommands() as $command) {
            $session->sendTranslatedMessage("HELP_COMMAND_TEMPLATE", [
                "name" => array_shift($command->getAliases()),
                "description" => $session->translate($command->getDescriptionMessageId()),
                "usage" => $session->translate($command->getUsageMessageId())
            ]);
            $session->getPlayer()->sendMessage(array_shift($command->getAliases()) . ": " . $session->translate($command->getDescriptionMessageId()));
        }
    }
    
}