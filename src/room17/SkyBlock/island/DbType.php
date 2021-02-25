<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */


declare(strict_types=1);

namespace room17\SkyBlock\island;


final class DbType {

    public const TYPE_INTEGER = 0;
    public const TYPE_TEXT = 1;
    public const TYPE_BOOLEAN = 2;

    private function __construct() {
        // NOOP
    }

}