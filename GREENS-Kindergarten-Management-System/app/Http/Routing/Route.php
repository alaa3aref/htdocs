<?php

declare(strict_types=1);

namespace App\Http\Routing;

class Route
{
    private string $uri;

    /** @var list<string> */
    private array $methods;

    private mixed $action;

    private string $name = '';

    private string $namespace = '';

    /** @var list<string> */
    private array $middleware = [];

    /** @var array<string, mixed> */
    private array $metadata = [];

    /** @param list<string>|string $methods */
    public function __construct(string $uri, mixed $action, array|string $methods = ['GET'])
    {
        $this->uri = $uri;
        $this->action = $action;
        $this->methods = is_string($methods) ? [$methods] : $methods;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    /** @return list<string> */
    public function getMethods(): array
    {
        return $this->methods;
    }

    public function getAction(): mixed
    {
        return $this->action;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /** @return list<string> */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /** @return array<string, mixed> */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function withNamespace(string $namespace): self
    {
        $this->namespace = $namespace;
        return $this;
    }

    public function middleware(array|string $middleware): self
    {
        $this->middleware = is_string($middleware) ? [$middleware] : array_values($middleware);
        return $this;
    }

    public function metadata(array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function withUri(string $uri): self
    {
        $this->uri = $uri;
        return $this;
    }
}
