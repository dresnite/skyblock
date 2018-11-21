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

namespace room17\SkyBlock\event\isle;


use room17\SkyBlock\event\SkyBlockEvent;
use room17\SkyBlock\isle\Isle;

abstract class IsleEvent extends SkyBlockEvent {
    
    /** @var Isle */
    private $isle;
    
    /**
     * IsleEvent constructor.
     * @param Isle $isle
     */
    public function __construct(Isle $isle) {
        $this->isle = $isle;
    }
    
    /**
     * @return Isle
     */
    public function getIsle(): Isle {
        return $this->isle;
    }
    
}