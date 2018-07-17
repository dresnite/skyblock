<?php
/*
 * Copyright (C) Andrés Arias - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
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
    public abstract function openSession(iSession $session) : void;
    
    /**
     * @param iSession $session
     */
    public abstract function saveSession(iSession $session): void;
    
    /**
     * @param string $identifier
     */
    public abstract function checkIsle(string $identifier): void;
    
    /**
     * @param Isle $isle
     */
    public abstract function saveIsle(Isle $isle): void;
    
}