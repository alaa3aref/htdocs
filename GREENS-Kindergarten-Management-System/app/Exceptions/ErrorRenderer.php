<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Contracts\Exceptions\ErrorRendererInterface;
use Throwable;

class ErrorRenderer implements ErrorRendererInterface
{
    public function render(Throwable $exception, bool $debug = false, array $context = []): array
    {
        $rendered = [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ];

        $rendered['exception'] = $exception::class;

        if ($exception instanceof HttpException) {
            $rendered['statusCode'] = $exception->getStatusCode();
        }

        if ($debug) {
            $rendered['trace'] = $exception->getTraceAsString();
            $rendered['context'] = $context;

            $previous = $exception->getPrevious();
            if ($previous !== null) {
                $rendered['previous'] = $this->render($previous, $debug);
            }
        }

        return $rendered;
    }
}

