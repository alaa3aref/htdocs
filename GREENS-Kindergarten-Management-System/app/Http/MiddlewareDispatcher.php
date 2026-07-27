<?php

declare(strict_types=1);

namespace App\Http;

use App\Contracts\Http\MiddlewareDispatcherInterface;

class MiddlewareDispatcher implements MiddlewareDispatcherInterface
{
    public function dispatch(RequestContext $context, array $middleware): RequestContext
    {
        $stack = new MiddlewareStack();
        foreach ($middleware as $entry) {
            $stack->push($entry);
        }

        return $context;
    }
}
