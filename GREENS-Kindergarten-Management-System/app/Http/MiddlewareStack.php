<?php

declare(strict_types=1);

namespace App\Http;

class MiddlewareStack
{
    /** @var list<string|object> */
    private array $stack = [];

    public function push(string|object $middleware): self
    {
        $this->stack[] = $middleware;
        return $this;
    }

    /** @return list<string|object> */
    public function all(): array
    {
        return $this->stack;
    }
}
