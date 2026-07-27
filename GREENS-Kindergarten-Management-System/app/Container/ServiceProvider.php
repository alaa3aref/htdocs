<?php

declare(strict_types=1);

namespace App\Container;

use App\Contracts\Container\ContainerInterface;
use App\Contracts\Container\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    protected ContainerInterface $container;

    public function __construct(?ContainerInterface $container = null)
    {
        $this->container = $container ?? new Container();
    }

    public function setContainer(ContainerInterface $container): self
    {
        $this->container = $container;
        return $this;
    }

    public function register(): void
    {
    }

    public function boot(): void
    {
    }
}
