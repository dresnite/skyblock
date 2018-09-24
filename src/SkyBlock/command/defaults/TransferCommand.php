<?php
/*
 * Copyright (C) PrimeGames - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
*/

namespace SkyBlock\command\defaults;


use SkyBlock\command\IsleCommand;
use SkyBlock\command\IsleCommandMap;
use SkyBlock\session\iSession;
use SkyBlock\session\Session;
use SkyBlock\SkyBlock;

class TransferCommand extends IsleCommand {
    
    /** @var SkyBlock */
    private $plugin;
    
    /**
     * TransferCommand constructor.
     * @param IsleCommandMap $map
     */
    public function __construct(IsleCommandMap $map) {
        $this->plugin = $map->getPlugin();
        parent::__construct(["transfer", "makeleader"], "TRANSFER_USAGE", "TRANSFER_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkFounder($session)) {
            return;
        } elseif(!isset($args[0])) {
            $session->sendTranslatedMessage("TRANSFER_USAGE");
            return;
        }
        $player = $this->plugin->getServer()->getPlayer($args[0]);
        if($player == null) {
            $session->sendTranslatedMessage("NOT_ONLINE_PLAYER", [
                "name" => $args[0]
            ]);
            return;
        }
        $playerSession = $this->plugin->getSessionManager()->getSession($player);
        if($this->checkClone($session, $playerSession)) {
            return;
        } elseif($playerSession->getIsle() !== $session->getIsle()) {
            $session->sendTranslatedMessage("MUST_BE_PART_OF_YOUR_ISLE", [
                "name" => $playerSession->getUsername()
            ]);
            return;
        }
        $session->setRank(iSession::RANK_DEFAULT);
        $playerSession->setRank(iSession::RANK_FOUNDER);
        $session->sendTranslatedMessage("RANK_TRANSFERRED", [
            "name" => $playerSession->getUsername()
        ]);
        $playerSession->sendTranslatedMessage("GOT_RANK_TRANSFERRED", [
            "name" => $session->getUsername()
        ]);
    }
    
}