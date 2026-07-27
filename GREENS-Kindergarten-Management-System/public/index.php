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

use App\Core\Application;
use App\Exceptions\Handler;
use App\Http\Request;

$request = Request::fromGlobals();
$app = new Application($rootPath);

try {
    $response = $app->handle($request);
    $app->terminate($request, $response);
    $response->send();
} catch (Throwable $exception) {
    $handler = new Handler();
    $response = $handler->render($exception);
    $response->send();
}
