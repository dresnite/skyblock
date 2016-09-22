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

use pocketmine\scheduler\PluginTask;

class PluginHearbeat extends PluginTask {

    /** @var int */
    private $nextUpdate = 0;

    /**
     * PluginHearbeat constructor.
     *
     * @param Main $owner
     */
    public function __construct(Main $owner) {
        parent::__construct($owner);
    }

    public function onRun($currentTick) {
        $this->nextUpdate++;
        /** @var Main $owner */
        $owner = $this->getOwner();
        if($this->nextUpdate == 120) {
            $this->nextUpdate = 0;
            $owner->getIslandManager()->update();
        }
        $owner->getInvitationHandler()->tick();
        $owner->getResetHandler()->tick();
    }

}