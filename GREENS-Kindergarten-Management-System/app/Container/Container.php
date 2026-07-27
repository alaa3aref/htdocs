<?php

declare(strict_types=1);

namespace App\Container;

use App\Contracts\Container\BindingInterface;
use App\Contracts\Container\ContainerInterface as ContainerContractInterface;
use App\Contracts\Container\ServiceProviderInterface;
use App\Exceptions\ContainerException;

class Container implements ContainerContractInterface
{
    /** @var array<string, BindingInterface> */
    private array $bindings = [];

    /** @var array<string, mixed> */
    private array $instances = [];

    /** @var array<int, ServiceProviderInterface> */
    private array $providers = [];

    /** @var array<int, ServiceProviderInterface> */
    private array $bootedProviders = [];

    private Resolver $resolver;

    public function __construct()
    {
        $this->resolver = new Resolver($this);
    }

    public function bind(string $abstract, mixed $concrete = null): self
    {
        $this->bindings[$abstract] = new Binding($abstract, $concrete, false);
        return $this;
    }

    public function singleton(string $abstract, mixed $concrete = null): self
    {
        $this->bindings[$abstract] = new Binding($abstract, $concrete, true);
        return $this;
    }

    public function instance(string $abstract, mixed $instance): self
    {
        $this->instances[$abstract] = $instance;
        return $this;
    }

    public function make(string $abstract): mixed
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        if (isset($this->bindings[$abstract])) {
            $binding = $this->bindings[$abstract];
            $concrete = $binding->getConcrete();

            if ($binding->isSingleton()) {
                if (!isset($this->instances[$abstract])) {
                    $this->instances[$abstract] = $this->resolveConcrete($concrete);
                }

                return $this->instances[$abstract];
            }

            return $this->resolveConcrete($concrete);
        }

        if (class_exists($abstract)) {
            return $this->resolver->build($abstract);
        }

        throw new ContainerException(sprintf('No binding registered for "%s".', $abstract));
    }

    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]) || class_exists($abstract);
    }

    public function register(ServiceProviderInterface $provider): self
    {
        $provider->setContainer($this);
        $this->providers[] = $provider;
        return $this;
    }

    public function boot(): void
    {
        foreach ($this->providers as $provider) {
            if (in_array($provider, $this->bootedProviders, true)) {
                continue;
            }

            $provider->register();
            $provider->boot();
            $this->bootedProviders[] = $provider;
        }
    }

    public function get(string $abstract): mixed
    {
        return $this->make($abstract);
    }

    private function resolveConcrete(mixed $concrete): mixed
    {
        if (is_callable($concrete)) {
            return $concrete($this);
        }

        if (is_string($concrete) && class_exists($concrete)) {
            return $this->resolver->build($concrete);
        }

        return $concrete;
    }
}
