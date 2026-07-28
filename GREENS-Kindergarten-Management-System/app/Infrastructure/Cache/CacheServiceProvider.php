<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use App\Container\ServiceProvider;
use App\Contracts\Cache\CacheInterface;
use App\Contracts\Cache\CacheManagerInterface;
use App\Contracts\Cache\MemoryCacheInterface;
use App\Infrastructure\Config\Configuration;

class CacheServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(CacheManagerInterface::class, function (): CacheManagerInterface {
            $configuration = $this->container->get(Configuration::class);
            return new CacheManager($configuration);
        });

        $this->container->singleton(CacheInterface::class, function (): CacheInterface {
            $manager = $this->container->get(CacheManagerInterface::class);
            return $manager->store();
        });

        $this->container->singleton(MemoryCacheInterface::class, function (): MemoryCacheInterface {
            return new ArrayCache();
        });
    }
}

