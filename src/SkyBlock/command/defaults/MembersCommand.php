<?php
/**
 *  _____    ____    ____   __  __  __  ______
 * |  __ \  / __ \  / __ \ |  \/  |/_ ||____  |
 * | |__) || |  | || |  | || \  / | | |    / /
 * |  _  / | |  | || |  | || |\/| | | |   / /
 * | | \ \ | |__| || |__| || |  | | | |  / /
 * |_|  \_\ \____/  \____/ |_|  |_| |_| /_/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 */

namespace SkyBlock\command\defaults;


use SkyBlock\command\IsleCommand;
use SkyBlock\session\Session;

class MembersCommand extends IsleCommand {
    
    /**
     * MembersCommand constructor.
     */
    public function __construct() {
        parent::__construct(["members"], "MEMBERS_USAGE", "MEMBERS_DESCRIPTION");
    }
    
    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($this->checkIsle($session)) {
            return;
        }
        $members = $session->getIsle()->getMembers();
        $session->sendTranslatedMessage("MEMBERS_COMMAND_HEADER", [
            "amount" => count($members)
        ]);
        foreach($members as $member) {
            $memberSession = $member->getSession();
            if($memberSession != null) {
                $session->sendTranslatedMessage("ONLINE_MEMBER", [
                    "name" => $memberSession->getPlayer()->getName()
                ]);
            } else {
                $session->sendTranslatedMessage("OFFLINE_MEMBER", [
                    "name" => $member->getUsername()
                ]);
            }
        }
    }
    
}