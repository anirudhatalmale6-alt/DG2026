<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    $created = [];
    $skipped = [];

    // 1. job_card_types
    if (!Schema::hasTable('job_card_types')) {
        DB::statement("
            CREATE TABLE `job_card_types` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL,
                `description` TEXT NULL,
                `submission_to` VARCHAR(100) NULL,
                `display_order` INT NOT NULL DEFAULT 0,
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `created_by` BIGINT UNSIGNED NULL,
                `updated_by` BIGINT UNSIGNED NULL,
                `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                INDEX `idx_jct_active` (`is_active`),
                INDEX `idx_jct_order` (`display_order`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $created[] = 'job_card_types';
    } else {
        $skipped[] = 'job_card_types';
    }

    // 2. job_card_type_steps
    if (!Schema::hasTable('job_card_type_steps')) {
        DB::statement("
            CREATE TABLE `job_card_type_steps` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `job_type_id` BIGINT UNSIGNED NOT NULL,
                `step_name` VARCHAR(255) NOT NULL,
                `step_description` TEXT NULL,
                `display_order` INT NOT NULL DEFAULT 0,
                `is_required` TINYINT(1) NOT NULL DEFAULT 1,
                `step_type` ENUM('checkbox','document_required','info_review') NOT NULL DEFAULT 'checkbox',
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                INDEX `idx_jcts_type` (`job_type_id`),
                INDEX `idx_jcts_order` (`display_order`),
                CONSTRAINT `fk_jcts_type` FOREIGN KEY (`job_type_id`) REFERENCES `job_card_types`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $created[] = 'job_card_type_steps';
    } else {
        $skipped[] = 'job_card_type_steps';
    }

    // 3. job_card_type_fields
    if (!Schema::hasTable('job_card_type_fields')) {
        DB::statement("
            CREATE TABLE `job_card_type_fields` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `job_type_id` BIGINT UNSIGNED NOT NULL,
                `field_name` VARCHAR(100) NOT NULL,
                `field_label` VARCHAR(150) NOT NULL,
                `display_order` INT NOT NULL DEFAULT 0,
                `is_required` TINYINT(1) NOT NULL DEFAULT 0,
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                INDEX `idx_jctf_type` (`job_type_id`),
                INDEX `idx_jctf_order` (`display_order`),
                CONSTRAINT `fk_jctf_type` FOREIGN KEY (`job_type_id`) REFERENCES `job_card_types`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $created[] = 'job_card_type_fields';
    } else {
        $skipped[] = 'job_card_type_fields';
    }

    // 4. job_card_type_documents
    if (!Schema::hasTable('job_card_type_documents')) {
        DB::statement("
            CREATE TABLE `job_card_type_documents` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `job_type_id` BIGINT UNSIGNED NOT NULL,
                `document_type_id` BIGINT UNSIGNED NULL,
                `document_label` VARCHAR(255) NOT NULL,
                `is_required` TINYINT(1) NOT NULL DEFAULT 1,
                `display_order` INT NOT NULL DEFAULT 0,
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                INDEX `idx_jctd_type` (`job_type_id`),
                INDEX `idx_jctd_order` (`display_order`),
                CONSTRAINT `fk_jctd_type` FOREIGN KEY (`job_type_id`) REFERENCES `job_card_types`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $created[] = 'job_card_type_documents';
    } else {
        $skipped[] = 'job_card_type_documents';
    }

    // 5. job_cards
    if (!Schema::hasTable('job_cards')) {
        DB::statement("
            CREATE TABLE `job_cards` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `job_code` VARCHAR(50) NOT NULL,
                `client_id` BIGINT NOT NULL,
                `job_type_id` BIGINT UNSIGNED NOT NULL,
                `assigned_to` BIGINT UNSIGNED NULL,
                `status` ENUM('draft','in_progress','review','completed','submitted','cancelled') NOT NULL DEFAULT 'draft',
                `priority` ENUM('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
                `due_date` DATE NULL,
                `started_at` TIMESTAMP NULL,
                `completed_at` TIMESTAMP NULL,
                `submitted_at` TIMESTAMP NULL,
                `completion_percentage` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
                `notes` TEXT NULL,
                `external_pack_path` VARCHAR(500) NULL,
                `internal_pack_path` VARCHAR(500) NULL,
                `created_by` BIGINT UNSIGNED NULL,
                `updated_by` BIGINT UNSIGNED NULL,
                `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                `deleted_at` TIMESTAMP NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_jc_code` (`job_code`),
                INDEX `idx_jc_client` (`client_id`),
                INDEX `idx_jc_type` (`job_type_id`),
                INDEX `idx_jc_status` (`status`),
                INDEX `idx_jc_assigned` (`assigned_to`),
                INDEX `idx_jc_due` (`due_date`),
                INDEX `idx_jc_deleted` (`deleted_at`),
                CONSTRAINT `fk_jc_type` FOREIGN KEY (`job_type_id`) REFERENCES `job_card_types`(`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $created[] = 'job_cards';
    } else {
        $skipped[] = 'job_cards';
    }

    // 6. job_card_progress
    if (!Schema::hasTable('job_card_progress')) {
        DB::statement("
            CREATE TABLE `job_card_progress` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `job_card_id` BIGINT UNSIGNED NOT NULL,
                `step_id` BIGINT UNSIGNED NOT NULL,
                `status` ENUM('pending','in_progress','completed','skipped','na') NOT NULL DEFAULT 'pending',
                `completed_by` BIGINT UNSIGNED NULL,
                `completed_at` TIMESTAMP NULL,
                `notes` TEXT NULL,
                `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                INDEX `idx_jcp_card` (`job_card_id`),
                INDEX `idx_jcp_step` (`step_id`),
                INDEX `idx_jcp_status` (`status`),
                UNIQUE KEY `uk_jcp_card_step` (`job_card_id`, `step_id`),
                CONSTRAINT `fk_jcp_card` FOREIGN KEY (`job_card_id`) REFERENCES `job_cards`(`id`) ON DELETE CASCADE,
                CONSTRAINT `fk_jcp_step` FOREIGN KEY (`step_id`) REFERENCES `job_card_type_steps`(`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $created[] = 'job_card_progress';
    } else {
        $skipped[] = 'job_card_progress';
    }

    // 7. job_card_attachments
    if (!Schema::hasTable('job_card_attachments')) {
        DB::statement("
            CREATE TABLE `job_card_attachments` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `job_card_id` BIGINT UNSIGNED NOT NULL,
                `step_id` BIGINT UNSIGNED NULL,
                `document_type_id` BIGINT UNSIGNED NULL,
                `cims_document_id` BIGINT UNSIGNED NULL,
                `file_name` VARCHAR(255) NOT NULL,
                `file_original_name` VARCHAR(255) NULL,
                `file_path` VARCHAR(500) NOT NULL,
                `file_mime_type` VARCHAR(100) NULL,
                `file_size` BIGINT UNSIGNED NULL,
                `file_type` ENUM('source_document','internal_pack','external_pack','other') NOT NULL DEFAULT 'source_document',
                `uploaded_by` BIGINT UNSIGNED NULL,
                `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                INDEX `idx_jca_card` (`job_card_id`),
                INDEX `idx_jca_step` (`step_id`),
                INDEX `idx_jca_type` (`file_type`),
                CONSTRAINT `fk_jca_card` FOREIGN KEY (`job_card_id`) REFERENCES `job_cards`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $created[] = 'job_card_attachments';
    } else {
        $skipped[] = 'job_card_attachments';
    }

    echo "<h2>Job Cards Migration Complete</h2>";
    echo "<p><strong>Created:</strong> " . (count($created) ? implode(', ', $created) : 'None') . "</p>";
    echo "<p><strong>Skipped (already exist):</strong> " . (count($skipped) ? implode(', ', $skipped) : 'None') . "</p>";
    echo "<p>Total: " . count($created) . " created, " . count($skipped) . " skipped</p>";

} catch (\Exception $e) {
    echo "<h2>Migration Error</h2>";
    echo "<pre>" . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
}
