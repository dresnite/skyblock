<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace room17\SkyBlock\event\session;


use room17\SkyBlock\event\SkyBlockEvent;
use room17\SkyBlock\session\Session;

class SessionCreationEvent extends SkyBlockEvent {

    /** @var string */
    private $sessionClass;

    public function __construct(string $sessionClass){
        $this->sessionClass = $sessionClass;
    }

    public function getSessionClass() : string{
        return $this->sessionClass;
    }

    public function setSessionClass(string $sessionClass) : void{
        if(!is_a($sessionClass, Session::class, true)) {
            throw new \InvalidArgumentException("$sessionClass must extend Session");
        }

        $this->sessionClass = $sessionClass;
    }

}