<?php
/*
 * Copyright (C) Andrés Arias - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

namespace SkyBlock\session;


class OfflineSession {
    
    /** @var string */
    private $username;
    
    /** @var string */
    private $isleId;
    
    /** @var bool */
    private $founder;
    
    /**
     * OfflineSession constructor.
     * @param SessionManager $manager
     * @param string $username
     */
    public function __construct(SessionManager $manager, string $username) {
        $this->username = $username;
    }
    
    /**
     * @return string
     */
    public function getUsername(): string {
        return $this->username;
    }
    
    /**
     * @return string
     */
    public function getIsleId(): string {
        return $this->isleId;
    }
    
    /**
     * @return string
     */
    public function getFounder(): string {
        return $this->founder;
    }
    
}