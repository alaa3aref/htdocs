<?php

declare(strict_types=1);

namespace App\Contracts\Exceptions;

use Throwable;

interface ErrorResponseFactoryInterface
{
    public function create(Throwable $exception, bool $debug = false, array $context = []): array;
}
