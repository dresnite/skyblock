<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace room17\SkyBlock\command\presets;


use ReflectionException;
use room17\SkyBlock\command\IslandCommand;
use room17\SkyBlock\command\IslandCommandMap;
use room17\SkyBlock\island\IslandFactory;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\SkyBlock;
use room17\SkyBlock\utils\message\MessageContainer;

class CreateCommand extends IslandCommand {

    /** @var SkyBlock */
    private $plugin;

    public function __construct(IslandCommandMap $map) {
        $this->plugin = $map->getPlugin();
    }

    public function getName(): string {
        return "create";
    }

    public function getUsageMessageContainer(): MessageContainer {
        return new MessageContainer("CREATE_USAGE");
    }

    public function getDescriptionMessageContainer(): MessageContainer {
        return new MessageContainer("CREATE_DESCRIPTION");
    }

    /**
     * @throws ReflectionException
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkIslandAvailability($session) or $this->checkIslandCreationCooldown($session)) {
            return;
        }
        
        $generator = strtolower($args[0] ?? "Shelly");
        if($this->plugin->getGeneratorManager()->isGenerator($generator) and $this->hasPermission($session, $generator)) {
            IslandFactory::createIslandFor($session, $generator);
            $session->sendTranslatedMessage(new MessageContainer("SUCCESSFULLY_CREATED_A_ISLAND"));
        } else {
            $session->sendTranslatedMessage(new MessageContainer("NOT_VALID_GENERATOR", ["name" => $generator]));
        }
    }

    private function hasPermission(Session $session, string $generator): bool {
        return $session->getPlayer()->hasPermission("skyblock.island.$generator");
    }

    private function checkIslandAvailability(Session $session): bool {
        $hasIsland = $session->hasIsland();
        if($hasIsland) {
            $session->sendTranslatedMessage(new MessageContainer("NEED_TO_BE_FREE"));
        }
        return $hasIsland;
    }

    private function checkIslandCreationCooldown(Session $session): bool {
        $minutesSinceLastIsland =
            $session->hasLastIslandCreationTime()
            ? (microtime(true) - $session->getLastIslandCreationTime()) / 60
            : -1;
        $cooldownDuration = $this->plugin->getSettings()->getCreationCooldownDuration();
        if($minutesSinceLastIsland !== -1 and $minutesSinceLastIsland < $cooldownDuration) {
            $session->sendTranslatedMessage(new MessageContainer("YOU_HAVE_TO_WAIT", [
                "minutes" => ceil($cooldownDuration - $minutesSinceLastIsland),
            ]));
            return true;
        }
        return false;
    }

}