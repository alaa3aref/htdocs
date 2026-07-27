<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

class ConfigurationServiceProvider
{
    public function createManager(string $basePath): ConfigManager
    {
        $configLoader = new ConfigLoader($basePath . DIRECTORY_SEPARATOR . 'config');
        $bootstrapLoader = new BootstrapConfigLoader($basePath, $configLoader);
        $collection = $bootstrapLoader->load();
        $repository = new ConfigRepository($collection);

        return new ConfigManager($repository);
    }
}
