<?php

namespace SkyBlock;


use pocketmine\scheduler\Task;

class PluginHearbeat extends Task {

    /** @var int */
    private $nextUpdate = 0;

    /** @var SkyBlock */
    private $instance;

    /**
     * PluginHearbeat constructor.
     *
     * @param SkyBlock $owner
     */
    public function __construct(SkyBlock $instance) {
        $this->instance = $instance;
    }

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