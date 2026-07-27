<?php

declare(strict_types=1);

namespace App\Exceptions;

class ServiceUnavailableException extends HttpException
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(503, $message, $code, $previous);
    }
}

