<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

class EnvironmentLoader
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function load(): ConfigCollection
    {
        if (!is_file($this->path)) {
            return new ConfigCollection();
        }

        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return new ConfigCollection();
        }

        $collection = new ConfigCollection();
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            $parts = explode('=', $line, 2);
            if (count($parts) !== 2) {
                continue;
            }

            $key = trim($parts[0]);
            $value = trim($parts[1]);
            $collection->set($key, $value);
        }

        return $collection;
    }
}
