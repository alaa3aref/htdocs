<?php

declare(strict_types=1);

namespace App\Contracts\Http\Routing;

interface RouteServiceProviderInterface
{
    public function register(RouteRegistrarInterface $registrar): void;

    public function boot(RouteRegistrarInterface $registrar): void;
}
