<?php

namespace SkyBlock;

use pocketmine\scheduler\PluginTask;

class PluginHearbeat extends PluginTask {

    /** @var int */
    private $nextUpdate = 0;

    /**
     * PluginHearbeat constructor.
     *
     * @param SkyBlock $owner
     */
    public function __construct(SkyBlock $owner) {
        parent::__construct($owner);
    }

    public function onRun(int $currentTick) {
        $this->nextUpdate++;
        /** @var SkyBlock $owner */
        $owner = $this->getOwner();
        if($this->nextUpdate == 120) {
            $this->nextUpdate = 0;
            $owner->getIslandManager()->update();
        }
        $owner->getInvitationHandler()->tick();
        $owner->getResetHandler()->tick();
    }

}