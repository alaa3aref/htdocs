<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use App\Contracts\Cache\CacheInterface;
use RuntimeException;

class FileCache implements CacheInterface
{
    private string $directory;

    public function __construct(string $directory)
    {
        $this->directory = rtrim($directory, '/\\');

        if (!is_dir($this->directory) && !mkdir($this->directory, 0777, true) && !is_dir($this->directory)) {
            throw new RuntimeException(sprintf('Unable to create cache directory "%s".', $this->directory));
        }
    }

    public function has(string $key): bool
    {
        $path = $this->path($key);

        if (!is_file($path)) {
            return false;
        }

        $data = $this->read($path);

        return $data !== null;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $path = $this->path($key);

        if (!is_file($path)) {
            return $default;
        }

        $data = $this->read($path);

        if ($data === null) {
            return $default;
        }

        return $data['value'];
    }

    public function put(string $key, mixed $value, ?int $ttlSeconds = null): void
    {
        $path = $this->path($key);

        $data = [
            'value' => $value,
            'expiresAt' => $ttlSeconds !== null ? time() + $ttlSeconds : null,
        ];

        $directory = dirname($path);
        if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new RuntimeException(sprintf('Unable to create cache directory "%s".', $directory));
        }

        file_put_contents($path, serialize($data), LOCK_EX);
    }

    public function forget(string $key): void
    {
        $path = $this->path($key);

        if (is_file($path)) {
            unlink($path);
        }
    }

    public function clear(): void
    {
        $this->clearDirectory($this->directory);
    }

    private function path(string $key): string
    {
        $safe = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $key);

        return $this->directory . DIRECTORY_SEPARATOR . $safe . '.cache';
    }

    /** @return array{value: mixed, expiresAt: ?int}|null */
    private function read(string $path): ?array
    {
        $contents = file_get_contents($path);

        if ($contents === false || $contents === '') {
            return null;
        }

        /** @var array{value: mixed, expiresAt: ?int}|false $data */
        $data = unserialize($contents);

        if (!is_array($data) || !array_key_exists('value', $data) || !array_key_exists('expiresAt', $data)) {
            return null;
        }

        if ($data['expiresAt'] !== null && $data['expiresAt'] <= time()) {
            unlink($path);
            return null;
        }

        return $data;
    }

    private function clearDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                unlink($file->getRealPath());
            }
        }
    }
}

