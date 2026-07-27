<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

class StorageManager
{
    private string $rootPath;

    public function __construct(string $rootPath)
    {
        $this->rootPath = rtrim($rootPath, '/\\');
    }

    public function path(string $path = ''): string
    {
        return $this->rootPath . DIRECTORY_SEPARATOR . ltrim($path, '/\\');
    }

    public function ensureDirectory(string $path): self
    {
        $directory = $this->path($path);
        if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new \RuntimeException(sprintf('Unable to create storage directory %s.', $directory));
        }

        return $this;
    }

    public function append(string $path, string $contents): void
    {
        $this->ensureDirectory(dirname($path));
        file_put_contents($this->path($path), $contents, FILE_APPEND);
    }

    public function exists(string $path): bool
    {
        return file_exists($this->path($path));
    }

    public function read(string $path): string
    {
        return file_get_contents($this->path($path)) ?: '';
    }
}
