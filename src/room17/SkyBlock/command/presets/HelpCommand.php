<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace room17\SkyBlock\command\presets;


use room17\SkyBlock\command\IslandCommand;
use room17\SkyBlock\command\IslandCommandMap;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\utils\message\MessageContainer;

class HelpCommand extends IslandCommand {

    private IslandCommandMap $map;

    public function __construct(IslandCommandMap $map) {
        $this->map = $map;
    }

    public function getName(): string {
        return "help";
    }

    public function getAliases(): array {
        return ["?"];
    }

    public function getUsageMessageContainer(): MessageContainer {
        return new MessageContainer("HELP_USAGE");
    }

    public function getDescriptionMessageContainer(): MessageContainer {
        return new MessageContainer("HELP_DESCRIPTION");
    }

    public function onCommand(Session $session, array $args): void {
        $session->sendTranslatedMessage(new MessageContainer("HELP_HEADER", [
            "amount" => count($this->map->getCommands())
        ]));

        foreach($this->map->getCommands() as $command) {
            $session->sendTranslatedMessage(new MessageContainer("HELP_COMMAND_TEMPLATE", [
                "name" => $command->getName(),
                "description" => $session->getMessage($command->getDescriptionMessageContainer()),
                "usage" => $session->getMessage($command->getUsageMessageContainer())
            ]));
        }
    }

}