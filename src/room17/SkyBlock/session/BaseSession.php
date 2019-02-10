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

namespace room17\SkyBlock\session;


use room17\SkyBlock\provider\Provider;

abstract class BaseSession {
    
    /** @var SessionManager */
    protected $manager;
    
    /** @var Provider */
    protected $provider;
    
    /** @var string */
    protected $username;
    
    /** @var string|null */
    protected $isleId = null;
    
    /** @var bool */
    protected $inChat = false;
    
    /** @var int */
    protected $rank = false;
    
    const RANK_DEFAULT = 0;
    const RANK_OFFICER = 1;
    const RANK_LEADER = 2;
    const RANK_FOUNDER = 3;

    /** @var float|null */
    protected $lastIslandCreationTime;
    
    /**
     * iSession constructor.
     * @param SessionManager $manager
     * @param string $username
     */
    public function __construct(SessionManager $manager, string $username) {
        $this->manager = $manager;
        $this->username = $username;
        $this->provider = $manager->getPlugin()->getProvider();
        $this->provider->loadSession($this);
    }
    
    /**
     * @return string
     */
    public function getUsername(): string {
        return $this->username;
    }
    
    /**
     * @return null|string
     */
    public function getIsleId(): ?string {
        return $this->isleId;
    }
    
    /**
     * @return bool
     */
    public function isInChat(): bool {
        return $this->inChat;
    }
    
    /**
     * @return int
     */
    public function getRank(): int {
        return $this->rank;
    }

    /**
     * @return bool
     */
    public function hasLastIslandCreationTime(): bool {
        return $this->lastIslandCreationTime != null;
    }

    /**
     * @return float|null
     */
    public function getLastIslandCreationTime(): ?float {
        return $this->lastIslandCreationTime;
    }
    
    /**
     * @param null|string $isle
     */
    public function setIsleId(?string $isle): void {
        $this->isleId = $isle;
    }
    
    /**
     * @param bool $inChat
     */
    public function setInChat(bool $inChat = true): void {
        $this->inChat = $inChat;
    }
    
    /**
     * @param int $rank
     */
    public function setRank(int $rank = self::RANK_DEFAULT): void {
        $this->rank = $rank;
    }

    /**
     * @param float|null $lastIslandCreationTime
     */
    public function setLastIslandCreationTime(?float $lastIslandCreationTime): void  {
        $this->lastIslandCreationTime = $lastIslandCreationTime;
    }
    
    public function save(): void {
        $this->provider->saveSession($this);
    }
    
}