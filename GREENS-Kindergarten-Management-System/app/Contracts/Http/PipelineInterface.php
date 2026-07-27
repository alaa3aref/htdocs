<?php

declare(strict_types=1);

namespace App\Contracts\Http;

use App\Http\RequestContext;

interface PipelineInterface
{
    public function send(RequestContext $context): RequestContext;

    public function through(array $middleware): self;
}
