<?php

/**
 * This is GiantQuartz property.
 *
 * Copyright (C) 2016 GiantQuartz
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author GiantQuartz
 *
 */

namespace SkyBlock;

use pocketmine\level\Level;
use pocketmine\level\Position;

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
        return Main::getInstance()->getDataFolder() . "islands" . DIRECTORY_SEPARATOR . $id . ".json";
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
            $level = Main::getInstance()->getServer()->getLevelByName($array[0]);
            if($level instanceof Level) {
                return new Position($array[1], $array[2], $array[3], $level);
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