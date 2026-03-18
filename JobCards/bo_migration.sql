-- =====================================================
-- BO WORKFLOW — ALTER cims_job_card_attachments
-- Adds columns and ENUM values needed for BO documents
-- =====================================================

-- 1. Expand file_type ENUM to include BO-related types
ALTER TABLE `cims_job_card_attachments`
    MODIFY COLUMN `file_type` ENUM(
        'source_document',
        'internal_pack',
        'external_pack',
        'bo_document',
        'id_document',
        'poa_document',
        'other'
    ) NOT NULL DEFAULT 'source_document';

-- 2. Add director_id column (links attachment to a specific director)
ALTER TABLE `cims_job_card_attachments`
    ADD COLUMN `director_id` BIGINT UNSIGNED NULL AFTER `file_type`,
    ADD INDEX `idx_attachments_director` (`director_id`);

-- 3. Add document_category column (e.g. cra01, poa, id_front, register_of_shareholders, etc.)
ALTER TABLE `cims_job_card_attachments`
    ADD COLUMN `document_category` VARCHAR(100) NULL AFTER `director_id`;
