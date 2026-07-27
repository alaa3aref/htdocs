<?php

declare(strict_types=1);

namespace App\Http;

use App\Contracts\Http\MiddlewareRegistryInterface;
use App\Exceptions\MiddlewareException;

class MiddlewareRegistry implements MiddlewareRegistryInterface
{
    /** @var array<string, mixed> */
    private array $middleware = [];

    public function register(string $name, mixed $middleware): self
    {
        $this->middleware[$name] = $middleware;
        return $this;
    }

    public function resolve(string $name): mixed
    {
        if (!$this->has($name)) {
            throw new MiddlewareException(sprintf('Middleware "%s" is not registered.', $name));
        }

        return $this->middleware[$name];
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->middleware);
    }
}
