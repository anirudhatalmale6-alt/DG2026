-- ============================================================
-- Add category_id column to docgen_templates table
-- Links templates to cims_form_template_categories
-- ============================================================

ALTER TABLE `docgen_templates`
ADD COLUMN `category_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `category`;

ALTER TABLE `docgen_templates`
ADD INDEX `idx_category_id` (`category_id`);
