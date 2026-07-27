<?php

declare(strict_types=1);

namespace App\Contracts\Events;

interface EventInterface
{
    public function getName(): string;

    public function getPayload(): array;

    public function getMetadata(): array;

    public function withMetadata(array $metadata): self;

    public function stopPropagation(): self;

    public function isPropagationStopped(): bool;
}
