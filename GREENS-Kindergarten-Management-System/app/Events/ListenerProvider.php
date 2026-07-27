<?php

declare(strict_types=1);

namespace App\Events;

use App\Contracts\Events\EventInterface;
use App\Contracts\Events\EventListenerInterface;
use App\Contracts\Events\ListenerProviderInterface;

class ListenerProvider implements ListenerProviderInterface
{
    public function __construct(private ?ListenerCollection $listenerCollection = null)
    {
        $this->listenerCollection ??= new ListenerCollection();
    }

    public function addListener(string $eventName, callable|EventListenerInterface $listener, int $priority = 0): self
    {
        $this->listenerCollection->add($eventName, $listener, $priority);

        return $this;
    }

    public function getListenersForEvent(EventInterface $event): iterable
    {
        $listeners = $this->listenerCollection->forEvent($event->getName());
        usort(
            $listeners,
            static fn (array $left, array $right): int => $left['priority'] <=> $right['priority']
        );

        return $listeners;
    }

    public function discover(?string $eventName = null): array
    {
        if ($eventName === null) {
            return $this->listenerCollection->all();
        }

        return $this->listenerCollection->forEvent($eventName);
    }
}
