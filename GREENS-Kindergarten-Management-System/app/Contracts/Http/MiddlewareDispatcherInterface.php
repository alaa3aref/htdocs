<?php

declare(strict_types=1);

namespace App\Contracts\Http;

use App\Http\RequestContext;

interface MiddlewareDispatcherInterface
{
    public function dispatch(RequestContext $context, array $middleware): RequestContext;
}
