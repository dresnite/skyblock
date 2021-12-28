<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace room17\SkyBlock\island;


use pocketmine\world\World;
use room17\SkyBlock\event\island\IslandOpenEvent;
use room17\SkyBlock\event\island\IslandCloseEvent;
use room17\SkyBlock\SkyBlock;

class IslandManager {

    private SkyBlock $plugin;

    /** @var Island[] */
    private array $islands = [];

    public function __construct(SkyBlock $plugin) {
        $this->plugin = $plugin;
        $plugin->getServer()->getPluginManager()->registerEvents(new IslandListener($this), $plugin);
    }

    public function getPlugin(): SkyBlock {
        return $this->plugin;
    }

    /**
     * @return Island[]
     */
    public function getIslands(): array {
        return $this->islands;
    }

    public function getIsland(string $identifier): ?Island {
        return $this->islands[$identifier] ?? null;
    }

    public function getIslandByWorld(World $world): ?Island {
        return $this->getIsland($world->getFolderName());
    }

    public function openIsland(string $identifier, array $members, bool $locked, string $type, World $world, int $blocksBuilt): void {
        $this->islands[$identifier] = new Island($this, $identifier, $members, $locked, $type, $world, $blocksBuilt);
        (new IslandOpenEvent($this->islands[$identifier]))->call();
    }

    public function closeIsland(Island $island): void {
        $island->save();
        $server = $this->plugin->getServer();
        (new IslandCloseEvent($island))->call();
        $server->getWorldManager()->unloadWorld($island->getWorld());
        unset($this->islands[$island->getIdentifier()]);
    }

}