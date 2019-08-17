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

namespace room17\SkyBlock\provider;


use room17\SkyBlock\island\Island;
use room17\SkyBlock\session\BaseSession;
use room17\SkyBlock\SkyBlock;

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
     * @param BaseSession $session
     */
    public abstract function loadSession(BaseSession $session): void;

    /**
     * @param BaseSession $session
     */
    public abstract function saveSession(BaseSession $session): void;

    /**
     * @param string $identifier
     */
    public abstract function loadIsland(string $identifier): void;

    /**
     * @param Island $island
     */
    public abstract function saveIsland(Island $island): void;

}
