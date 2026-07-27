<?php

declare(strict_types=1);

namespace App\Contracts\Cache;

interface CacheInterface
{
    public function has(string $key): bool;

    public function get(string $key, mixed $default = null): mixed;

    public function put(string $key, mixed $value, ?int $ttlSeconds = null): void;

    public function forget(string $key): void;

    public function clear(): void;
}

