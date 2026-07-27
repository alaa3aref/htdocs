<?php

declare(strict_types=1);

namespace App\Events;

use App\Contracts\Events\EventInterface;

class EventCollection
{
    /** @var array<int, EventInterface> */
    private array $events = [];

    public function add(EventInterface $event): self
    {
        $this->events[] = $event;

        return $this;
    }

    /** @return array<int, EventInterface> */
    public function all(): array
    {
        return $this->events;
    }

    public function count(): int
    {
        return count($this->events);
    }
}
