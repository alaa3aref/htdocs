<?php

declare(strict_types=1);

namespace Database;

use PDO;
use RuntimeException;

final class MigrationRunner
{
    private PDO $pdo;
    private string $migrationPath;

    public function __construct(PDO $pdo, string $migrationPath)
    {
        $this->pdo = $pdo;
        $this->migrationPath = $migrationPath;
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->pdo->prepare('SET NAMES utf8mb4')->execute();
        $this->pdo->prepare('SET FOREIGN_KEY_CHECKS = 1')->execute();
    }

    public function migrate(): void
    {
        $this->ensureHistoryTable();
        $applied = $this->applied();
        $batch = $this->nextBatch();

        foreach ($this->discover() as $migration) {
            if (isset($applied[$migration->version()])) {
                continue;
            }

            foreach ($migration->dependencies() as $dependency) {
                if (!isset($applied[$dependency])) {
                    throw new RuntimeException(sprintf('Migration %s requires %s.', $migration->version(), $dependency));
                }
            }

            $this->pdo->beginTransaction();
            try {
                $migration->up($this->pdo);
                $statement = $this->pdo->prepare(
                    'INSERT INTO migration_history (migration_name, batch_number, executed_at) VALUES (:name, :batch, UTC_TIMESTAMP(6))'
                );
                $statement->execute(['name' => $migration->version(), 'batch' => $batch]);
                $this->pdo->commit();
                $applied[$migration->version()] = true;
            } catch (\Throwable $exception) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                throw $exception;
            }
        }
    }

    public function rollback(): void
    {
        $this->ensureHistoryTable();
        $batch = (int) $this->pdo->query('SELECT COALESCE(MAX(batch_number), 0) FROM migration_history')->fetchColumn();
        if ($batch === 0) {
            return;
        }

        $history = $this->pdo->prepare('SELECT migration_name FROM migration_history WHERE batch_number = :batch ORDER BY id DESC');
        $history->execute(['batch' => $batch]);
        $byVersion = [];
        foreach ($this->discover() as $migration) {
            $byVersion[$migration->version()] = $migration;
        }

        foreach ($history->fetchAll(PDO::FETCH_COLUMN) as $version) {
            if (!isset($byVersion[$version])) {
                throw new RuntimeException(sprintf('Applied migration file not found: %s.', $version));
            }
            $this->pdo->beginTransaction();
            try {
                $byVersion[$version]->down($this->pdo);
                $delete = $this->pdo->prepare('DELETE FROM migration_history WHERE migration_name = :name');
                $delete->execute(['name' => $version]);
                $this->pdo->commit();
            } catch (\Throwable $exception) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                throw $exception;
            }
        }
    }

    /** @return list<MigrationInterface> */
    private function discover(): array
    {
        $files = glob(rtrim($this->migrationPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*.php') ?: [];
        sort($files, SORT_NATURAL);
        $migrations = [];
        foreach ($files as $file) {
            $migration = require $file;
            if (!$migration instanceof MigrationInterface) {
                throw new RuntimeException(sprintf('Migration %s must return MigrationInterface.', basename($file)));
            }
            $migrations[] = $migration;
        }
        usort($migrations, static fn (MigrationInterface $a, MigrationInterface $b): int => strcmp($a->version(), $b->version()));
        return $migrations;
    }

    /** @return array<string, true> */
    private function applied(): array
    {
        $rows = $this->pdo->query('SELECT migration_name FROM migration_history')->fetchAll(PDO::FETCH_COLUMN);
        return array_fill_keys($rows, true);
    }

    private function nextBatch(): int
    {
        return (int) $this->pdo->query('SELECT COALESCE(MAX(batch_number), 0) + 1 FROM migration_history')->fetchColumn();
    }

    private function ensureHistoryTable(): void
    {
        $this->pdo->prepare('CREATE TABLE IF NOT EXISTS migration_history (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, migration_name VARCHAR(191) NOT NULL, batch_number INT UNSIGNED NOT NULL, executed_at DATETIME(6) NOT NULL, UNIQUE KEY uq_migration_history_name (migration_name), KEY idx_migration_history_batch (batch_number)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci')->execute();
    }
}
