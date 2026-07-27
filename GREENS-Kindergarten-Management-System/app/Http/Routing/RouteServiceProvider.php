<?php

declare(strict_types=1);

namespace App\Http\Routing;

use App\Contracts\Http\Routing\RouteCacheInterface;
use App\Contracts\Http\Routing\RouteRegistrarInterface;
use App\Contracts\Http\Routing\RouteServiceProviderInterface;

class RouteServiceProvider implements RouteServiceProviderInterface
{
    public function register(RouteRegistrarInterface $registrar): void
    {
        unset($registrar);
    }

    public function boot(RouteRegistrarInterface $registrar): void
    {
        unset($registrar);
    }

    public function createCache(): RouteCacheInterface
    {
        return new NullRouteCache();
    }
}
