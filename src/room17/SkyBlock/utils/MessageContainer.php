<?php

declare(strict_types=1);

namespace room17\SkyBlock\utils;


use room17\SkyBlock\SkyBlock;

class MessageContainer {

    /** @var string */
    private $messageId;

    /** @var array */
    private $arguments = [];

    /**
     * MessageContainer constructor.
     * @param string $messageId
     * @param array $arguments
     */
    public function __construct(string $messageId, $arguments = []) {
        $this->messageId = $messageId;
        $this->arguments = $arguments;
    }

    /**
     * @return string
     */
    public function getMessageId(): string {
        return $this->messageId;
    }

    /**
     * @return array
     */
    public function getArguments(): array {
        return $this->arguments;
    }

    public function getMessage(): string {
        return SkyBlock::getInstance()->getSettings()->getMessage($this);
    }

}