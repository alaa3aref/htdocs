<?php

declare(strict_types=1);

namespace App\Container;

use App\Contracts\Container\ContainerInterface;
use App\Exceptions\ContainerException;
use ReflectionClass;
use ReflectionNamedType;

class Resolver
{
    private ContainerInterface $container;

    /** @var array<string, int> */
    private array $resolving = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function resolve(string $abstract): mixed
    {
        if (isset($this->resolving[$abstract])) {
            throw new ContainerException(sprintf('Circular dependency detected for "%s".', $abstract));
        }

        $this->resolving[$abstract] = 1;
        $concrete = $this->container->make($abstract);
        unset($this->resolving[$abstract]);

        return $concrete;
    }

    public function build(string $abstract): object
    {
        $reflection = new ReflectionClass($abstract);
        if (!$reflection->isInstantiable()) {
            throw new ContainerException(sprintf('Class "%s" cannot be instantiated.', $abstract));
        }

        $constructor = $reflection->getConstructor();
        if ($constructor === null) {
            return new $abstract();
        }

        $arguments = [];
        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();
            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $arguments[] = $this->container->make($type->getName());
                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
                continue;
            }

            $arguments[] = null;
        }

        return $reflection->newInstanceArgs($arguments);
    }
}
