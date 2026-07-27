<?php

declare(strict_types=1);

namespace App\Container;

use App\Contracts\Container\ContainerInterface;
use App\Contracts\Container\ServiceProviderInterface;

class ProviderRepository
{
    /** @var array<int, ServiceProviderInterface> */
    private array $providers = [];

    private ?ContainerInterface $container = null;

    public function setContainer(ContainerInterface $container): self
    {
        $this->container = $container;
        return $this;
    }

    public function register(ServiceProviderInterface $provider): self
    {
        if ($this->container !== null) {
            $provider->setContainer($this->container);
        }

        $this->providers[] = $provider;
        return $this;
    }

    public function boot(): void
    {
        foreach ($this->providers as $provider) {
            $provider->register();
            $provider->boot();
        }
    }
}
