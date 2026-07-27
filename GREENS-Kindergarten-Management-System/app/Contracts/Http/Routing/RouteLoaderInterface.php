<?php

declare(strict_types=1);

namespace App\Contracts\Http\Routing;

interface RouteLoaderInterface
{
    public function load(mixed $source): RouteCollectionInterface;
}
