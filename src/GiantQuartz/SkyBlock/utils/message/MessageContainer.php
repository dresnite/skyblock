<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace GiantQuartz\SkyBlock\utils\message;


use GiantQuartz\SkyBlock\SkyBlock;

class MessageContainer {

    /** @var string */
    private $messageId;

    /** @var array */
    private $arguments = [];

    public function __construct(string $messageId, array $arguments = []) {
        $this->messageId = $messageId;
        $this->arguments = $arguments;
    }

    public function getMessageId(): string {
        return $this->messageId;
    }

    public function getArguments(): array {
        return $this->arguments;
    }

    public function getMessage(): string {
        return SkyBlock::getInstance()->getMessageManager()->getMessage($this);
    }

}