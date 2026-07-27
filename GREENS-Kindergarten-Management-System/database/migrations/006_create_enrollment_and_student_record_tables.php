<?php

declare(strict_types=1);

use Database\MigrationInterface;
use PDO;

return new class implements MigrationInterface {
    public function version(): string { return '006'; }
    public function timestamp(): string { return '2026-07-25T00:05:00Z'; }
    public function description(): string { return 'Create enrollment and restricted student record tables.'; }
    public function dependencies(): array { return ['005']; }
    public function up(PDO $pdo): void {
        $sql = [
            'CREATE TABLE student_enrollments (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, student_id BIGINT UNSIGNED NOT NULL, academic_year_id BIGINT UNSIGNED NOT NULL, class_id BIGINT UNSIGNED NOT NULL, public_id CHAR(26) NOT NULL, starts_on DATE NOT NULL, ends_on DATE NULL, status VARCHAR(32) NOT NULL, active_year_key BIGINT UNSIGNED GENERATED ALWAYS AS (CASE WHEN ends_on IS NULL THEN academic_year_id ELSE NULL END) STORED, created_at DATETIME(6) NOT NULL, updated_at DATETIME(6) NOT NULL, UNIQUE KEY uq_student_enrollments_public_id (public_id), UNIQUE KEY uq_student_enrollments_active_year (student_id, active_year_key), KEY idx_student_enrollments_class (class_id, starts_on, ends_on, status), CONSTRAINT fk_student_enrollments_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE RESTRICT, CONSTRAINT fk_student_enrollments_year FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE RESTRICT, CONSTRAINT fk_student_enrollments_class FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE RESTRICT, CONSTRAINT chk_student_enrollments_dates CHECK (ends_on IS NULL OR ends_on >= starts_on)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
            'CREATE TABLE student_status_history (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, student_id BIGINT UNSIGNED NOT NULL, from_status VARCHAR(32) NULL, to_status VARCHAR(32) NOT NULL, effective_at DATETIME(6) NOT NULL, reason VARCHAR(500) NULL, actor_user_id BIGINT UNSIGNED NULL, created_at DATETIME(6) NOT NULL, KEY idx_student_status_history_student_time (student_id, effective_at), CONSTRAINT fk_student_status_history_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE RESTRICT, CONSTRAINT fk_student_status_history_actor FOREIGN KEY (actor_user_id) REFERENCES users(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
            'CREATE TABLE medical_profiles (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, student_id BIGINT UNSIGNED NOT NULL, allergies TEXT NULL, conditions_text TEXT NULL, medications TEXT NULL, emergency_instructions TEXT NULL, reviewed_at DATETIME(6) NULL, created_at DATETIME(6) NOT NULL, updated_at DATETIME(6) NOT NULL, deleted_at DATETIME(6) NULL, UNIQUE KEY uq_medical_profiles_student (student_id), CONSTRAINT fk_medical_profiles_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE RESTRICT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
            'CREATE TABLE student_consents (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, student_id BIGINT UNSIGNED NOT NULL, guardian_id BIGINT UNSIGNED NOT NULL, consent_type VARCHAR(64) NOT NULL, consent_version VARCHAR(32) NOT NULL, decision BOOLEAN NOT NULL, decided_at DATETIME(6) NOT NULL, withdrawn_at DATETIME(6) NULL, created_at DATETIME(6) NOT NULL, KEY idx_student_consents_student_type (student_id, consent_type, decided_at), CONSTRAINT fk_student_consents_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE RESTRICT, CONSTRAINT fk_student_consents_guardian FOREIGN KEY (guardian_id) REFERENCES guardians(id) ON DELETE RESTRICT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        ]; foreach ($sql as $statement) { $pdo->prepare($statement)->execute(); }
    }
    public function down(PDO $pdo): void { foreach (['student_consents','medical_profiles','student_status_history','student_enrollments'] as $table) { $pdo->prepare("DROP TABLE IF EXISTS {$table}")->execute(); } }
};
