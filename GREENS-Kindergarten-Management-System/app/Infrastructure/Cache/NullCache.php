<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use App\Contracts\Cache\CacheInterface;

class NullCache implements CacheInterface
{
    public function has(string $key): bool
    {
        return false;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $default;
    }

    public function put(string $key, mixed $value, ?int $ttlSeconds = null): void
    {
        unset($key, $value, $ttlSeconds);
    }

    public function forget(string $key): void
    {
        unset($key);
    }

    public function clear(): void
    {
    }
}

