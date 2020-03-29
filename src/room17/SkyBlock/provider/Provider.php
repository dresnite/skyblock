<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace room17\SkyBlock\provider;


use room17\SkyBlock\island\Island;
use room17\SkyBlock\session\BaseSession;
use room17\SkyBlock\SkyBlock;

abstract class Provider {

    /** @var SkyBlock */
    protected $plugin;

    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
        $this->initialize();
    }

    public abstract function initialize(): void;

    public abstract function loadSession(BaseSession $session): void;

    public abstract function saveSession(BaseSession $session): void;

    public abstract function loadIsland(string $identifier): void;

    public abstract function saveIsland(Island $island): void;

}
