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

use App\Container\Container;
use App\Core\Application;
use App\Http\Request;
use App\Http\Response;

$app = new Application($rootPath);
$container = $app->getContainer();
$request = Request::fromGlobals();
$response = new Response();

if (!($app instanceof Application)) {
    fwrite(STDERR, "Application instantiation failed\n");
    exit(1);
}

if (!($container instanceof Container)) {
    fwrite(STDERR, "Container binding failed\n");
    exit(1);
}

if (!($request instanceof Request)) {
    fwrite(STDERR, "Request creation failed\n");
    exit(1);
}

if (!($response instanceof Response)) {
    fwrite(STDERR, "Response creation failed\n");
    exit(1);
}

echo "Architecture skeleton test passed\n";
