<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

class BootstrapConfigLoader
{
    private string $basePath;

    private ConfigLoader $configLoader;

    public function __construct(string $basePath, ConfigLoader $configLoader)
    {
        $this->basePath = rtrim($basePath, '/\\');
        $this->configLoader = $configLoader;
    }

    public function load(): ConfigCollection
    {
        $config = $this->configLoader->load();
        $envPath = $this->basePath . DIRECTORY_SEPARATOR . '.env.example';
        $environment = new EnvironmentLoader($envPath);
        $envConfig = $environment->load();

        foreach ($envConfig->all() as $key => $value) {
            $config->set($key, $value);
        }

        return $config;
    }
}
