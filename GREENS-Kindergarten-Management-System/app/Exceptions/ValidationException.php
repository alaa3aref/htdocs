<?php

declare(strict_types=1);

namespace App\Exceptions;

class ValidationException extends HttpException
{
    private array $context;

    public function __construct(string $message = '', array $context = [], int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(422, $message, $code, $previous);
        $this->context = $context;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}

