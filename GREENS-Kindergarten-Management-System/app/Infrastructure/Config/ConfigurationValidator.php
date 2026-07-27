<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

class ConfigurationValidator
{
    /** @param array<string, mixed> $config */
    public function validate(array $config): bool
    {
        return is_array($config);
    }
}
