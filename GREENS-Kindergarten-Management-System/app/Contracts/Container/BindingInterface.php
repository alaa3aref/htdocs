<?php

declare(strict_types=1);

namespace App\Contracts\Container;

interface BindingInterface
{
    public function getAbstract(): string;

    public function getConcrete(): mixed;

    public function isShared(): bool;

    public function isSingleton(): bool;
}
