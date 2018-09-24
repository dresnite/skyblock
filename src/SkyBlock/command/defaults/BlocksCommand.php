<?php
/*
 * Copyright (C) PrimeGames - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
*/

namespace SkyBlock\command\defaults;


use SkyBlock\command\IsleCommand;
use SkyBlock\session\Session;

class BlocksCommand extends IsleCommand {
    
    /**
     * BlocksCommand constructor.
     */
    public function __construct() {
        parent::__construct(["blocks"], "BLOCKS_USAGE", "BLOCKS_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkIsle($session)) {
            return;
        }
        $session->sendTranslatedMessage("ISLE_BLOCKS", [
            "amount" => $session->getIsle()->getBlocksBuilt()
        ]);
    }
    
}