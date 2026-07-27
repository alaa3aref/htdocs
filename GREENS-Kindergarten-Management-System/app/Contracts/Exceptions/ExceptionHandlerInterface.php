<?php

declare(strict_types=1);

namespace App\Contracts\Exceptions;

use Throwable;

interface ExceptionHandlerInterface
{
    public function register(string $exceptionClass, int $statusCode): self;

    public function map(string $exceptionClass, int $statusCode): self;

    public function normalize(Throwable $exception): array;

    public function getContext(Throwable $exception): array;

    public function propagate(Throwable $exception): Throwable;

    public function setDebugMode(bool $enabled): self;

    public function setProductionMode(bool $enabled): self;

    public function registerRenderer(string $rendererClass, ErrorRendererInterface $renderer): self;

    public function registerResponseFactory(string $factoryClass, ErrorResponseFactoryInterface $factory): self;
}
