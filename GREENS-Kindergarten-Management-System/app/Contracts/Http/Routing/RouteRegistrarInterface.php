<?php

declare(strict_types=1);

namespace App\Contracts\Http\Routing;

use App\Http\Routing\Route;
use App\Http\Routing\RouteGroup;

interface RouteRegistrarInterface
{
    public function group(RouteGroup $group, callable $callback): void;

    public function api(callable $callback): void;

    public function web(callable $callback): void;

    public function get(string $uri, mixed $action, ?string $name = null): Route;

    public function post(string $uri, mixed $action, ?string $name = null): Route;

    public function put(string $uri, mixed $action, ?string $name = null): Route;

    public function patch(string $uri, mixed $action, ?string $name = null): Route;

    public function delete(string $uri, mixed $action, ?string $name = null): Route;
}
