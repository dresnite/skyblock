<?php
/*
 * Copyright (C) Andrés Arias - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
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