<?php

declare(strict_types=1);

namespace App\Contracts\Http;

use App\Http\RequestContext;

interface HttpKernelInterface
{
    public function handle(RequestContext $context): RequestContext;

    public function terminate(RequestContext $context): void;
}
