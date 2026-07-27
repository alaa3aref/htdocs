<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

use App\Contracts\Config\ConfigInterface;

class ConfigRepository implements ConfigInterface
{
    private ConfigInterface $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->config->get($key, $default);
    }

    public function has(string $key): bool
    {
        return $this->config->has($key);
    }

    public function all(): array
    {
        return $this->config->all();
    }
}
