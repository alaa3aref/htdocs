<?php

declare(strict_types=1);

namespace App\Container;

use App\Contracts\Container\BindingInterface;

class Binding implements BindingInterface
{
    private string $abstract;

    private mixed $concrete;

    private bool $shared;

    public function __construct(string $abstract, mixed $concrete, bool $shared = false)
    {
        $this->abstract = $abstract;
        $this->concrete = $concrete;
        $this->shared = $shared;
    }

    public function getAbstract(): string
    {
        return $this->abstract;
    }

    public function getConcrete(): mixed
    {
        return $this->concrete;
    }

    public function isShared(): bool
    {
        return $this->shared;
    }

    public function isSingleton(): bool
    {
        return $this->shared;
    }
}
