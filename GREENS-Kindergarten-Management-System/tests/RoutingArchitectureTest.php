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

use App\Contracts\Http\Routing\RouteCacheInterface;
use App\Contracts\Http\Routing\RouteCollectionInterface;
use App\Contracts\Http\Routing\RouteRegistrarInterface;
use App\Http\Routing\Route;
use App\Http\Routing\RouteCollection;
use App\Http\Routing\RouteDispatcher;
use App\Http\Routing\RouteGroup;
use App\Http\Routing\RouteLoader;
use App\Http\Routing\RouteMatcher;
use App\Http\Routing\RouteParameters;
use App\Http\Routing\RouteRegistrar;
use App\Http\Routing\RouteResolver;
use App\Http\Routing\RouteServiceProvider;

$collection = new RouteCollection();
$registrar = new RouteRegistrar($collection);

$registrar->get('/health', 'health.check', 'health')->name('health');
$registrar->api(static function (RouteRegistrarInterface $router): void {
    $router->get('/users', 'users.index', 'users.index')->name('users.index');
});

$group = new RouteGroup(prefix: '/admin', namePrefix: 'admin.', namespace: 'Admin', middleware: ['audit']);
$group->register($registrar, static function (RouteRegistrarInterface $router): void {
    $router->get('/dashboard', 'dashboard.index', 'dashboard.index')->name('dashboard');
});

$dispatcher = new RouteDispatcher(new RouteMatcher(), new RouteResolver());
$result = $dispatcher->dispatch('GET', '/admin/dashboard', $collection);

if (!($collection instanceof RouteCollectionInterface)) {
    fwrite(STDERR, "Route collection registration failed\n");
    exit(1);
}

if (!($registrar instanceof RouteRegistrarInterface)) {
    fwrite(STDERR, "Route registrar creation failed\n");
    exit(1);
}

if (!($dispatcher instanceof RouteDispatcher)) {
    fwrite(STDERR, "Route dispatcher creation failed\n");
    exit(1);
}

if (!($result['route'] instanceof \App\Http\Routing\Route)) {
    fwrite(STDERR, "Route dispatch failed\n");
    exit(1);
}

if (!($result['parameters'] instanceof \App\Http\Routing\RouteParameters)) {
    fwrite(STDERR, "Route parameters creation failed\n");
    exit(1);
}

if (!($result['parameters']->toArray() === [])) {
    fwrite(STDERR, "Route parameters should be empty for static routes\n");
    exit(1);
}

$loader = new RouteLoader();
$loaded = $loader->load([
    ['GET', '/status', 'status.index', 'status'],
]);

if (!($loaded instanceof RouteCollectionInterface)) {
    fwrite(STDERR, "Route loader failed\n");
    exit(1);
}

$serviceProvider = new RouteServiceProvider();
if (!($serviceProvider instanceof RouteServiceProvider)) {
    fwrite(STDERR, "Route service provider creation failed\n");
    exit(1);
}

$cache = $serviceProvider->createCache();
if (!($cache instanceof RouteCacheInterface)) {
    fwrite(STDERR, "Route cache creation failed\n");
    exit(1);
}

echo "Routing architecture test passed\n";
