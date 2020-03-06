<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace GiantQuartz\SkyBlock\event\session;


use GiantQuartz\SkyBlock\event\SkyBlockEvent;
use GiantQuartz\SkyBlock\session\Session;

abstract class SessionEvent extends SkyBlockEvent {

    /** @var Session */
    private $session;

    public function __construct(Session $session) {
        $this->session = $session;
    }

    public function getSession(): Session {
        return $this->session;
    }

}