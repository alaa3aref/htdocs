<?php

declare(strict_types=1);

namespace App\Http;

use App\Contracts\Http\ResponseEmitterInterface;

class ResponseEmitter implements ResponseEmitterInterface
{
    public function emit(Response $response): void
    {
        $response->send();
    }
}
