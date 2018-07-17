<?php
/*
 * Copyright (C) Andrés Arias - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

namespace SkyBlock\provider;


use SkyBlock\session\Session;
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
     * @param Session $session
     */
    public abstract function openSession(Session $session) : void;
    
    /**
     * @param Session $session
     */
    public abstract function saveSession(Session $session): void;
    
    /**
     * @param string $identifier
     */
    public abstract function checkIsle(string $identifier): void;
    
}