<?php

declare(strict_types=1);

namespace App\Http;

class Router
{
    /** @var array<string, array<int, mixed>> */
    private array $routes = [];

    public function addRoute(string $method, string $path, callable $handler): self
    {
        $this->routes[strtoupper($method)][$path] = $handler;
        return $this;
    }

    public function dispatch(Request $request): Response
    {
        $method = strtoupper($request->getMethod());
        $path = $request->getPath();

        if (!isset($this->routes[$method][$path])) {
            return new Response(404, ['Content-Type' => 'text/plain; charset=UTF-8'], 'Route not found.');
        }

        $handler = $this->routes[$method][$path];
        return $handler($request);
    }
}
