<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

use App\Contracts\Config\ConfigurationCacheInterface;

class NullConfigurationCache implements ConfigurationCacheInterface
{
    /** @var array<string, mixed> */
    private array $items = [];

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->items[$key] ?? $default;
    }

    public function put(string $key, mixed $value): void
    {
        $this->items[$key] = $value;
    }

    public function clear(): void
    {
        $this->items = [];
    }
}
