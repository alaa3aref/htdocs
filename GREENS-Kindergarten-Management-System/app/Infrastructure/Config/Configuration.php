<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

class Configuration
{
    /** @var array<string, mixed> */
    private array $items;

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public static function fromFile(string $path): self
    {
        if (!is_file($path)) {
            return new self();
        }

        $config = require $path;
        return new self(is_array($config) ? $config : []);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $value = $this->items;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    public function all(): array
    {
        return $this->items;
    }
}
