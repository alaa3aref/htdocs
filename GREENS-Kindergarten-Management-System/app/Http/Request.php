<?php

declare(strict_types=1);

namespace App\Http;

class Request
{
    private array $server;

    private array $query;

    private array $post;

    private array $files;

    private array $cookies;

    private array $headers;

    private string $method;

    private string $uri;

    private string $protocol;

    public function __construct(
        array $server = [],
        array $query = [],
        array $post = [],
        array $files = [],
        array $cookies = [],
        array $headers = [],
        string $method = 'GET',
        string $uri = '/',
        string $protocol = 'HTTP/1.1'
    ) {
        $this->server = $server;
        $this->query = $query;
        $this->post = $post;
        $this->files = $files;
        $this->cookies = $cookies;
        $this->headers = $headers;
        $this->method = strtoupper($method);
        $this->uri = $uri;
        $this->protocol = $protocol;
    }

    public static function fromGlobals(): self
    {
        $server = $_SERVER ?? [];
        $headers = self::normalizeHeaders($server);

        return new self(
            server: $server,
            query: $_GET ?? [],
            post: $_POST ?? [],
            files: $_FILES ?? [],
            cookies: $_COOKIE ?? [],
            headers: $headers,
            method: (string) ($server['REQUEST_METHOD'] ?? 'GET'),
            uri: (string) ($server['REQUEST_URI'] ?? '/'),
            protocol: (string) ($server['SERVER_PROTOCOL'] ?? 'HTTP/1.1')
        );
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getPath(): string
    {
        $path = parse_url($this->uri, PHP_URL_PATH);
        return is_string($path) && $path !== '' ? $path : '/';
    }

    public function getHeader(string $name, mixed $default = null): mixed
    {
        $normalized = strtolower($name);
        return $this->headers[$normalized] ?? $default;
    }

    public function getServer(string $name, mixed $default = null): mixed
    {
        return $this->server[$name] ?? $default;
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    public function getPost(): array
    {
        return $this->post;
    }

    public function getCookies(): array
    {
        return $this->cookies;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function all(): array
    {
        return [
            'server' => $this->server,
            'query' => $this->query,
            'post' => $this->post,
            'files' => $this->files,
            'cookies' => $this->cookies,
            'headers' => $this->headers,
        ];
    }

    private static function normalizeHeaders(array $server): array
    {
        $headers = [];
        foreach ($server as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace('HTTP_', '', $key);
                $headers[strtolower($name)] = $value;
            }
        }

        return $headers;
    }
}
