<?php

declare(strict_types=1);

namespace App\Contracts\Container;

interface ContainerInterface
{
    public function bind(string $abstract, mixed $concrete = null): self;

    public function singleton(string $abstract, mixed $concrete = null): self;

    public function instance(string $abstract, mixed $instance): self;

    public function make(string $abstract): mixed;

    public function get(string $abstract): mixed;

    public function has(string $abstract): bool;

    public function register(ServiceProviderInterface $provider): self;

    public function boot(): void;
}
