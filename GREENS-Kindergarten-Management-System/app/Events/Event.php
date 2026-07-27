<?php

declare(strict_types=1);

namespace App\Events;

use App\Contracts\Events\EventInterface;

class Event implements EventInterface
{
    public function __construct(
        private string $name,
        private array $payload = [],
        private array $metadata = [],
        private bool $propagationStopped = false
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function withMetadata(array $metadata): self
    {
        $event = clone $this;
        $event->metadata = $metadata;

        return $event;
    }

    public function stopPropagation(): self
    {
        $this->propagationStopped = true;

        return $this;
    }

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }
}
