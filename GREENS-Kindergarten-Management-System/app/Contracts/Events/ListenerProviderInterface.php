<?php

declare(strict_types=1);

namespace App\Contracts\Events;

interface ListenerProviderInterface
{
    public function addListener(string $eventName, callable|EventListenerInterface $listener, int $priority = 0): self;

    public function getListenersForEvent(EventInterface $event): iterable;

    public function discover(?string $eventName = null): array;
}
