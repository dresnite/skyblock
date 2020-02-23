<?php

declare(strict_types=1);

namespace room17\SkyBlock\utils\message;


use room17\SkyBlock\SkyBlock;

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