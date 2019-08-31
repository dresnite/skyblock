<?php

declare(strict_types=1);

namespace room17\SkyBlock\utils\message;


use room17\SkyBlock\SkyBlock;
use room17\SkyBlock\utils\Utils;

class MessageManager {

    /** @var string[] */
    private $messages;

    /**
     * MessageManager constructor.
     * @param SkyBlock $plugin
     */
    public function __construct(SkyBlock $plugin) {
        $this->messages = json_decode(file_get_contents($plugin->getDataFolder() . "messages.json"), true);
    }

    /**
     * @return string[]
     */
    public function getMessages(): array {
        return $this->messages;
    }

    /**
     * @param MessageContainer $container
     * @return string
     */
    public function getMessage(MessageContainer $container): string {
        $identifier = $container->getMessageId();
        $message = $this->messages[$identifier] ?? "Message ($identifier) not found";
        $message = Utils::translateColors($message);
        foreach($container->getArguments() as $arg => $value) {
            $message = str_replace("{" . $arg . "}", $value, $message);
        }
        return $message;
    }

    /**
     * @param string $identifier
     * @param string $message
     */
    public function addMessage(string $identifier, string $message): void {
        $this->messages[$identifier] = $message;
    }

}