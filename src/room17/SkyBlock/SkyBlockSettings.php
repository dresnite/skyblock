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
use pocketmine\utils\Config;
use room17\SkyBlock\utils\Utils;

class SkyBlockSettings {

    private const VERSION = "1";

    /** @var SkyBlock */
    private $plugin;

    /** @var Config */
    private $settingsConfig;

    /** @var int */
    private $settingsVersion;

    /** @var int[] */
    private $slotsByCategory;

    /** @var Item[] */
    private $defaultChestContent;

    /** @var array */
    private $customChestContent;

    /** @var int */
    private $creationCooldownDuration;

    /** @var bool */
    private $cancelVoidDamage;

    /** @var array */
    private $blockedCommands = [];

    /** @var string */
    private $chatFormat;

    /**
     * SkyBlockSettings constructor.
     * @param SkyBlock $plugin
     */
    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
        $this->refreshData();
        $this->checkVersion();
    }

    /**
     * @param string $category
     * @return int
     */
    public function getSlotsByCategory(string $category): int {
        return $this->slotsByCategory[$category] ?? 1;
    }

    /**
     * @return Item[]
     */
    public function getDefaultChestContent(): array {
        return $this->defaultChestContent;
    }

    /**
     * @param string $generator
     * @return array
     */
    public function getCustomChestContent(string $generator): array {
        return $this->customChestContent[$generator] ?? $this->defaultChestContent;
    }

    /**
     * @return int
     */
    public function getCreationCooldownDuration(): int {
        return $this->creationCooldownDuration;
    }

    /**
     * @return bool
     */
    public function preventVoidDamage(): bool {
        return $this->cancelVoidDamage;
    }

    /**
     * @return array
     */
    public function getBlockedCommands(): array {
        return $this->blockedCommands;
    }

    /**
     * @return string
     */
    public function getChatFormat(): string {
        return $this->chatFormat;
    }

    public function refreshData(): void {
        $dataFolder = $this->plugin->getDataFolder();
        $this->settingsConfig = new Config($dataFolder . "settings.yml");
        $settingsData = $this->settingsConfig->getAll();

        $this->settingsVersion = $settingsData["Version"];
        $this->slotsByCategory = $settingsData["SlotsByCategory"];
        $this->defaultChestContent = Utils::parseItems($settingsData["ChestContent"]);

        $this->customChestContent = [];
        foreach($settingsData["CustomChestContent"] as $generator => $items) {
            if(!empty($items)) {
                $this->customChestContent[$generator] = Utils::parseItems($items);
            }
        }

        $this->creationCooldownDuration = $settingsData["CreationCooldownDuration"];
        $this->cancelVoidDamage = $settingsData["CancelVoidDamage"];
        $this->blockedCommands = $settingsData["BlockedCommands"];
        $this->chatFormat = $settingsData["ChatFormat"];
    }

    private function checkVersion(): void {
        if($this->settingsVersion == self::VERSION) {
            return;
        }
        // ToDo: Set all the new fields here
        // $this->settingsConfig->set("newField", "value");
        $this->settingsConfig->save();
        $this->plugin->getLogger()->warning("The settings version does not match with the current version of SkyBlock, all fields will have been updated");
    }

}