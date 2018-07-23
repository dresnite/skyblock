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

namespace SkyBlock\provider;


use SkyBlock\isle\Isle;
use SkyBlock\session\iSession;
use SkyBlock\SkyBlock;

abstract class Provider {
    
    /** @var SkyBlock */
    protected $plugin;
    
    /**
     * Provider constructor.
     * @param SkyBlock $plugin
     */
    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
        $this->initialize();
    }
    
    public abstract function initialize(): void;
    
    /**
     * @param iSession $session
     */
    public abstract function loadSession(iSession $session) : void;
    
    /**
     * @param iSession $session
     */
    public abstract function saveSession(iSession $session): void;
    
    /**
     * @param string $identifier
     */
    public abstract function loadIsle(string $identifier): void;
    
    /**
     * @param Isle $isle
     */
    public abstract function saveIsle(Isle $isle): void;
    
}