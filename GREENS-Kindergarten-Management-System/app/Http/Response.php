<?php

declare(strict_types=1);

namespace App\Http;

class Response
{
    private int $statusCode;

    /** @var array<string, string> */
    private array $headers;

    private string $content;

    private string $protocolVersion;

    public function __construct(int $statusCode = 200, array $headers = [], string $content = '', string $protocolVersion = 'HTTP/1.1')
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->content = $content;
        $this->protocolVersion = $protocolVersion;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function withStatus(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function withHeader(string $name, string $value): self
    {
        return $this->setHeader($name, $value);
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value, true);
        }

        echo $this->content;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function __toString(): string
    {
        return $this->content;
    }
}
