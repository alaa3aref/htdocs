<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use App\Infrastructure\Storage\StorageManager;

class Logger
{
    private StorageManager $storage;

    public function __construct(StorageManager $storage)
    {
        $this->storage = $storage;
    }

    public function debug(string $message, array $context = []): void
    {
        $this->write('debug', $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->write('info', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->write('warning', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->write('error', $message, $context);
    }

    private function write(string $level, string $message, array $context): void
    {
        $entry = [
            'timestamp' => gmdate('c'),
            'level' => $level,
            'message' => $message,
            'context' => $context,
        ];

        $this->storage->ensureDirectory('logs');
        $this->storage->append('logs/app.log', json_encode($entry, JSON_UNESCAPED_SLASHES) . PHP_EOL);
    }
}
