<?php

declare(strict_types=1);

namespace App\Contracts\Http;

use App\Http\Response;

interface ResponseEmitterInterface
{
    public function emit(Response $response): void;
}
