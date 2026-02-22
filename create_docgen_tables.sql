-- ============================================================
-- Document Generator Module - Database Setup Script
-- Run this SQL on your MySQL database to create the required tables.
-- ============================================================

-- 1. Master document templates
CREATE TABLE IF NOT EXISTS `docgen_templates` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `code` VARCHAR(50) NOT NULL,
    `description` TEXT NULL,
    `category` VARCHAR(255) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_by` BIGINT UNSIGNED NULL,
    `updated_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    UNIQUE KEY `docgen_templates_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Template pages (one template = many pages, reorderable)
CREATE TABLE IF NOT EXISTS `docgen_template_pages` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `template_id` BIGINT UNSIGNED NOT NULL,
    `page_number` INT NOT NULL,
    `page_label` VARCHAR(255) NULL,
    `pdf_path` VARCHAR(255) NOT NULL COMMENT 'Path to background PDF page file',
    `orientation` VARCHAR(255) NOT NULL DEFAULT 'portrait',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    CONSTRAINT `docgen_tpl_pages_template_fk` FOREIGN KEY (`template_id`) REFERENCES `docgen_templates` (`id`) ON DELETE CASCADE,
    UNIQUE KEY `docgen_tpl_pages_unique` (`template_id`, `page_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Field mappings - defines where each data field goes on a page (x,y coordinates)
CREATE TABLE IF NOT EXISTS `docgen_field_mappings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `template_page_id` BIGINT UNSIGNED NOT NULL,
    `field_name` VARCHAR(255) NOT NULL COMMENT 'Database column or custom field name',
    `field_label` VARCHAR(255) NOT NULL,
    `field_source` VARCHAR(255) NOT NULL DEFAULT 'client_master' COMMENT 'Table/source: client_master, client_master_directors, client_master_addresses, form_input',
    `pos_x` DECIMAL(8,2) NOT NULL COMMENT 'X position in mm from left',
    `pos_y` DECIMAL(8,2) NOT NULL COMMENT 'Y position in mm from top',
    `width` DECIMAL(8,2) NULL COMMENT 'Max width in mm',
    `height` DECIMAL(8,2) NULL COMMENT 'Max height in mm',
    `font_family` VARCHAR(255) NULL COMMENT 'Override global font',
    `font_size` DECIMAL(5,2) NULL COMMENT 'Override global size',
    `font_style` VARCHAR(255) NULL COMMENT 'bold, italic, underline',
    `font_color` VARCHAR(20) NULL COMMENT 'Hex color e.g. #000000',
    `text_align` VARCHAR(255) NOT NULL DEFAULT 'left' COMMENT 'left, center, right',
    `field_type` VARCHAR(255) NOT NULL DEFAULT 'text' COMMENT 'text, date, checkbox, image, signature',
    `date_format` VARCHAR(255) NULL COMMENT 'e.g. d/m/Y',
    `default_value` VARCHAR(255) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    CONSTRAINT `docgen_field_mappings_page_fk` FOREIGN KEY (`template_page_id`) REFERENCES `docgen_template_pages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Generated documents
CREATE TABLE IF NOT EXISTS `docgen_documents` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `template_id` BIGINT UNSIGNED NOT NULL,
    `client_id` BIGINT UNSIGNED NULL,
    `client_code` VARCHAR(50) NULL,
    `client_name` VARCHAR(255) NULL,
    `document_name` VARCHAR(255) NOT NULL,
    `document_number` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(255) NULL,
    `file_name` VARCHAR(255) NULL,
    `file_size` BIGINT NULL,
    `requested_by` VARCHAR(255) NULL,
    `prepared_by` VARCHAR(255) NULL,
    `approved_by` VARCHAR(255) NULL,
    `signed_by` VARCHAR(255) NULL,
    `document_date` DATE NULL,
    `notes` TEXT NULL,
    `status` VARCHAR(255) NOT NULL DEFAULT 'active' COMMENT 'active, inactive, deleted',
    `emailed` TINYINT(1) NOT NULL DEFAULT 0,
    `emailed_to` VARCHAR(255) NULL,
    `emailed_at` TIMESTAMP NULL,
    `generated_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    UNIQUE KEY `docgen_documents_number_unique` (`document_number`),
    CONSTRAINT `docgen_documents_template_fk` FOREIGN KEY (`template_id`) REFERENCES `docgen_templates` (`id`),
    KEY `docgen_documents_client_id_index` (`client_id`),
    KEY `docgen_documents_client_code_index` (`client_code`),
    KEY `docgen_documents_number_index` (`document_number`),
    KEY `docgen_documents_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Audit log for document actions
