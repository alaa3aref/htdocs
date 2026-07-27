<?php

declare(strict_types=1);

namespace App\Http\Routing;

use App\Contracts\Http\Routing\RouteCollectionInterface;
use App\Contracts\Http\Routing\RouteRegistrarInterface;
use App\Exceptions\RouteRegistrationException;

class RouteRegistrar implements RouteRegistrarInterface
{
    private RouteCollectionInterface $collection;

    /** @var array<int, RouteGroup> */
    private array $groups = [];

    public function __construct(RouteCollectionInterface $collection)
    {
        $this->collection = $collection;
    }

    public function group(RouteGroup $group, callable $callback): void
    {
        $this->groups[] = $group;
        $callback($this);
        array_pop($this->groups);
    }

    public function api(callable $callback): void
    {
        $this->group(new RouteGroup(prefix: '/api', namePrefix: 'api.', metadata: ['group' => 'api']), $callback);
    }

    public function web(callable $callback): void
    {
        $this->group(new RouteGroup(prefix: '/', namePrefix: 'web.', metadata: ['group' => 'web']), $callback);
    }

    public function get(string $uri, mixed $action, ?string $name = null): Route
    {
        return $this->addRoute(['GET'], $uri, $action, $name);
    }

    public function post(string $uri, mixed $action, ?string $name = null): Route
    {
        return $this->addRoute(['POST'], $uri, $action, $name);
    }

    public function put(string $uri, mixed $action, ?string $name = null): Route
    {
        return $this->addRoute(['PUT'], $uri, $action, $name);
    }

    public function patch(string $uri, mixed $action, ?string $name = null): Route
    {
        return $this->addRoute(['PATCH'], $uri, $action, $name);
    }

    public function delete(string $uri, mixed $action, ?string $name = null): Route
    {
        return $this->addRoute(['DELETE'], $uri, $action, $name);
    }

    private function addRoute(array $methods, string $uri, mixed $action, ?string $name = null): Route
    {
        $route = new Route($this->resolveUri($uri), $action, $methods);

        foreach ($this->groups as $group) {
            $route = $group->applyTo($route);
        }

        if ($name !== null) {
            $route->name($this->resolveName($name));
        }

        if ($route->getName() !== '') {
            if ($this->collection->has($route->getName())) {
                throw new RouteRegistrationException(sprintf('Route name "%s" is already registered.', $route->getName()));
            }
        }

        $this->collection->add($route);

        return $route;
    }

    private function resolveUri(string $uri): string
    {
        $resolved = $uri;
        foreach ($this->groups as $group) {
            if ($group->getPrefix() !== '') {
                $resolved = $this->normalizePrefix($group->getPrefix()) . $this->normalizeRouteUri($resolved);
            }
        }

        return $this->normalizeRouteUri($resolved);
    }

    private function resolveName(string $name): string
    {
        $resolved = $name;
        foreach (array_reverse($this->groups) as $group) {
            if ($group->getNamePrefix() !== '') {
                $resolved = $group->getNamePrefix() . $resolved;
            }
        }

        return $resolved;
    }

    private function normalizeRouteUri(string $uri): string
    {
        if ($uri === '') {
            return '/';
        }

        if (!str_starts_with($uri, '/')) {
            return '/' . $uri;
        }

        return $uri;
    }

    private function normalizePrefix(string $prefix): string
    {
        return str_starts_with($prefix, '/') ? $prefix : '/' . $prefix;
    }
}
