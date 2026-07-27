<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Contracts\Exceptions\ErrorRendererInterface;
use App\Contracts\Exceptions\ErrorResponseFactoryInterface;
use App\Contracts\Exceptions\ExceptionHandlerInterface;
use Throwable;

class ExceptionHandler implements ExceptionHandlerInterface
{
    /** @var array<string, int> */
    private array $statusCodeMap = [];

    /** @var array<string, ErrorRendererInterface> */
    private array $renderers = [];

    /** @var array<string, ErrorResponseFactoryInterface> */
    private array $responseFactories = [];

    private bool $debugMode = false;

    public function register(string $exceptionClass, int $statusCode): self
    {
        $this->statusCodeMap[$exceptionClass] = $statusCode;

        return $this;
    }

    public function map(string $exceptionClass, int $statusCode): self
    {
        return $this->register($exceptionClass, $statusCode);
    }

    public function normalize(Throwable $exception): array
    {
        $statusCode = $this->resolveStatusCode($exception);

        return [
            'statusCode' => $statusCode,
            'message' => $exception->getMessage(),
            'exception' => $exception::class,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'code' => $exception->getCode(),
        ];
    }

    public function getContext(Throwable $exception): array
    {
        $context = [
            'exception' => $exception::class,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'code' => $exception->getCode(),
        ];

        if ($exception instanceof BadRequestException) {
            $context['validation'] = $exception->getContext();
        }

        if ($exception instanceof ValidationException) {
            $context['validation'] = $exception->getContext();
        }

        if ($this->debugMode) {
            $context['trace'] = $exception->getTraceAsString();

            $previous = $exception->getPrevious();
            if ($previous !== null) {
                $context['previous'] = $this->getContext($previous);
            }
        }

        return $context;
    }

    public function propagate(Throwable $exception): Throwable
    {
        return $exception;
    }

    public function setDebugMode(bool $enabled): self
    {
        $this->debugMode = $enabled;

        return $this;
    }

    public function setProductionMode(bool $enabled): self
    {
        $this->debugMode = !$enabled;

        return $this;
    }

    public function registerRenderer(string $rendererClass, ErrorRendererInterface $renderer): self
    {
        $this->renderers[$rendererClass] = $renderer;

        return $this;
    }

    public function registerResponseFactory(string $factoryClass, ErrorResponseFactoryInterface $factory): self
    {
        $this->responseFactories[$factoryClass] = $factory;

        return $this;
    }

    private function resolveStatusCode(Throwable $exception): int
    {
        $class = $exception::class;

        if (isset($this->statusCodeMap[$class])) {
            return $this->statusCodeMap[$class];
        }

        foreach ($this->statusCodeMap as $mappedClass => $statusCode) {
            if (is_subclass_of($exception, $mappedClass)) {
                return $statusCode;
            }
        }

        if ($exception instanceof HttpException) {
            return $exception->getStatusCode();
        }

        return 500;
    }
}

