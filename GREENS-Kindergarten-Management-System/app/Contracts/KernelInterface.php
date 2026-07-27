<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Http\Request;
use App\Http\Response;

interface KernelInterface
{
    public function handle(Request $request): Response;

    public function terminate(Request $request, Response $response): void;
}
