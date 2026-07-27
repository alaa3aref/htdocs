<?php

declare(strict_types=1);

namespace App\Events;

use App\Container\ServiceProvider;
use App\Contracts\Events\EventDispatcherInterface;
use App\Contracts\Events\ListenerProviderInterface;

class EventServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(EventDispatcherInterface::class, static fn (): EventDispatcherInterface => new EventDispatcher(new ListenerProvider()));
        $this->container->singleton(ListenerProviderInterface::class, static fn (): ListenerProviderInterface => new ListenerProvider());
    }

    public function boot(): void
    {
    }
}
