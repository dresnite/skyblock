<?php

namespace SkyBlock;

use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\Server;
use pocketmine\utils\Config;

class SkyBlockUtils {

    /**
     * @param $seconds
     * @return string
     */
    public static function printSeconds($seconds): string {
        $m = floor($seconds / 60);
        $s = floor($seconds % 60);
        return (($m < 10 ? "0" : "") . $m . ":" . ($s < 10 ? "0" : "") . (string) $s);
    }

    /**
     * Return island path
     *
     * @param $id
     * @return string
     */
    public static function getIslandPath($id) {
        return SkyBlock::getInstance()->getDataFolder() . "islands" . DIRECTORY_SEPARATOR . $id . ".json";
    }

	/**
	 * @param $id
	 * @return null|Config
	 */
	public static function getUserConfig($userName) {
		$fileName = SkyBlock::getInstance()->getDataFolder() . "users" . DIRECTORY_SEPARATOR . strtolower($userName) . ".json";
		if(is_file($fileName)){
			Server::getInstance()->getLogger()->info("SkyBlock SkyBlockUtils found user config for $userName");
			return new Config($fileName, Config::JSON);
		}
		return null;
	}

    /**
     * Return an unique island id
     *
     * @return string
     */
    public static function genIslandId() {
        return "a" . floor(microtime(true)) . "-" . rand(1,9999);
    }

    /**
     * The inverse of parse a position
     *
     * @param Position $position
     * @return string
     */
    public static function createPositionString(Position $position) {
        return "{$position->getLevel()->getName()},{$position->getX()},{$position->getY()},{$position->getZ()}";
    }

    /**
     * Return a parsed position
     *
     * @param $string
     * @return null|Position
     */
    public static function parsePosition($string) {
        $array = explode(",", $string);
        if(isset($array[3])) {
            $level = SkyBlock::getInstance()->getServer()->getLevelByName($array[0]);
            if($level instanceof Level) {
                return new Position((float) $array[1],(float) $array[2],(float) $array[3], $level);
            }
            else {
                return null;
            }
        }
        else {
            return null;
        }
    }

}