CREATE TABLE IF NOT EXISTS `docgen_audit_log` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `document_id` BIGINT UNSIGNED NOT NULL,
    `action` VARCHAR(255) NOT NULL COMMENT 'generated, viewed, emailed, downloaded, status_changed, deleted',
    `action_by` VARCHAR(255) NULL,
    `action_by_id` BIGINT UNSIGNED NULL,
    `details` TEXT NULL,
    `ip_address` VARCHAR(45) NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    CONSTRAINT `docgen_audit_log_document_fk` FOREIGN KEY (`document_id`) REFERENCES `docgen_documents` (`id`) ON DELETE CASCADE,
    KEY `docgen_audit_log_document_id_index` (`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Global settings (key-value pairs)
CREATE TABLE IF NOT EXISTS `docgen_settings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(255) NOT NULL,
    `setting_value` TEXT NULL,
    `setting_group` VARCHAR(255) NOT NULL DEFAULT 'general',
    `setting_type` VARCHAR(255) NOT NULL DEFAULT 'text' COMMENT 'text, number, select, color, boolean',
    `label` VARCHAR(255) NULL,
    `description` TEXT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    UNIQUE KEY `docgen_settings_key_unique` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Insert default settings
INSERT INTO `docgen_settings` (`setting_key`, `setting_value`, `setting_group`, `setting_type`, `label`, `description`, `created_at`, `updated_at`) VALUES
('default_font_family', 'Helvetica', 'general', 'select', 'Default Font Family', 'Default font for document text overlay', NOW(), NOW()),
('default_font_size', '10', 'general', 'number', 'Default Font Size', 'Default font size in points', NOW(), NOW()),
('default_font_color', '#000000', 'general', 'color', 'Default Font Color', 'Default text color (hex)', NOW(), NOW()),
('default_text_align', 'left', 'general', 'select', 'Default Text Alignment', 'Default text alignment for fields', NOW(), NOW()),
('company_name', '', 'general', 'text', 'Company Name', 'Your company name for documents', NOW(), NOW()),
('company_logo_path', '', 'general', 'text', 'Company Logo', 'Path to company logo file', NOW(), NOW()),
('document_storage_path', 'docgen/documents', 'general', 'text', 'Storage Path', 'Relative storage path for generated documents', NOW(), NOW()),
('smtp_host', '', 'smtp', 'text', 'SMTP Host', 'SMTP server hostname', NOW(), NOW()),
('smtp_port', '587', 'smtp', 'number', 'SMTP Port', 'SMTP server port', NOW(), NOW()),
('smtp_username', '', 'smtp', 'text', 'SMTP Username', 'SMTP authentication username', NOW(), NOW()),
('smtp_password', '', 'smtp', 'text', 'SMTP Password', 'SMTP authentication password', NOW(), NOW()),
('smtp_encryption', 'tls', 'smtp', 'select', 'SMTP Encryption', 'Encryption method (tls, ssl, or none)', NOW(), NOW()),
('smtp_from_address', '', 'smtp', 'text', 'From Address', 'Email sender address', NOW(), NOW()),
('smtp_from_name', '', 'smtp', 'text', 'From Name', 'Email sender display name', NOW(), NOW())
ON DUPLICATE KEY UPDATE `setting_key` = `setting_key`;
