-- =====================================================
-- JOB CARDS MODULE — DATABASE TABLES
-- Run this on the CIMS database to create all tables
-- =====================================================

-- 1. JOB CARD TYPES (master list of job types)
CREATE TABLE IF NOT EXISTS `cims_job_card_types` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `submission_to` VARCHAR(100) NULL COMMENT 'e.g. SARS, CIPC, Other',
    `display_order` INT NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_by` BIGINT UNSIGNED NULL,
    `updated_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_job_card_types_active` (`is_active`),
    INDEX `idx_job_card_types_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. JOB CARD TYPE STEPS (predefined checklist steps per job type)
CREATE TABLE IF NOT EXISTS `cims_job_card_type_steps` (
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
    INDEX `idx_type_steps_type` (`job_type_id`),
    INDEX `idx_type_steps_order` (`display_order`),
    CONSTRAINT `fk_type_steps_type` FOREIGN KEY (`job_type_id`) REFERENCES `cims_job_card_types`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. JOB CARD TYPE FIELDS (which client_master fields to show per job type)
CREATE TABLE IF NOT EXISTS `cims_job_card_type_fields` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `job_type_id` BIGINT UNSIGNED NOT NULL,
    `field_name` VARCHAR(100) NOT NULL COMMENT 'Maps to client_master column name',
    `field_label` VARCHAR(150) NOT NULL COMMENT 'Display label on job card',
    `display_order` INT NOT NULL DEFAULT 0,
    `is_required` TINYINT(1) NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_type_fields_type` (`job_type_id`),
    INDEX `idx_type_fields_order` (`display_order`),
    CONSTRAINT `fk_type_fields_type` FOREIGN KEY (`job_type_id`) REFERENCES `cims_job_card_types`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. JOB CARD TYPE DOCUMENTS (which documents are required per job type)
CREATE TABLE IF NOT EXISTS `cims_job_card_type_documents` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `job_type_id` BIGINT UNSIGNED NOT NULL,
    `document_type_id` BIGINT UNSIGNED NULL COMMENT 'FK to cims_document_types if available',
    `document_label` VARCHAR(255) NOT NULL,
    `is_required` TINYINT(1) NOT NULL DEFAULT 1,
    `display_order` INT NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_type_docs_type` (`job_type_id`),
    INDEX `idx_type_docs_order` (`display_order`),
    CONSTRAINT `fk_type_docs_type` FOREIGN KEY (`job_type_id`) REFERENCES `cims_job_card_types`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. JOB CARDS (the actual job cards)
CREATE TABLE IF NOT EXISTS `cims_job_cards` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `job_code` VARCHAR(50) NOT NULL COMMENT 'Auto-generated e.g. JC-2026-0001',
    `client_id` BIGINT NOT NULL COMMENT 'FK to client_master.client_id',
    `job_type_id` BIGINT UNSIGNED NOT NULL,
    `assigned_to` BIGINT UNSIGNED NULL COMMENT 'FK to users table',
    `followed_by` BIGINT UNSIGNED NULL COMMENT 'FK to users table - follower',
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
    UNIQUE KEY `uk_job_cards_code` (`job_code`),
    INDEX `idx_job_cards_client` (`client_id`),
    INDEX `idx_job_cards_type` (`job_type_id`),
    INDEX `idx_job_cards_status` (`status`),
    INDEX `idx_job_cards_assigned` (`assigned_to`),
    INDEX `idx_job_cards_due` (`due_date`),
    INDEX `idx_job_cards_deleted` (`deleted_at`),
    CONSTRAINT `fk_job_cards_type` FOREIGN KEY (`job_type_id`) REFERENCES `cims_job_card_types`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. JOB CARD PROGRESS (step-by-step tracking per job card)
CREATE TABLE IF NOT EXISTS `cims_job_card_progress` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `job_card_id` BIGINT UNSIGNED NOT NULL,
    `step_id` BIGINT UNSIGNED NOT NULL COMMENT 'FK to job_card_type_steps',
    `status` ENUM('pending','in_progress','completed','skipped','na') NOT NULL DEFAULT 'pending',
    `completed_by` BIGINT UNSIGNED NULL,
    `completed_at` TIMESTAMP NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_progress_card` (`job_card_id`),
    INDEX `idx_progress_step` (`step_id`),
    INDEX `idx_progress_status` (`status`),
    UNIQUE KEY `uk_progress_card_step` (`job_card_id`, `step_id`),
    CONSTRAINT `fk_progress_card` FOREIGN KEY (`job_card_id`) REFERENCES `cims_job_cards`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_progress_step` FOREIGN KEY (`step_id`) REFERENCES `cims_job_card_type_steps`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. JOB CARD ATTACHMENTS (documents collected/generated for the job card)
CREATE TABLE IF NOT EXISTS `cims_job_card_attachments` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `job_card_id` BIGINT UNSIGNED NOT NULL,
    `step_id` BIGINT UNSIGNED NULL COMMENT 'FK to job_card_type_steps if linked to a step',
    `document_type_id` BIGINT UNSIGNED NULL COMMENT 'FK to cims_document_types if applicable',
    `cims_document_id` BIGINT UNSIGNED NULL COMMENT 'FK to cims_documents if pulled from existing docs',
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
    INDEX `idx_attachments_card` (`job_card_id`),
    INDEX `idx_attachments_step` (`step_id`),
    INDEX `idx_attachments_type` (`file_type`),
    CONSTRAINT `fk_attachments_card` FOREIGN KEY (`job_card_id`) REFERENCES `cims_job_cards`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
