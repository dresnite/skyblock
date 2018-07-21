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

namespace SkyBlock\command;


use SkyBlock\session\Session;

abstract class IsleCommand {
    
    /** @var array */
    private $aliases = [];
    
    /** @var string */
    private $usageMessageId;
    
    /** @var string */
    private $descriptionMessageId;
    
    /**
     * IsleCommand constructor.
     * @param array $aliases
     * @param string $usageMessageId
     * @param string $descriptionMessageId
     */
    public function __construct(array $aliases, string $usageMessageId, string $descriptionMessageId) {
        $this->aliases = array_map("strtolower", $aliases);
        $this->usageMessageId = $usageMessageId;
        $this->descriptionMessageId = $descriptionMessageId;
    }
    
    /**
     * @return array
     */
    public function getAliases(): array {
        return $this->aliases;
    }
    
    /**
     * @return string
     */
    public function getUsageMessageId(): string {
        return $this->usageMessageId;
    }
    
    /**
     * @return string
     */
    public function getDescriptionMessageId(): string {
        return $this->descriptionMessageId;
    }
    
    /**
     * @param Session $session
     * @param array $args
     * @return void
     */
    public abstract function onCommand(Session $session, array $args): void;
    
}