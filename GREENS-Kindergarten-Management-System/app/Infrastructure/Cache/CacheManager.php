<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use App\Contracts\Cache\CacheInterface;
use App\Contracts\Cache\CacheManagerInterface;
use App\Infrastructure\Config\Configuration;

class CacheManager implements CacheManagerInterface
{
    private Configuration $configuration;

    /** @var array<string, CacheInterface> */
    private array $stores = [];

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function store(?string $name = null): CacheInterface
    {
        $name ??= $this->configuration->get('cache.default', 'array');

        if (!isset($this->stores[$name])) {
            $this->stores[$name] = $this->resolve($name);
        }

        return $this->stores[$name];
    }

    public function has(string $key): bool
    {
        return $this->store()->has($key);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->store()->get($key, $default);
    }

    public function put(string $key, mixed $value, ?int $ttlSeconds = null): void
    {
        $this->store()->put($key, $value, $ttlSeconds);
    }

    public function forget(string $key): void
    {
        $this->store()->forget($key);
    }

    public function clear(): void
    {
        $this->store()->clear();
    }

    private function resolve(string $name): CacheInterface
    {
        return match ($name) {
            'array' => new ArrayCache(),
            'file' => new FileCache($this->resolvePath()),
            'null' => new NullCache(),
            default => throw new \RuntimeException(sprintf('Unsupported cache store "%s".', $name)),
        };
    }

    private function resolvePath(): string
    {
        $base = $this->configuration->get('cache.path', '');
        if ($base === '') {
            $base = $this->configuration->get('storage.private.path', 'storage/private');
        }

        return $base . DIRECTORY_SEPARATOR . 'cache';
    }
}

