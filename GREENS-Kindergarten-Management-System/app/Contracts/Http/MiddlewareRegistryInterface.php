<?php

declare(strict_types=1);

namespace App\Contracts\Http;

interface MiddlewareRegistryInterface
{
    public function register(string $name, mixed $middleware): self;

    public function resolve(string $name): mixed;

    public function has(string $name): bool;
}
