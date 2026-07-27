<?php

declare(strict_types=1);

namespace Database;

use PDO;

interface MigrationInterface
{
    public function version(): string;

    public function timestamp(): string;

    public function description(): string;

    /** @return list<string> */
    public function dependencies(): array;

    public function up(PDO $pdo): void;

    public function down(PDO $pdo): void;
}
