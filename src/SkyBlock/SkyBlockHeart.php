<?php

namespace SkyBlock;


use pocketmine\scheduler\Task;

class SkyBlockHeart extends Task {

    /** @var int */
    private $nextUpdate = 0;

    /** @var SkyBlock */
    private $instance;

    /**
     * SkyBlockHeart constructor.
     *
     * @param SkyBlock $instance
     */
    public function __construct(SkyBlock $instance) {
        $this->instance = $instance;
    }
    
    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick) {
        $this->nextUpdate++;
        if($this->nextUpdate == 120) {
            $this->nextUpdate = 0;
            $this->instance->getIslandManager()->update();
        }
        $this->instance->getInvitationHandler()->tick();
        $this->instance->getResetHandler()->tick();
    }

}