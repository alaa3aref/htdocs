<?php

declare(strict_types=1);

namespace App\Contracts\Http\Routing;

interface RouterInterface
{
    public function register(): void;
}
