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


use SkyBlock\session\iSession;
use SkyBlock\session\Session;

abstract class IsleCommand {
    
    /** @var string */
    private $name;
    
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
        $this->name = array_shift($this->aliases);
        $this->usageMessageId = $usageMessageId;
        $this->descriptionMessageId = $descriptionMessageId;
    }
    
    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
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
     * @return bool
     */
    public function checkIsle(Session $session): bool {
        if($session->hasIsle()) {
            return false;
        }
        $session->sendTranslatedMessage("NEED_ISLE");
        return true;
    }
    
    /**
     * @param Session $session
     * @return bool
     */
    public function checkLeader(Session $session): bool {
        if($this->checkIsle($session)) {
            return true;
        } elseif($session->getRank() == iSession::RANK_FOUNDER or $session->getRank() == iSession::RANK_LEADER) {
            return false;
        }
        $session->sendTranslatedMessage("MUST_BE_LEADER");
        return true;
    }
    
    /**
     * @param Session $session
     * @return bool
     */
    public function checkOfficer(Session $session): bool {
        if($this->checkIsle($session)) {
            return true;
        } elseif($session->getRank() == iSession::RANK_OFFICER) {
            return false;
        }
        $session->sendTranslatedMessage("MUST_BE_OFFICER");
        return true;
    }
    
    /**
     * @param Session $session
     * @param array $args
     * @return void
     */
    public abstract function onCommand(Session $session, array $args): void;
    
}