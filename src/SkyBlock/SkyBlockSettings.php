<?php
/**
 *  _____    ____    ____   __  __  __  ______
 * |  __ \  / __ \  / __ \ |  \/  |/_ ||____  |
 * | |__) || |  | || |  | || \  / | | |    / /
 * |  _  / | |  | || |  | || |\/| | | |   / /
 * | | \ \ | |__| || |__| || |  | | | |  / /
 * |_|  \_\ \____/  \____/ |_|  |_| |_| /_/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 */

namespace SkyBlock;


use pocketmine\item\Item;

class SkyBlockSettings {
    
    /** @var SkyBlock */
    private $plugin;
    
    /** @var array */
    private $data;
    
    /** @var Item[] */
    private $defaultChest;
    
    /** @var array */
    private $chestPerGenerator;
    
    /** @var string[] */
    private $messages = [];
    
    /**
     * SkyBlockSettings constructor.
     * @param SkyBlock $plugin
     */
    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
        $this->refresh();
    }
    
    /**
     * @return Item[]
     */
    public function getDefaultChest(): array {
        return $this->defaultChest;
    }
    
    /**
     * @param string $generator
     * @return array
     */
    public function getChestPerGenerator(string $generator): array {
        return $this->chestPerGenerator[$generator] ?? $this->defaultChest;
    }
    
    /**
     * @return string[]
     */
    public function getMessages(): array {
        return $this->messages;
    }
    
    /**
     * @param string $identifier
     * @param array $args
     * @return string
     */
    public function getMessage(string $identifier, array $args = []): string {
        $message = $this->messages[$identifier] ?? "Message ($identifier) not found";
        $message = SkyBlock::translateColors($message);
        foreach($args as $arg => $value) {
            $message = str_replace("{" . $arg . "}", $value, $message);
        }
        return $message;
    }
    
    public function refresh(): void {
        $this->data = json_decode(file_get_contents($this->plugin->getDataFolder() . "settings.json"), true);
        $this->messages = json_decode(file_get_contents($this->plugin->getDataFolder() . "messages.json"), true);
        $this->defaultChest = SkyBlock::parseItems($this->data["default-chest"]);
        $this->chestPerGenerator = [];
        foreach($this->data["chest-per-generator"] as $world => $items) {
            $this->chestPerGenerator[$world] = SkyBlock::parseItems($items);
        }
    }
    
}