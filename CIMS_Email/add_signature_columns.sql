-- Add new signature fields: WhatsApp, Direct Number, Slogan, Disclaimer
ALTER TABLE `cims_email_signatures`
ADD COLUMN `whatsapp` VARCHAR(50) NULL DEFAULT '' AFTER `mobile`,
ADD COLUMN `direct_number` VARCHAR(50) NULL DEFAULT '' AFTER `whatsapp`,
ADD COLUMN `slogan` VARCHAR(500) NULL DEFAULT '' AFTER `company_website`,
ADD COLUMN `disclaimer_html` TEXT NULL AFTER `slogan`;
