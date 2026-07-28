<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use App\Contracts\Cache\MemoryCacheInterface;

class ArrayCache implements MemoryCacheInterface
{
    /** @var array<string, array{value: mixed, expiresAt: ?int}> */
    private array $items = [];

    public function has(string $key): bool
    {
        $this->evictExpired();

        return array_key_exists($key, $this->items);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->evictExpired();

        if (!array_key_exists($key, $this->items)) {
            return $default;
        }

        return $this->items[$key]['value'];
    }

    public function put(string $key, mixed $value, ?int $ttlSeconds = null): void
    {
        $this->items[$key] = [
            'value' => $value,
            'expiresAt' => $ttlSeconds !== null ? time() + $ttlSeconds : null,
        ];
    }

    public function forget(string $key): void
    {
        unset($this->items[$key]);
    }

    public function clear(): void
    {
        $this->items = [];
    }

    public function increment(string $key, int $step = 1): int
    {
        $current = (int) $this->get($key, 0);
        $new = $current + $step;
        $this->put($key, $new);

        return $new;
    }

    public function decrement(string $key, int $step = 1): int
    {
        $current = (int) $this->get($key, 0);
        $new = $current - $step;
        $this->put($key, $new);

        return $new;
    }

    public function remember(string $key, callable $callback, ?int $ttlSeconds = null): mixed
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $value = $callback();
        $this->put($key, $value, $ttlSeconds);

        return $value;
    }

    private function evictExpired(): void
    {
        $now = time();
        foreach ($this->items as $key => $item) {
            if ($item['expiresAt'] !== null && $item['expiresAt'] <= $now) {
                unset($this->items[$key]);
            }
        }
    }
}

