<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

class DotEnvParser
{
    public function parse(string $content): ConfigCollection
    {
        $collection = new ConfigCollection();
        $lines = preg_split('/\r\n|\r|\n/', $content) ?: [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
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
