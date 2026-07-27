<?php

declare(strict_types=1);

namespace App\Http\Routing;

class RouteParameters
{
    /** @var array<string, string> */
    private array $parameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /** @return array<string, string> */
    public function toArray(): array
    {
        return $this->parameters;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->parameters);
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return $this->parameters[$name] ?? $default;
    }
}
