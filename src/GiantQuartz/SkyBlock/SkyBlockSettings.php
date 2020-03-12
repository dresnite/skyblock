<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace GiantQuartz\SkyBlock;


use GiantQuartz\SkyBlock\island\Island;
use pocketmine\item\Item;
use pocketmine\utils\Config;
use GiantQuartz\SkyBlock\utils\Utils;

class SkyBlockSettings {

    private const VERSION = "2";

    /** @var SkyBlock */
    private $plugin;

    /** @var Config */
    private $settingsConfig;

    /** @var int */
    private $settingsVersion;

    /** @var int[] */
    private $slotsByCategory;

    /** @var int[] */
    private $blocksByCategory;

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

    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
        $this->refreshData();
        $this->checkVersion();
    }

    public function getSlotsByCategory(string $category): int {
        return $this->slotsByCategory[$category] ?? 1;
    }

    public function getCategoryByBlocks(int $blocks): string {
        if($blocks >= $this->blocksByCategory["L"]) {
            $category = Island::CATEGORY_EXTRA_LARGE;
        } elseif($blocks >= $this->blocksByCategory["M"]) {
            $category = Island::CATEGORY_LARGE;
        } elseif($blocks >= $this->blocksByCategory["S"]) {
            $category = Island::CATEGORY_MEDIUM;
        } elseif($blocks >= $this->blocksByCategory["XS"]) {
            $category = Island::CATEGORY_SMALL;
        } else {
            $category = Island::CATEGORY_EXTRA_SMALL;
        }
        return $category;
    }

    public function getDefaultChestContent(): array {
        return $this->defaultChestContent;
    }

    public function getCustomChestContent(string $generator): array {
        return $this->customChestContent[$generator] ?? $this->defaultChestContent;
    }

    public function getCreationCooldownDuration(): int {
        return $this->creationCooldownDuration;
    }

    public function preventVoidDamage(): bool {
        return $this->cancelVoidDamage;
    }

    /**
     * @return array
     */
    public function getBlockedCommands(): array {
        return $this->blockedCommands;
    }

    public function getChatFormat(): string {
        return $this->chatFormat;
    }

    public function refreshData(): void {
        $dataFolder = $this->plugin->getDataFolder();
        $this->settingsConfig = new Config($dataFolder . "settings.yml");
        $settingsData = $this->settingsConfig->getAll();

        $this->settingsVersion = $settingsData["Version"];
        $this->slotsByCategory = $settingsData["SlotsByCategory"];
        $this->blocksByCategory = $settingsData["CategoryByBlocks"];
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

        $this->settingsConfig->set("Version", self::VERSION);
        $this->settingsConfig->set("CategoryByBlocks", [
            "XS" => 500,
            "S" => 1000,
            "M" => 5000,
            "XL" => 10000
        ]);

        $this->settingsConfig->save();
        $this->plugin->getLogger()->warning("The settings file does not match with the current version of SkyBlock, the file has been updated");
    }

}