<?php

declare(strict_types=1);

namespace App\Http;

use App\Contracts\Http\PipelineInterface;

class Pipeline implements PipelineInterface
{
    /** @var list<string|object> */
    private array $middleware = [];

    public function send(RequestContext $context): RequestContext
    {
        return $context;
    }

    public function through(array $middleware): self
    {
        $this->middleware = $middleware;
        return $this;
    }

    /** @return list<string|object> */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }
}
