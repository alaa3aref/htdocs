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

use App\Contracts\Exceptions\ErrorRendererInterface;
use App\Contracts\Exceptions\ErrorResponseFactoryInterface;
use App\Contracts\Exceptions\ExceptionHandlerInterface;
use App\Exceptions\BadRequestException;
use App\Exceptions\ConfigurationException;
use App\Exceptions\ErrorRenderer;
use App\Exceptions\ErrorResponseFactory;
use App\Exceptions\ExceptionHandler;
use App\Exceptions\ForbiddenException;
use App\Exceptions\HttpException;
use App\Exceptions\InternalServerException;
use App\Exceptions\NotFoundException;
use App\Exceptions\RoutingException;
use App\Exceptions\ServiceUnavailableException;
use App\Exceptions\ValidationException;

$handler = new ExceptionHandler();
if (!($handler instanceof ExceptionHandlerInterface)) {
    fwrite(STDERR, "Exception handler creation failed\n");
    exit(1);
}

$handler->register(BadRequestException::class, 400);
$handler->register(NotFoundException::class, 404);
$handler->register(ForbiddenException::class, 403);
$handler->register(InternalServerException::class, 500);
$handler->register(ServiceUnavailableException::class, 503);

$renderer = new ErrorRenderer();
if (!($renderer instanceof ErrorRendererInterface)) {
    fwrite(STDERR, "Error renderer creation failed\n");
    exit(1);
}

$responseFactory = new ErrorResponseFactory();
if (!($responseFactory instanceof ErrorResponseFactoryInterface)) {
    fwrite(STDERR, "Error response factory creation failed\n");
    exit(1);
}

$exception = new BadRequestException('bad input', ['field' => 'name']);
$normalized = $handler->normalize($exception);
if ($normalized['statusCode'] !== 400) {
    fwrite(STDERR, "Exception mapping failed\n");
    exit(1);
}

if ($normalized['message'] !== 'bad input') {
    fwrite(STDERR, "Exception normalization failed\n");
    exit(1);
}

$rendered = $renderer->render($exception, true, ['trace' => []]);
if (!is_array($rendered) || !isset($rendered['message'])) {
    fwrite(STDERR, "Error rendering failed\n");
    exit(1);
}

$response = $responseFactory->create($exception, true, ['trace' => []]);
if (!is_array($response) || !isset($response['statusCode'])) {
    fwrite(STDERR, "Error response creation failed\n");
    exit(1);
}

$contextException = new ValidationException('invalid data', ['field' => 'email']);
$context = $handler->getContext($contextException);
if (!isset($context['exception']) || $context['exception'] !== ValidationException::class) {
    fwrite(STDERR, "Exception context failed\n");
    exit(1);
}

$propagated = $handler->propagate($exception);
if (!($propagated instanceof 
    Throwable)) {
    fwrite(STDERR, "Exception propagation failed\n");
    exit(1);
}

$handler->setDebugMode(true);
$handler->setProductionMode(false);

$handler->registerRenderer(ErrorRenderer::class, $renderer);
$handler->registerResponseFactory(ErrorResponseFactory::class, $responseFactory);

echo "Exception architecture test passed\n";
