<?php

declare(strict_types=1);

$rootPath = dirname(__DIR__);

spl_autoload_register(static function (string $class) use ($rootPath): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $path = $rootPath . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';
    if (is_file($path)) {
        require_once $path;
    }
});

use App\Contracts\Events\EventDispatcherInterface;
use App\Contracts\Events\EventInterface;
use App\Contracts\Events\EventListenerInterface;
use App\Contracts\Events\ListenerProviderInterface;
use App\Events\Event;
use App\Events\EventDispatcher;
use App\Events\ListenerProvider;

$dispatcher = new EventDispatcher(new ListenerProvider());
if (!($dispatcher instanceof EventDispatcherInterface)) {
    fwrite(STDERR, "Event dispatcher creation failed\n");
    exit(1);
}

$events = [];
$firstListener = new class($events) implements EventListenerInterface {
    public function __construct(private array &$events)
    {
    }

    public function handle(EventInterface $event): void
    {
        $this->events[] = 'first:' . $event->getName();
    }

    public function getPriority(): int
    {
        return 10;
    }
};

$secondListener = new class($events) implements EventListenerInterface {
    public function __construct(private array &$events)
    {
    }

    public function handle(EventInterface $event): void
    {
        $this->events[] = 'second:' . $event->getName();
    }

    public function getPriority(): int
    {
        return 20;
    }
};

$stopListener = new class($events) implements EventListenerInterface {
    public function __construct(private array &$events)
    {
    }

    public function handle(EventInterface $event): void
    {
        $this->events[] = 'stop:' . $event->getName();
        $event->stopPropagation();
    }

    public function getPriority(): int
    {
        return 30;
    }
};

$dispatcher->listen('app.boot', $firstListener, 10);
$dispatcher->subscribe('app.boot', $secondListener, 20);
$dispatcher->register('app.boot', $stopListener, 30);
$dispatcher->boot();

$event = new Event('app.boot', ['phase' => 'infra'], ['source' => 'test']);
$dispatched = $dispatcher->dispatch($event);

if ($dispatched->getName() !== 'app.boot') {
    fwrite(STDERR, "Event dispatch name mismatch\n");
    exit(1);
}

if ($events[0] !== 'first:app.boot' || $events[1] !== 'second:app.boot' || $events[2] !== 'stop:app.boot') {
    fwrite(STDERR, "Listener ordering failed\n");
    exit(1);
}

$metadataEvent = $event->withMetadata(['source' => 'infra']);
if ($metadataEvent === $event) {
    fwrite(STDERR, "Immutable event object failed\n");
    exit(1);
}

if (!($dispatcher->getListenerProvider() instanceof ListenerProviderInterface)) {
    fwrite(STDERR, "Listener provider access failed\n");
    exit(1);
}

echo "Event architecture test passed\n";
