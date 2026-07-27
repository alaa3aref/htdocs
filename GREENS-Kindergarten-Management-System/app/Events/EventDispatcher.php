<?php

declare(strict_types=1);

namespace App\Events;

use App\Contracts\Events\EventDispatcherInterface;
use App\Contracts\Events\EventInterface;
use App\Contracts\Events\EventListenerInterface;
use App\Contracts\Events\ListenerProviderInterface;

class EventDispatcher implements EventDispatcherInterface
{
    public function __construct(private ?ListenerProviderInterface $listenerProvider = null)
    {
        $this->listenerProvider ??= new ListenerProvider();
    }

    public function dispatch(EventInterface $event): EventInterface
    {
        foreach ($this->listenerProvider->getListenersForEvent($event) as $listenerDefinition) {
            $listener = $listenerDefinition['listener'];
            $result = $this->invokeListener($listener, $event);

            if ($result instanceof EventInterface) {
                $event = $result;
            }

            if ($event->isPropagationStopped()) {
                break;
            }
        }

        return $event;
    }

    public function listen(string $eventName, callable|EventListenerInterface $listener, int $priority = 0): self
    {
        $this->listenerProvider->addListener($eventName, $listener, $priority);

        return $this;
    }

    public function subscribe(string $eventName, callable|EventListenerInterface $listener, int $priority = 0): self
    {
        return $this->listen($eventName, $listener, $priority);
    }

    public function register(string $eventName, callable|EventListenerInterface $listener, int $priority = 0): self
    {
        return $this->listen($eventName, $listener, $priority);
    }

    public function boot(): void
    {
    }

    public function getListenerProvider(): ListenerProviderInterface
    {
        return $this->listenerProvider;
    }

    private function invokeListener(callable|EventListenerInterface $listener, EventInterface $event): EventInterface
    {
        if ($listener instanceof EventListenerInterface) {
            $listener->handle($event);

            return $event;
        }

        $result = $listener($event);

        return $result instanceof EventInterface ? $result : $event;
    }
}
