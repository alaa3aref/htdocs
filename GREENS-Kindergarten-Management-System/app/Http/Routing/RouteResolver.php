<?php

declare(strict_types=1);

namespace App\Http\Routing;

use App\Contracts\Http\Routing\RouteCollectionInterface;

class RouteResolver
{
    public function resolveByName(string $name, RouteCollectionInterface $collection): ?Route
    {
        return $collection->get($name);
    }

    public function resolveByMethodAndUri(string $method, string $uri, RouteCollectionInterface $collection, ?RouteMatcher $matcher = null): array
    {
        $matcher ??= new RouteMatcher();

        foreach ($collection->getByMethod($method) as $route) {
            $parameters = $matcher->match($route, $uri);
            if ($parameters !== null) {
                return ['route' => $route, 'parameters' => $parameters];
            }
        }

        return ['route' => null, 'parameters' => new RouteParameters()];
    }
}
