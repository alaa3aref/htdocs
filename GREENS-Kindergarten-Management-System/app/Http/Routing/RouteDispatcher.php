<?php

declare(strict_types=1);

namespace App\Http\Routing;

use App\Contracts\Http\Routing\RouteCollectionInterface;

class RouteDispatcher
{
    private RouteMatcher $matcher;

    private RouteResolver $resolver;

    public function __construct(?RouteMatcher $matcher = null, ?RouteResolver $resolver = null)
    {
        $this->matcher = $matcher ?? new RouteMatcher();
        $this->resolver = $resolver ?? new RouteResolver();
    }

    public function dispatch(string $method, string $uri, ?RouteCollectionInterface $collection = null): array
    {
        $collection ??= new RouteCollection();
        return $this->resolver->resolveByMethodAndUri($method, $uri, $collection, $this->matcher);
    }
}
