<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */


declare(strict_types=1);

namespace room17\SkyBlock\island;


use pocketmine\world\Position;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\World;
use room17\SkyBlock\session\OfflineSession;
use room17\SkyBlock\session\Session;
use room17\SkyBlock\utils\message\MessageContainer;

class Island {

    private IslandManager $manager;
    private string $identifier;

    /** @var OfflineSession[] */
    private array $members = [];

    private bool $locked;
    private string $type;

    private World $world;

    private int $blocksBuilt;
    private string $category;

    /** @var Session[] */
    private array $cooperators = [];

    private bool $closed = false;

    public function __construct(IslandManager $manager, string $identifier, array $members, bool $locked, string $type,
                                World $world, int $blocksBuilt) {
        $this->manager = $manager;
        $this->identifier = $identifier;
        $this->locked = $locked;
        $this->type = $type;
        $this->world = $world;
        $this->blocksBuilt = $blocksBuilt;

        foreach($members as $member) {
            $this->addMember($member);
        }

        $this->updateCategory();
    }

    public function getIdentifier(): string {
        return $this->identifier;
    }

    /**
     * @return OfflineSession[]
     */
    public function getMembers(): array {
        return $this->members;
    }

    /**
     * @return Session[]
     */
    public function getPlayersOnline(): array {
        return $this->world->getPlayers();
    }

    /**
     * @return Session[]
     */
    public function getSessionsOnline(): array {
        $sessions = [];
        foreach($this->members as $member) {
            $session = $member->getOnlineSession();
            if($session != null) {
                $sessions[] = $session;
            }
        }
        return $sessions;
    }

    /**
     * Returns the sessions of the players that are in the private island's chat
     *
     * @return Player[]
     */
    public function getChattingPlayers(): array {
        $players = [];
        foreach($this->getSessionsOnline() as $session) {
            if($session->isInChat()) {
                $players[] = $session->getPlayer();
            }
        }
        return $players;
    }

    public function isLocked(): bool {
        return $this->locked;
    }

    public function getType(): string {
        return $this->type;
    }

    public function getWorld(): World {
        return $this->world;
    }

    public function getSpawnLocation(): Position {
        return $this->world->getSpawnLocation();
    }

    public function getBlocksBuilt(): int {
        return $this->blocksBuilt;
    }

    public function getCategory(): string {
        return $this->category;
    }

    public function getNextCategory(): ?string {
        return match ($this->category) {
            CategoryIds::EXTRA_LARGE => null,
            CategoryIds::LARGE => CategoryIds::EXTRA_LARGE,
            CategoryIds::MEDIUM => CategoryIds::LARGE,
            CategoryIds::SMALL => CategoryIds::MEDIUM,
            default => CategoryIds::SMALL,
        };
    }

    public function getSlots(): int {
        return $this->manager->getPlugin()->getSettings()->getSlotsByCategory($this->category);
    }

    /**
     * @return Session[]
     */
    public function getCooperators(): array {
        return $this->cooperators;
    }

    public function isCooperator(Session $session): bool {
        return isset($this->cooperators[$session->getLowerCaseName()]);
    }

    public function isClosed(): bool {
        return $this->closed;
    }

    public function canInteract(Session $session): bool {
        return $session->getIsland() === $this or $this->isCooperator($session) or $session->getPlayer()->hasPermission("skyblock.interaction");
    }

    public function setLocked(bool $locked = true): void {
        $this->locked = $locked;
    }

    /**
     * @param OfflineSession[] $members
     */
    public function setMembers(array $members): void {
        $this->members = $members;
    }

    public function setSpawnLocation(Vector3 $position): void {
        $this->world->setSpawnLocation($position);
    }

    public function setBlocksBuilt(int $blocksBuilt): void {
        $this->blocksBuilt = max(0, $blocksBuilt);
        $this->updateCategory();
    }

    public function updateCategory(): void {
        $this->category = $this->manager->getPlugin()->getSettings()->getCategoryByBlocks($this->blocksBuilt);
    }

    public function addBlock(): void {
        $this->setBlocksBuilt($this->blocksBuilt + 1);
    }

    public function destroyBlock(): void {
        $this->setBlocksBuilt($this->blocksBuilt - 1);
    }

    public function addMember(OfflineSession $session): void {
        $this->members[strtolower($session->getLowerCaseName())] = $session;
    }

    /**
     * @param Session[] $cooperators
     */
    public function setCooperators(array $cooperators): void {
        $this->cooperators = $cooperators;
    }

    public function addCooperator(Session $session): void {
        $this->cooperators[$session->getLowerCaseName()] = $session;
    }

    public function removeCooperator(Session $session): void {
        if(isset($this->cooperators[$username = $session->getLowerCaseName()])) {
            unset($this->cooperators[$username]);
        }
    }

    public function broadcastMessage(string $message): void {
        foreach($this->getSessionsOnline() as $session) {
            $session->getPlayer()->sendMessage($message);
        }
    }

    public function broadcastTranslatedMessage(MessageContainer $container): void {
        foreach($this->getSessionsOnline() as $session) {
            $session->sendTranslatedMessage($container);
        }
    }

    public function broadcastPopup(string $message): void {
        foreach($this->getSessionsOnline() as $session) {
            $session->getPlayer()->sendPopup($message);
        }
    }

    public function broadcastTranslatedPopup(MessageContainer $container): void {
        foreach($this->getSessionsOnline() as $session) {
            $session->sendTranslatedPopup($container);
        }
    }

    public function broadcastTip(string $message): void {
        foreach($this->getSessionsOnline() as $session) {
            $session->getPlayer()->sendTip($message);
        }
    }

    public function broadcastTranslatedTip(MessageContainer $container): void {
        foreach($this->getSessionsOnline() as $session) {
            $session->sendTranslatedTip($container);
        }
    }

    public function save(): void {
        $this->manager->getPlugin()->getProvider()->saveIsland($this);
    }

    public function updateMembers(): void {
        foreach($this->getSessionsOnline() as $member) {
            if($member->getIsland() !== $this) {
                unset($this->members[$member->getLowerCaseName()]);
            }
        }
    }

    public function tryToClose(): void {
        $this->updateMembers();
        if(!$this->closed and empty($this->getPlayersOnline()) and empty($this->getSessionsOnline())) {
            $this->closed = true;
            $this->manager->closeIsland($this);
        }
    }

}