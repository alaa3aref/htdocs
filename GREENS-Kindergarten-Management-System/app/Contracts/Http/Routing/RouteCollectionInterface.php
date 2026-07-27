<?php

declare(strict_types=1);

namespace App\Contracts\Http\Routing;

use App\Http\Routing\Route;

interface RouteCollectionInterface
{
    public function add(Route $route): self;

    public function get(string $name): ?Route;

    public function getByMethod(string $method): array;

    public function all(): array;

    public function has(string $name): bool;

    public function clear(): void;
}
