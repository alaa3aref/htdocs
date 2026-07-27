<?php

declare(strict_types=1);

namespace App\Contracts\Http;

interface MiddlewareResolverInterface
{
    public function resolve(array|string|null $middleware): array;
}
