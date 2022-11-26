<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace room17\SkyBlock\session;


use room17\SkyBlock\island\RankIds;
use room17\SkyBlock\provider\Provider;

abstract class BaseSession {

    protected SessionManager $manager;
    protected Provider $provider;

    protected string $lowerCaseName;
    protected ?string $islandId = null;

    protected bool $inChat = false;
    protected int $rank;

    protected ?float $lastIslandCreationTime;

    public function __construct(SessionManager $manager, string $name) {
        $this->manager = $manager;
        $this->lowerCaseName = strtolower($name);
        $this->provider = $manager->getPlugin()->getProvider();
        $this->provider->loadSession($this);
    }

    public function getLowerCaseName(): string {
        return $this->lowerCaseName;
    }

    public function getIslandId(): ?string {
        return $this->islandId;
    }

    public function isInChat(): bool {
        return $this->inChat;
    }

    public function getRank(): int {
        return $this->rank;
    }

    public function hasLastIslandCreationTime(): bool {
        return $this->lastIslandCreationTime != null;
    }

    public function getLastIslandCreationTime(): ?float {
        return $this->lastIslandCreationTime;
    }

    public function setIslandId(?string $identifier): void {
        $this->islandId = $identifier;
    }

    public function setInChat(bool $inChat = true): void {
        $this->inChat = $inChat;
    }

    public function setRank(int $rank = RankIds::MEMBER): void {
        $this->rank = $rank;
    }

    public function setLastIslandCreationTime(?float $lastIslandCreationTime): void {
        $this->lastIslandCreationTime = $lastIslandCreationTime;
    }

    public function save(): void {
        $this->provider->saveSession($this);
    }

}