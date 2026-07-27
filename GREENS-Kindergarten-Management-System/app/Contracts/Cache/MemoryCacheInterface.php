<?php

declare(strict_types=1);

namespace App\Contracts\Cache;

interface MemoryCacheInterface extends CacheInterface
{
    public function increment(string $key, int $step = 1): int;

    public function decrement(string $key, int $step = 1): int;

    public function remember(string $key, callable $callback, ?int $ttlSeconds = null): mixed;
}

