<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Http\Response;
use Throwable;

class Handler
{
    public function render(Throwable $exception): Response
    {
        $statusCode = $exception instanceof HttpException ? $exception->getStatusCode() : 500;
        return new Response($statusCode, ['Content-Type' => 'text/plain; charset=UTF-8'], $exception->getMessage());
    }
}
