<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Infrastructure\Config\Configuration;

class ConnectionManager
{
    private Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function connection(string $name = 'default'): array
    {
        $config = $this->configuration->get('database.' . $name, []);
        return is_array($config) ? $config : [];
    }

    public function isConfigured(string $name = 'default'): bool
    {
        return $this->connection($name) !== [];
    }
}
