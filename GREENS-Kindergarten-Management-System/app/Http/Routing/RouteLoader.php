<?php

declare(strict_types=1);

namespace App\Http\Routing;

use App\Contracts\Http\Routing\RouteCollectionInterface;
use App\Contracts\Http\Routing\RouteLoaderInterface;

class RouteLoader implements RouteLoaderInterface
{
    public function load(mixed $source): RouteCollectionInterface
    {
        if (is_string($source) && is_file($source)) {
            $source = require $source;
        }

        $collection = new RouteCollection();
        if (!is_array($source)) {
            return $collection;
        }

        $registrar = new RouteRegistrar($collection);
        foreach ($source as $definition) {
            if (!is_array($definition) || count($definition) < 3) {
                continue;
            }

            [$method, $uri, $action] = array_values($definition);
            $name = $definition[3] ?? null;
            $methods = is_array($method) ? $method : [$method];

            $route = new Route($uri, $action, $methods);
            if ($name !== null) {
                $route->name((string) $name);
            }

            $collection->add($route);
        }

        return $collection;
    }
}
