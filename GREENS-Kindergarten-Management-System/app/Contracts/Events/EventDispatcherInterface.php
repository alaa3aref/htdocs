<?php

declare(strict_types=1);

namespace App\Contracts\Events;

interface EventDispatcherInterface
{
    public function dispatch(EventInterface $event): EventInterface;

    public function listen(string $eventName, callable|EventListenerInterface $listener, int $priority = 0): self;

    public function subscribe(string $eventName, callable|EventListenerInterface $listener, int $priority = 0): self;

    public function register(string $eventName, callable|EventListenerInterface $listener, int $priority = 0): self;

    public function boot(): void;

    public function getListenerProvider(): ListenerProviderInterface;
}
