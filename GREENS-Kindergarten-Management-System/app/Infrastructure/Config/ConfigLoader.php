<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

class ConfigLoader
{
    private string $configDirectory;

    public function __construct(string $configDirectory)
    {
        $this->configDirectory = rtrim($configDirectory, '/\\');
    }

    public function load(): ConfigCollection
    {
        if (!is_dir($this->configDirectory)) {
            return new ConfigCollection();
        }

        $collection = new ConfigCollection();
        $files = glob($this->configDirectory . DIRECTORY_SEPARATOR . '*.php');
        if ($files === false) {
            return $collection;
        }

        sort($files);
        foreach ($files as $file) {
            $config = require $file;
            if (!is_array($config)) {
                continue;
            }

            $name = pathinfo($file, PATHINFO_FILENAME);
            $collection->set($name, $config);
        }

        return $collection;
    }
}
