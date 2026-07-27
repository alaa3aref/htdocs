<?php

declare(strict_types=1);

namespace App\Core;

use App\Contracts\Container\ContainerInterface;
use App\Contracts\KernelInterface;
use App\Container\Container;
use App\Http\Request;
use App\Http\Response;
use App\Infrastructure\Config\Configuration;
use App\Infrastructure\Database\ConnectionManager;
use App\Infrastructure\Logging\Logger;
use App\Infrastructure\Storage\StorageManager;
use RuntimeException;

class Application implements KernelInterface
{
    private string $basePath;

    private ContainerInterface $container;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/\\');
        $this->container = new Container();
        $this->registerCoreBindings();
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function handle(Request $request): Response
    {
        $response = new Response(200, ['Content-Type' => 'text/plain; charset=UTF-8']);
        $response->setContent(
            sprintf(
                "GREENS backend skeleton ready for %s %s\n",
                $request->getMethod(),
                $request->getPath()
            )
        );

        return $response;
    }

    public function terminate(Request $request, Response $response): void
    {
        unset($request, $response);
    }

    private function registerCoreBindings(): void
    {
        $this->container->singleton(self::class, $this);
        $this->container->singleton(ContainerInterface::class, $this->container);
        $this->container->singleton(Configuration::class, new Configuration([
            'app' => ['name' => 'GREENS Kindergarten Management System'],
            'path' => ['base' => $this->basePath],
        ]));
        $this->container->singleton(StorageManager::class, new StorageManager($this->basePath . DIRECTORY_SEPARATOR . 'storage'));
        $this->container->singleton(ConnectionManager::class, function (ContainerInterface $container): ConnectionManager {
            $configuration = $container->get(Configuration::class);
            return new ConnectionManager($configuration);
        });
        $this->container->singleton(Logger::class, function (ContainerInterface $container): Logger {
            $storage = $container->get(StorageManager::class);
            return new Logger($storage);
        });
    }
}
