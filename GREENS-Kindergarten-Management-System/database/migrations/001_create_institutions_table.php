<?php

declare(strict_types=1);

use Database\MigrationInterface;
use PDO;

return new class implements MigrationInterface {
    public function version(): string { return '001'; }
    public function timestamp(): string { return '2026-07-25T00:00:00Z'; }
    public function description(): string { return 'Create institution foundation.'; }
    public function dependencies(): array { return []; }
    public function up(PDO $pdo): void {
        $pdo->prepare('CREATE TABLE institutions (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, public_id CHAR(26) NOT NULL, legal_name VARCHAR(255) NOT NULL, display_name VARCHAR(255) NOT NULL, phone VARCHAR(32) NULL, email VARCHAR(254) NULL, address TEXT NULL, timezone VARCHAR(64) NOT NULL DEFAULT \'Africa/Cairo\', created_at DATETIME(6) NOT NULL, updated_at DATETIME(6) NOT NULL, UNIQUE KEY uq_institutions_public_id (public_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci')->execute();
    }
    public function down(PDO $pdo): void { $pdo->prepare('DROP TABLE IF EXISTS institutions')->execute(); }
};
