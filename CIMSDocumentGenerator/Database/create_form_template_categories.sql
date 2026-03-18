-- ============================================================
-- CIMSDocumentGenerator - Form Template Categories Table
-- Run this script on your database to create the table and seed data
-- ============================================================

CREATE TABLE IF NOT EXISTS `cims_form_template_categories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_name` VARCHAR(150) NOT NULL,
    `category_code` VARCHAR(20) NOT NULL,
    `description` VARCHAR(500) NULL DEFAULT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_category_code` (`category_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Seed Data - Accounting, Taxation & Payroll Business
-- ============================================================

INSERT INTO `cims_form_template_categories` (`category_name`, `category_code`, `description`, `sort_order`, `is_active`, `is_deleted`) VALUES

-- Tax & SARS Related
('Tax Returns', 'TAX', 'Individual and company income tax returns', 1, 1, 0),
('Tax Compliance', 'TAXCOMP', 'Tax compliance certificates and related documents', 2, 1, 0),
('Provisional Tax', 'PROV', 'IRP6 provisional tax submissions', 3, 1, 0),
('VAT', 'VAT', 'VAT registration, returns and related documents', 4, 1, 0),
('Employee Tax', 'EMP', 'PAYE, UIF, SDL and employee tax documents', 5, 1, 0),
('Dividends Tax', 'DIV', 'Dividends tax declarations and returns', 6, 1, 0),
('SARS', 'SARS', 'General SARS correspondence, registrations and submissions', 7, 1, 0),
('SARS Registrations', 'SARSR', 'SARS tax type registrations and amendments', 8, 1, 0),

-- Financial & Audit
('Annual Financial Statements', 'AFS', 'Annual financial statements and related reports', 10, 1, 0),
('Bookkeeping', 'BKP', 'Bookkeeping and accounting records', 11, 1, 0),
('Audit & Assurance', 'AUD', 'Audit reports, assurance engagements and review documents', 12, 1, 0),

-- Payroll
('Payroll', 'PAY', 'Payroll processing, payslips and related documents', 15, 1, 0),

-- Company & Regulatory
('Company Secretarial', 'COSEC', 'Company secretarial services and statutory documents', 20, 1, 0),
('CIPC Filings', 'CIPC', 'CIPC annual returns, amendments and company registrations', 21, 1, 0),

-- Government & Compliance Bodies
('COIDA', 'COIDA', 'Compensation for Occupational Injuries and Diseases Act submissions', 25, 1, 0),
('Department of Labour', 'DOL', 'Department of Labour submissions and compliance documents', 26, 1, 0),
('MIBCO', 'MIBCO', 'Motor Industry Bargaining Council submissions and compliance', 27, 1, 0),
('CSD', 'CSD', 'Central Supplier Database registration and maintenance', 28, 1, 0),
('CIDB', 'CIDB', 'Construction Industry Development Board registrations and renewals', 29, 1, 0),
('PSIRA', 'PSIRA', 'Private Security Industry Regulatory Authority compliance', 30, 1, 0),

-- Advisory & Client Management
('Advisory & Consulting', 'ADV', 'Advisory services, consulting reports and recommendations', 35, 1, 0),
('Client Onboarding', 'ONBRD', 'New client onboarding packs, FICA and engagement letters', 36, 1, 0),
('BizPack', 'BIZPK', 'Business pack documents and company setup packages', 37, 1, 0),

-- General
('Compliance', 'COMPL', 'General compliance documents and certificates', 40, 1, 0),
('Other', 'OTHER', 'Documents that do not fall under a specific category', 99, 1, 0);
