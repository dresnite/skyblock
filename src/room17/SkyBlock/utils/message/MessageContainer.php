<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace room17\SkyBlock\utils\message;


use room17\SkyBlock\SkyBlock;

class MessageContainer {

    /** @var string */
    private $id;

    /** @var array */
    private $args;

    public function __construct(string $id, array $arguments = []) {
        $this->id = $id;
        $this->args = $arguments;
    }

    public function __toString(): string {
        return $this->getMessage();
    }

    public function getId(): string {
        return $this->id;
    }

    public function getArgs(): array {
        return $this->args;
    }

    public function getMessage(): string {
        return SkyBlock::getInstance()->getMessageManager()->getMessage($this);
    }

}