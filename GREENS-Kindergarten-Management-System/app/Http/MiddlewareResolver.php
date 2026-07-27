<?php

declare(strict_types=1);

namespace App\Http;

use App\Contracts\Http\MiddlewareResolverInterface;
use App\Exceptions\MiddlewareException;

class MiddlewareResolver implements MiddlewareResolverInterface
{
    public function resolve(array|string|null $middleware): array
    {
        if ($middleware === null) {
            return [];
        }

        if (is_string($middleware)) {
            return [$middleware];
        }

        if (!is_array($middleware)) {
            throw new MiddlewareException('Middleware must be a string or array.');
        }

        return array_values($middleware);
    }
}
