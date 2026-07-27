<?php

declare(strict_types=1);

namespace App\Http\Routing;

use App\Contracts\Http\Routing\RouteCollectionInterface;
use App\Exceptions\RouteRegistrationException;

class RouteCollection implements RouteCollectionInterface
{
    /** @var array<int, Route> */
    private array $routes = [];

    /** @var array<string, Route> */
    private array $routesByName = [];

    /** @var array<string, array<int, Route>> */
    private array $routesByMethod = [];

    public function add(Route $route): self
    {
        if ($route->getName() !== '' && $this->has($route->getName())) {
            throw new RouteRegistrationException(sprintf('Route name "%s" is already registered.', $route->getName()));
        }

        $this->routes[] = $route;

        if ($route->getName() !== '') {
            $this->routesByName[$route->getName()] = $route;
        }

        foreach ($route->getMethods() as $method) {
            $this->routesByMethod[strtoupper($method)][] = $route;
        }

        return $this;
    }

    public function get(string $name): ?Route
    {
        return $this->routesByName[$name] ?? null;
    }

    public function getByMethod(string $method): array
    {
        return $this->routesByMethod[strtoupper($method)] ?? [];
    }

    public function all(): array
    {
        return $this->routes;
    }

    public function has(string $name): bool
    {
        return isset($this->routesByName[$name]);
    }

    public function clear(): void
    {
        $this->routes = [];
        $this->routesByName = [];
        $this->routesByMethod = [];
    }
}
