<?php

declare(strict_types=1);

namespace App\Contracts\Config;

interface ConfigurationCacheInterface
{
    public function has(string $key): bool;

    public function get(string $key, mixed $default = null): mixed;

    public function put(string $key, mixed $value): void;

    public function clear(): void;
}
