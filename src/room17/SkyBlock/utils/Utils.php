<?php

declare(strict_types=1);

namespace room17\SkyBlock\utils;


use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class Utils {

    /**
     * @param array $items
     * @return array
     */
    public static function parseItems(array $items): array {
        $result = [];
        foreach($items as $item) {
            $item = self::parseItem($item);
            if($item != null) {
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * Parse an Item
     *
     * @param string $item
     * @return null|Item
     */
    public static function parseItem(string $item): ?Item {
        $parts = explode(",", str_replace(" ", "", $item));
        foreach($parts as $key => $value) {
            $parts[$key] = (int)$value;
        }
        if(isset($parts[0])) {
            return Item::get($parts[0], $parts[1] ?? 0, $parts[2] ?? 1);
        }
        return null;
    }

    /**
     * @param string $message
     * @return string
     */
    public static function translateColors(string $message): string {
        $message = str_replace("&", TextFormat::ESCAPE, $message);
        $message = str_replace("{BLACK}", TextFormat::BLACK, $message);
        $message = str_replace("{DARK_BLUE}", TextFormat::DARK_BLUE, $message);
        $message = str_replace("{DARK_GREEN}", TextFormat::DARK_GREEN, $message);
        $message = str_replace("{DARK_AQUA}", TextFormat::DARK_AQUA, $message);
        $message = str_replace("{DARK_RED}", TextFormat::DARK_RED, $message);
        $message = str_replace("{DARK_PURPLE}", TextFormat::DARK_PURPLE, $message);
        $message = str_replace("{ORANGE}", TextFormat::GOLD, $message);
        $message = str_replace("{GRAY}", TextFormat::GRAY, $message);
        $message = str_replace("{DARK_GRAY}", TextFormat::DARK_GRAY, $message);
        $message = str_replace("{BLUE}", TextFormat::BLUE, $message);
        $message = str_replace("{GREEN}", TextFormat::GREEN, $message);
        $message = str_replace("{AQUA}", TextFormat::AQUA, $message);
        $message = str_replace("{RED}", TextFormat::RED, $message);
        $message = str_replace("{LIGHT_PURPLE}", TextFormat::LIGHT_PURPLE, $message);
        $message = str_replace("{YELLOW}", TextFormat::YELLOW, $message);
        $message = str_replace("{WHITE}", TextFormat::WHITE, $message);
        $message = str_replace("{OBFUSCATED}", TextFormat::OBFUSCATED, $message);
        $message = str_replace("{BOLD}", TextFormat::BOLD, $message);
        $message = str_replace("{STRIKETHROUGH}", TextFormat::STRIKETHROUGH, $message);
        $message = str_replace("{UNDERLINE}", TextFormat::UNDERLINE, $message);
        $message = str_replace("{ITALIC}", TextFormat::ITALIC, $message);
        $message = str_replace("{RESET}", TextFormat::RESET, $message);
        return $message;
    }

}