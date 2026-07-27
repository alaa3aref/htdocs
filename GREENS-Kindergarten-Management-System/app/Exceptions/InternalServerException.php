<?php

declare(strict_types=1);

namespace App\Exceptions;

class InternalServerException extends HttpException
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(500, $message, $code, $previous);
    }
}

