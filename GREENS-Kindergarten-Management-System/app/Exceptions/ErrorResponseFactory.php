<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Contracts\Exceptions\ErrorResponseFactoryInterface;
use Throwable;

class ErrorResponseFactory implements ErrorResponseFactoryInterface
{
    public function create(Throwable $exception, bool $debug = false, array $context = []): array
    {
        $statusCode = 500;

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
        }

        $response = [
            'statusCode' => $statusCode,
            'message' => $exception->getMessage(),
        ];

        if ($debug) {
            $response['exception'] = $exception::class;
            $response['file'] = $exception->getFile();
            $response['line'] = $exception->getLine();
            $response['trace'] = $exception->getTraceAsString();
            $response['context'] = $context;
        }

        return $response;
    }
}

