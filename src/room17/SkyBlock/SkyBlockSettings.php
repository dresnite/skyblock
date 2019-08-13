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

declare(strict_types=1);

namespace room17\SkyBlock;


use pocketmine\item\Item;
use room17\SkyBlock\utils\MessageContainer;
use room17\SkyBlock\utils\Utils;

class SkyBlockSettings {
    
    /** @var SkyBlock */
    private $plugin;
    
    /** @var array */
    private $data;
    
    /** @var int[] */
    private $slotsBySize = [];
    
    /** @var Item[] */
    private $defaultChest;
    
    /** @var array */
    private $chestPerGenerator;
    
    /** @var string[] */
    private $messages = [];

    /** @var int */
    private $cooldownDuration;

    /** @var bool */
    private $preventVoidDamage;

    /** @var array */
    private $islandBlockedCommands = [];
    
    /**
     * SkyBlockSettings constructor.
     * @param SkyBlock $plugin
     */
    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
        $this->refresh();
    }
    
    /**
     * @param string $size
     * @return int
     */
    public function getSlotsBySize(string $size): int {
        return $this->slotsBySize[$size] ?? 1;
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
     * @return int
     */
    public function getCooldownDuration(): int {
        return $this->cooldownDuration;
    }

    /**
     * @return bool
     */
    public function isPreventVoidDamage(): bool {
        return $this->preventVoidDamage;
    }

    /**
     * @return array
     */
    public function getIslandBlockedCommands(): array {
        return $this->islandBlockedCommands;
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

    public function refresh(): void {
        $this->data = json_decode(file_get_contents($this->plugin->getDataFolder() . "settings.json"), true);
        $this->messages = json_decode(file_get_contents($this->plugin->getDataFolder() . "messages.json"), true);
        $this->slotsBySize = $this->data["slots-by-size"];
        $this->defaultChest = Utils::parseItems($this->data["default-chest"]);
        $this->chestPerGenerator = [];
        foreach($this->data["chest-per-generator"] as $world => $items) {
            $this->chestPerGenerator[$world] = Utils::parseItems($items);
        }
        $this->cooldownDuration = $this->data["cooldown-duration-minutes"] ?? 20;
        $this->preventVoidDamage = $this->data["prevent-void-damage"] ?? true;
        $this->islandBlockedCommands = $this->data["commands-blocked-in-isles"] ?? [];
        $this->islandBlockedCommands = array_map("strtolower", $this->islandBlockedCommands);
    }
    
}