<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace room17\SkyBlock\event\provider;


use room17\SkyBlock\event\SkyBlockEvent;
use room17\SkyBlock\provider\Provider;

class ProviderUpdateEvent extends SkyBlockEvent {

    /** @var string|null */
    private $providerClass;

    public function __construct(string $providerClass) {
        $this->setProviderClass($providerClass);
    }

    public function getProviderClass(): string {
        return $this->providerClass;
    }

    public function setProviderClass(string $providerClass): void {
        if(!is_a($providerClass, Provider::class, true)) {
            throw new \InvalidArgumentException("$providerClass is not a valid provider, it doesn't extend the Provider class.");
        }

        $this->providerClass = $providerClass;
    }

}