<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */


declare(strict_types=1);

namespace room17\SkyBlock\island;


class IslandCustomValue {

    /** @var string */
    private $identifier;

    /** @var mixed */
    private $value;

    /** @var int */
    private $dbType;

    /**
     * IslandCustomValue constructor.
     *
     * @param string $identifier
     * @param mixed $value
     * @param int $dbType
     *
     * @see DbType
     */
    public function __construct(string $identifier, $value, int $dbType){
        $this->identifier = $identifier;
        $this->value = $value;
        $this->dbType = $dbType;
    }

    public function getIdentifier(): string {
        return $this->identifier;
    }

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) : void{
        $this->value = $value;
    }

    public function getDbType(): int {
        return $this->dbType;
    }

}