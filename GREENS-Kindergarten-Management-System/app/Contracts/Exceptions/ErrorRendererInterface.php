<?php

declare(strict_types=1);

namespace App\Contracts\Exceptions;

use Throwable;

interface ErrorRendererInterface
{
    public function render(Throwable $exception, bool $debug = false, array $context = []): array;
}
