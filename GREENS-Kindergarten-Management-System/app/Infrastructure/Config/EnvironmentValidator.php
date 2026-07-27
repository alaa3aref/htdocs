<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

class EnvironmentValidator
{
    public function validate(ConfigCollection $environment): bool
    {
        return $environment instanceof ConfigCollection;
    }
}
