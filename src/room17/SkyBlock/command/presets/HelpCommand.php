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


use room17\SkyBlock\command\IslandCommand;
use room17\SkyBlock\command\IslandCommandMap;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\utils\message\MessageContainer;

class HelpCommand extends IslandCommand {

    /** @var IslandCommandMap */
    private $map;

    /**
     * HelpCommand constructor.
     * @param IslandCommandMap $map
     */
    public function __construct(IslandCommandMap $map) {
        $this->map = $map;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "help";
    }

    /**
     * @return array
     */
    public function getAliases(): array {
        return ["?"];
    }

    /**
     * @return MessageContainer
     */
    public function getUsageMessageContainer(): MessageContainer {
        return new MessageContainer("HELP_USAGE");
    }

    /**
     * @return MessageContainer
     */
    public function getDescriptionMessageContainer(): MessageContainer {
        return new MessageContainer("HELP_DESCRIPTION");
    }

    /**
     * @param Session $session
     * @param array $args
     */
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