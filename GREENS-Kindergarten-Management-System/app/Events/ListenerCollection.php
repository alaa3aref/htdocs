<?php

declare(strict_types=1);

namespace App\Events;

use App\Contracts\Events\EventListenerInterface;

class ListenerCollection
{
    /** @var array<int, array{eventName: string, listener: callable|EventListenerInterface, priority: int}> */
    private array $listeners = [];

    public function add(string $eventName, callable|EventListenerInterface $listener, int $priority = 0): self
    {
        $this->listeners[] = [
            'eventName' => $eventName,
            'listener' => $listener,
            'priority' => $priority,
        ];

        return $this;
    }

    /** @return array<int, array{eventName: string, listener: callable|EventListenerInterface, priority: int}> */
    public function all(): array
    {
        return $this->listeners;
    }

    /** @return array<int, array{eventName: string, listener: callable|EventListenerInterface, priority: int}> */
    public function forEvent(string $eventName): array
    {
        return array_values(array_filter(
            $this->listeners,
            static fn (array $listener): bool => $listener['eventName'] === $eventName
        ));
    }
}
