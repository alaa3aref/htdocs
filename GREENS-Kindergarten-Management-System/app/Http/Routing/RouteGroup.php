<?php

declare(strict_types=1);

namespace App\Http\Routing;

use App\Contracts\Http\Routing\RouteRegistrarInterface;

class RouteGroup
{
    private string $prefix;

    private string $namePrefix;

    private string $namespace;

    /** @var list<string> */
    private array $middleware;

    /** @var array<string, mixed> */
    private array $metadata;

    /**
     * @param list<string> $middleware
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        string $prefix = '',
        string $namePrefix = '',
        string $namespace = '',
        array $middleware = [],
        array $metadata = []
    ) {
        $this->prefix = $prefix;
        $this->namePrefix = $namePrefix;
        $this->namespace = $namespace;
        $this->middleware = $middleware;
        $this->metadata = $metadata;
    }

    public function register(RouteRegistrarInterface $registrar, callable $callback): void
    {
        $registrar->group($this, $callback);
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getNamePrefix(): string
    {
        return $this->namePrefix;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /** @return list<string> */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /** @return array<string, mixed> */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function applyTo(Route $route): Route
    {
        if ($this->namespace !== '' && $route->getNamespace() === '') {
            $route->withNamespace($this->namespace);
        }

        if ($this->middleware !== []) {
            $route->middleware($this->middleware);
        }

        if ($this->metadata !== []) {
            $route->metadata(array_merge($route->getMetadata(), $this->metadata));
        }

        return $route;
    }
}
