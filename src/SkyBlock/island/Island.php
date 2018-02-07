<?php

namespace SkyBlock\island;


use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\Config;
use SkyBlock\Utils;

class Island {

    /** @var Config */
    private $config;

    /** @var string */
    private $ownerName;

    /** @var string */
    private $identifier;

    /** @var Player[] */
    private $playersOnline = [];

    /** @var string[] */
    private $members;

    /** @var bool */
    private $locked;

    /** @var string */
    private $home;

    /** @var string */
    private $generator;

    /**
     * Island constructor.
     *
     * @param Config $config
     * @param string $ownerName
     * @param string $identifier
     * @param array $members
     * @param bool $locked
     * @param string $home
     * @param string $generator
     */
    public function __construct(Config $config, $ownerName, $identifier, $members, $locked, $home, $generator) {
        $this->config = $config;
        $this->ownerName = $ownerName;
        $this->identifier = $identifier;
        $this->members = $members;
        $this->locked = $locked;
        $this->home = $home;
        $this->generator = $generator;
    }

    /**
     * Return island config
     *
     * @return Config
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * Return owner name
     *
     * @return string
     */
    public function getOwnerName() {
        return $this->ownerName;
    }

    /**
     * Return identifier
     *
     * @return string
     */
    public function getIdentifier() {
        return $this->identifier;
    }

    /**
     * Return island online players
     *
     * @return Player[]
     */
    public function getPlayersOnline() {
        return $this->playersOnline;
    }

    /**
     * Return island members
     *
     * @return string[]
     */
    public function getMembers() {
        return $this->members;
    }

    /**
     * Return if the island is locked
     *
     * @return boolean
     */
    public function isLocked() {
        return $this->locked;
    }

    /**
     * Return island home not parsed
     *
     * @return string
     */
    public function getHome() {
        return $this->home;
    }

    /**
     * Return home position
     *
     * @return Position
     */
    public function getHomePosition() {
        return Utils::parsePosition($this->home);
    }

    /**
     * Return if the island has a home
     *
     * @return bool
     */
    public function hasHome() {
        return $this->getHomePosition() instanceof Position;
    }

    /**
     * Return all members (also the owner)
     *
     * @return array|\string[]
     */
    public function getAllMembers() {
        $members = $this->members;
        $members[] = $this->ownerName;
        return $members;
    }

    /**
     * Return island generator
     *
     * @return string
     */
    public function getGenerator() {
        return $this->generator;
    }

    /**
     * Add a player to the island
     *
     * @param Player $player
     */
    public function addPlayer(Player $player) {
        $this->playersOnline[] = $player;
    }

    /**
     * Set owner name
     *
     * @param $ownerName
     */
    public function setOwnerName($ownerName) {
        $this->ownerName = strtolower($ownerName);
    }

    /**
     * Set island identifier
     *
     * @param string $identifier
     */
    public function setIdentifier($identifier) {
        $this->identifier = $identifier;
    }

    /**
     * Set island players online
     *
     * @param Player[] $playersOnline
     */
    public function setPlayersOnline($playersOnline) {
        $this->playersOnline = $playersOnline;
    }

    /**
     * Set island members
     *
     * @param string[] $members
     */
    public function setMembers($members) {
        $this->members = $members;
    }

    /**
     * Add a member to the team
     *
     * @param Player $player
     */
    public function addMember(Player $player) {
        $this->members[] = strtolower($player->getName());
    }

    /**
     * Set the island locked
     *
     * @param boolean $locked
     */
    public function setLocked($locked = true) {
        $this->locked = $locked;
    }

    /**
     * Set not parsed home
     *
     * @param string $home
     */
    public function setHome($home) {
        $this->home = $home;
    }

    /**
     * Set home position
     *
     * @param Position $position
     */
    public function setHomePosition(Position $position) {
        $this->home = Utils::createPositionString($position);
    }

    /**
     * Set island config
     *
     * @param Config $config
     */
    public function setConfig(Config $config) {
        $this->config = $config;
    }

    /**
     * Set island generator
     *
     * @param string $generator
     */
    public function setGenerator($generator) {
        $this->generator = $generator;
    }

    /**
     * Tries to remove a player
     *
     * @param Player $player
     */
    public function tryRemovePlayer(Player $player) {
        if(in_array($player, $this->playersOnline)) {
            unset($this->playersOnline[array_search($player, $this->playersOnline)]);
        }
    }

    /**
     * Remove member
     *
     * @param string $string
     */
    public function removeMember($string) {
        if(in_array($string, $this->members)) {
            unset($this->members[array_search($string, $this->members)]);
        }
    }

    public function update() {
        $this->config->set("owner", $this->getOwnerName());
        $this->config->set("home", $this->getHome());
        $this->config->set("locked", $this->isLocked());
        $this->config->set("members", $this->getMembers());
        $this->config->save();
    }

}