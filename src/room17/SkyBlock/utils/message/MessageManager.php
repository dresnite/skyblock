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
use room17\SkyBlock\utils\Utils;

class MessageManager {

    /** @var string[] */
    private array $messages;

    public function __construct(SkyBlock $plugin) {
        $this->addMessageFile($plugin->getDataFolder() . "messages.json");
    }

    /**
     * @return string[]
     */
    public function getMessages(): array {
        return $this->messages;
    }

    public function getMessage(MessageContainer $container): string {
        $identifier = $container->getId();
        $message = $this->messages[$identifier] ?? "Message ($identifier) not found";
        $message = Utils::translateColors($message);
        foreach($container->getArgs() as $arg => $value) {
            $message = str_replace("{" . $arg . "}", $value, $message);
        }
        return $message;
    }

    public function addMessage(string $identifier, string $message): void {
        $this->messages[$identifier] = $message;
    }

    public function addMessageFile(string $filePath): void {
        $messages = json_decode(file_get_contents($filePath), true);

        foreach($messages as $identifier => $message) {
            $this->addMessage($identifier, $message);
        }
    }

}