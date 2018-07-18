<?php

namespace SkyBlock;

use pocketmine\level\generator\GeneratorManager;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\Server;
use pocketmine\utils\Config;
use SkyBlock\generator\SkyBlockGenerator;

class Utils {

    /**
     * Return minutes
     *
     * @param $seconds
     * @return string
     */
    public static function printSeconds($seconds) {
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
			Server::getInstance()->getLogger()->info("SkyBlock Utils found user config for $userName");
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

    /**
     * Compares a string to available world names.  If the matching world is a SkyBlock island,
     * this will return a copy of the matching SkyBlockIslandGenerator, otherwise it returns null.
     *
     * @param string $islandName
     * @return null|SkyBlockGenerator
     */
    public static function getIslandGenerator(string $islandName): ?SkyBlockGenerator {
        $level = Server::getInstance()->getLevelByName($islandName);
        $islandGeneratorName = GeneratorManager::getGenerator($level->getProvider()->getGenerator());
        $islandGenerator = new $islandGeneratorName();
        if($islandGenerator instanceof SkyBlockGenerator){
            return $islandGenerator;
        }
        return null;
    }

}