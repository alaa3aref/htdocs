<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

use App\Contracts\Config\ConfigInterface;

class ConfigManager implements ConfigInterface
{
    private ConfigInterface $repository;

    public function __construct(ConfigInterface $repository)
    {
        $this->repository = $repository;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->repository->get($key, $default);
    }

    public function has(string $key): bool
    {
        return $this->repository->has($key);
    }

    public function all(): array
    {
        return $this->repository->all();
    }
}
