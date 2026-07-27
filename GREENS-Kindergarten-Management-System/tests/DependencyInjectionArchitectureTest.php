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

use App\Container\Binding;
use App\Container\Container;
use App\Container\ProviderRepository;
use App\Container\Resolver;
use App\Container\ServiceProvider;
use App\Contracts\Container\BindingInterface;
use App\Contracts\Container\ContainerInterface;
use App\Contracts\Container\ServiceProviderInterface;
use App\Exceptions\ContainerException;

$container = new Container();
if (!($container instanceof ContainerInterface)) {
    fwrite(STDERR, "Container creation failed\n");
    exit(1);
}

$binding = new Binding('stdClass', static fn (): object => new stdClass());
if (!($binding instanceof BindingInterface)) {
    fwrite(STDERR, "Binding creation failed\n");
    exit(1);
}

$container->bind('stdClass', static fn (): stdClass => new stdClass());
$container->singleton('ArrayObject', static fn (): ArrayObject => new ArrayObject());
$container->instance('string', 'infra');

if (!$container->has('stdClass')) {
    fwrite(STDERR, "Container bind registration failed\n");
    exit(1);
}

$resolved = $container->make('stdClass');
if (!($resolved instanceof stdClass)) {
    fwrite(STDERR, "Container make resolution failed\n");
    exit(1);
}

$singletonA = $container->make('ArrayObject');
$singletonB = $container->make('ArrayObject');
if ($singletonA !== $singletonB) {
    fwrite(STDERR, "Singleton lifecycle failed\n");
    exit(1);
}

if ($container->make('string') !== 'infra') {
    fwrite(STDERR, "Instance binding failed\n");
    exit(1);
}

$provider = new class extends ServiceProvider {
    public function register(): void
    {
        $this->container->bind('App\\Contracts\\Container\\ContainerInterface', static fn (ContainerInterface $container): ContainerInterface => $container);
    }
};

$providerRepository = new ProviderRepository();
$providerRepository->setContainer($container);
$providerRepository->register($provider);
$providerRepository->boot();

if (!($providerRepository instanceof ProviderRepository)) {
    fwrite(STDERR, "Provider repository creation failed\n");
    exit(1);
}

$resolver = new Resolver($container);
if (!($resolver instanceof Resolver)) {
    fwrite(STDERR, "Resolver creation failed\n");
    exit(1);
}

try {
    $container->make('App\\Contracts\\Container\\ContainerInterface');
} catch (ContainerException $exception) {
    fwrite(STDERR, $exception->getMessage() . "\n");
    exit(1);
}

echo "Dependency injection architecture test passed\n";
