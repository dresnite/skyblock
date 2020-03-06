<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace GiantQuartz\SkyBlock\command\presets;


use ReflectionException;
use GiantQuartz\SkyBlock\command\IslandCommand;
use GiantQuartz\SkyBlock\command\IslandCommandMap;
use GiantQuartz\SkyBlock\island\IslandFactory;
use GiantQuartz\SkyBlock\island\IslandManager;
use GiantQuartz\SkyBlock\session\Session;
use GiantQuartz\SkyBlock\utils\message\MessageContainer;

class DisbandCommand extends IslandCommand {

    /** @var IslandManager */
    private $islandManager;

    public function __construct(IslandCommandMap $map) {
        $this->islandManager = $map->getPlugin()->getIslandManager();
    }

    public function getName(): string {
        return "disband";
    }

    public function getUsageMessageContainer(): MessageContainer {
        return new MessageContainer("DISBAND_USAGE");
    }

    public function getDescriptionMessageContainer(): MessageContainer {
        return new MessageContainer("DISBAND_DESCRIPTION");
    }

    /**
     * @throws ReflectionException
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkFounder($session)) {
            return;
        }
        IslandFactory::disbandIsland($session->getIsland());
    }

}
