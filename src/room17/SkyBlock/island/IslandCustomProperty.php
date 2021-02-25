<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */


declare(strict_types=1);

namespace room17\SkyBlock\island;


class IslandCustomProperty {

    /** @var string */
    private $name;

    /** @var int */
    private $dbType;

    /**
     * IslandCustomProperty constructor.
     *
     * @param string $name
     * @param int $dbType
     *
     * @see DbType
     */
    public function __construct(string $name, int $dbType) {
        if(!ctype_alpha($name) or strlen($name) === 0 or strlen($name) > 10) {
            throw new \InvalidArgumentException("Tried to create an IslandCustomProperty with an invalid name. The name must be alphanumeric and have length less than 10");
        }

        $this->name = $name;
        $this->dbType = $dbType;
    }

    public function getName() : string{
        return $this->name;
    }

    public function getDbType() : int{
        return $this->dbType;
    }

}