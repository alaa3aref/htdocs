<?php

declare(strict_types=1);

namespace App\Contracts\Events;

interface EventListenerInterface
{
    public function handle(EventInterface $event): void;

    public function getPriority(): int;
}
