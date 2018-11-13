<?php
/*
 * Copyright (C) PrimeGames - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
*/

namespace room17\SkyBlock\command\defaults;


use room17\SkyBlock\command\IsleCommand;
use room17\SkyBlock\session\Session;

class CategoryCommand extends IsleCommand {
    
    /**
     * CategoryCommand constructor.
     */
    public function __construct() {
        parent::__construct(["category", "c"], "CATEGORY_USAGE", "CATEGORY_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkIsle($session)) {
            return;
        }
        $session->sendTranslatedMessage("ISLE_CATEGORY", [
            "category" => $session->getIsle()->getCategory()
        ]);
    }
    
}