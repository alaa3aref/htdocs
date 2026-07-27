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

use App\Contracts\Http\HttpKernelInterface;
use App\Contracts\Http\MiddlewareDispatcherInterface;
use App\Contracts\Http\MiddlewareRegistryInterface;
use App\Contracts\Http\MiddlewareResolverInterface;
use App\Contracts\Http\PipelineInterface;
use App\Contracts\Http\ResponseEmitterInterface;
use App\Http\HttpKernel;
use App\Http\MiddlewareDispatcher;
use App\Http\MiddlewareRegistry;
use App\Http\MiddlewareResolver;
use App\Http\Pipeline;
use App\Http\RequestContext;
use App\Http\ResponseEmitter;
use App\Http\Response;
use App\Http\Request;

$kernel = new HttpKernel();
if (!($kernel instanceof HttpKernelInterface)) {
    fwrite(STDERR, "HttpKernel creation failed\n");
    exit(1);
}

$pipeline = new Pipeline();
if (!($pipeline instanceof PipelineInterface)) {
    fwrite(STDERR, "Pipeline creation failed\n");
    exit(1);
}

$dispatcher = new MiddlewareDispatcher();
if (!($dispatcher instanceof MiddlewareDispatcherInterface)) {
    fwrite(STDERR, "MiddlewareDispatcher creation failed\n");
    exit(1);
}

$resolver = new MiddlewareResolver();
if (!($resolver instanceof MiddlewareResolverInterface)) {
    fwrite(STDERR, "MiddlewareResolver creation failed\n");
    exit(1);
}

$registry = new MiddlewareRegistry();
if (!($registry instanceof MiddlewareRegistryInterface)) {
    fwrite(STDERR, "MiddlewareRegistry creation failed\n");
    exit(1);
}

$emitter = new ResponseEmitter();
if (!($emitter instanceof ResponseEmitterInterface)) {
    fwrite(STDERR, "ResponseEmitter creation failed\n");
    exit(1);
}

$context = new RequestContext(new Request(), new Response());
if (!($context instanceof RequestContext)) {
    fwrite(STDERR, "RequestContext creation failed\n");
    exit(1);
}

$kernel->setPipeline($pipeline);
$kernel->setDispatcher($dispatcher);
$kernel->setResolver($resolver);
$kernel->setRegistry($registry);
$kernel->setEmitter($emitter);

if ($kernel->getPipeline() !== $pipeline) {
    fwrite(STDERR, "HttpKernel pipeline binding failed\n");
    exit(1);
}

if ($kernel->getResolver() !== $resolver) {
    fwrite(STDERR, "HttpKernel resolver binding failed\n");
    exit(1);
}

if ($kernel->getDispatcher() !== $dispatcher) {
    fwrite(STDERR, "HttpKernel dispatcher binding failed\n");
    exit(1);
}

if ($kernel->getRegistry() !== $registry) {
    fwrite(STDERR, "HttpKernel registry binding failed\n");
    exit(1);
}

if ($kernel->getEmitter() !== $emitter) {
    fwrite(STDERR, "HttpKernel emitter binding failed\n");
    exit(1);
}

$kernel->handle($context);
$kernel->terminate($context);
$emitter->emit($context->getResponse());

echo "Http pipeline architecture test passed\n";
