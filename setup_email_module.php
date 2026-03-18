<?php
// Setup CIMS_Email module - run once then delete
error_reporting(E_ALL);

$host = 'localhost';
$db = 'grow_crm_2026';
$user = '5fokp_qnbo1';
$pass = '4P9716bzm7598A';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create cims_emails table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `cims_emails` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `client_id` bigint(20) unsigned DEFAULT NULL,
        `user_id` bigint(20) unsigned NOT NULL,
        `from_email` varchar(255) NOT NULL DEFAULT '',
        `from_name` varchar(255) NOT NULL DEFAULT '',
        `to_emails` json DEFAULT NULL,
        `cc_emails` json DEFAULT NULL,
        `bcc_emails` json DEFAULT NULL,
        `subject` varchar(500) NOT NULL DEFAULT '',
        `body_html` longtext DEFAULT NULL,
        `body_text` longtext DEFAULT NULL,
        `status` enum('draft','sending','sent','failed') NOT NULL DEFAULT 'draft',
        `folder` enum('sent','drafts','trash') NOT NULL DEFAULT 'sent',
        `is_read` tinyint(1) NOT NULL DEFAULT 0,
        `parent_id` bigint(20) unsigned DEFAULT NULL,
        `sent_at` timestamp NULL DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        `deleted_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `idx_cims_emails_user_folder` (`user_id`, `folder`),
        KEY `idx_cims_emails_client` (`client_id`),
        KEY `idx_cims_emails_status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Created cims_emails table\n";

    // Create cims_email_attachments table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `cims_email_attachments` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `email_id` bigint(20) unsigned NOT NULL,
        `filename` varchar(255) NOT NULL,
        `original_filename` varchar(255) NOT NULL,
        `file_path` varchar(500) NOT NULL,
        `file_size` bigint(20) unsigned DEFAULT 0,
        `mime_type` varchar(100) DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `idx_cims_email_att_email` (`email_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Created cims_email_attachments table\n";

    // Create cims_email_templates table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `cims_email_templates` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `name` varchar(200) NOT NULL,
        `subject` varchar(500) NOT NULL DEFAULT '',
        `body_html` longtext DEFAULT NULL,
        `category` varchar(100) NOT NULL DEFAULT 'General',
        `is_active` tinyint(1) NOT NULL DEFAULT 1,
        `created_by` bigint(20) unsigned DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Created cims_email_templates table\n";

    // Insert default templates (only if table is empty)
    $count = $pdo->query("SELECT COUNT(*) FROM cims_email_templates")->fetchColumn();
    if ($count == 0) {
        $pdo->exec("INSERT INTO `cims_email_templates` (`name`, `subject`, `body_html`, `category`, `is_active`, `created_at`, `updated_at`) VALUES
        ('Welcome Letter', 'Welcome to {company_name}', '<div style=\"font-family:Arial,sans-serif;max-width:600px;margin:0 auto;\"><div style=\"background:linear-gradient(135deg,#0e6977,#148f9f);padding:20px;text-align:center;border-radius:8px 8px 0 0;\"><h1 style=\"color:#fff;margin:0;font-size:24px;\">Welcome</h1></div><div style=\"padding:30px;background:#fff;border:1px solid #e0e0e0;\"><p>Dear {client_name},</p><p>Welcome to our services. We look forward to working with you.</p><p>Should you have any questions, please do not hesitate to contact us.</p><p style=\"margin-top:30px;\">Kind Regards,<br><strong>{user_name}</strong></p></div><div style=\"background:#f5f5f5;padding:15px;text-align:center;font-size:12px;color:#888;border-radius:0 0 8px 8px;\">SmartWeigh</div></div>', 'General', 1, NOW(), NOW()),
        ('EMP201 Reminder', 'EMP201 Return Reminder - {month} {year}', '<div style=\"font-family:Arial,sans-serif;max-width:600px;margin:0 auto;\"><div style=\"background:linear-gradient(135deg,#0e6977,#148f9f);padding:20px;text-align:center;border-radius:8px 8px 0 0;\"><h1 style=\"color:#fff;margin:0;font-size:22px;\">EMP201 Return Reminder</h1></div><div style=\"padding:30px;background:#fff;border:1px solid #e0e0e0;\"><p>Dear {client_name},</p><p>This is a friendly reminder that your EMP201 monthly return is due for submission.</p><p>Please ensure all payroll declarations are submitted timeously to avoid penalties.</p><p style=\"margin-top:30px;\">Kind Regards,<br><strong>{user_name}</strong></p></div><div style=\"background:#f5f5f5;padding:15px;text-align:center;font-size:12px;color:#888;border-radius:0 0 8px 8px;\">SmartWeigh</div></div>', 'Compliance', 1, NOW(), NOW()),
        ('Tax Compliance Notice', 'Tax Compliance Status Update - {client_name}', '<div style=\"font-family:Arial,sans-serif;max-width:600px;margin:0 auto;\"><div style=\"background:linear-gradient(135deg,#0e6977,#148f9f);padding:20px;text-align:center;border-radius:8px 8px 0 0;\"><h1 style=\"color:#fff;margin:0;font-size:22px;\">Tax Compliance Notice</h1></div><div style=\"padding:30px;background:#fff;border:1px solid #e0e0e0;\"><p>Dear {client_name},</p><p>Please be advised of the following compliance matters that require your attention.</p><p>Please contact our office at your earliest convenience to discuss.</p><p style=\"margin-top:30px;\">Kind Regards,<br><strong>{user_name}</strong></p></div><div style=\"background:#f5f5f5;padding:15px;text-align:center;font-size:12px;color:#888;border-radius:0 0 8px 8px;\">SmartWeigh</div></div>', 'Compliance', 1, NOW(), NOW()),
        ('General Notice', 'Important Notice - {company_name}', '<div style=\"font-family:Arial,sans-serif;max-width:600px;margin:0 auto;\"><div style=\"background:linear-gradient(135deg,#0e6977,#148f9f);padding:20px;text-align:center;border-radius:8px 8px 0 0;\"><h1 style=\"color:#fff;margin:0;font-size:22px;\">Notice</h1></div><div style=\"padding:30px;background:#fff;border:1px solid #e0e0e0;\"><p>Dear {client_name},</p><p></p><p style=\"margin-top:30px;\">Kind Regards,<br><strong>{user_name}</strong></p></div><div style=\"background:#f5f5f5;padding:15px;text-align:center;font-size:12px;color:#888;border-radius:0 0 8px 8px;\">SmartWeigh</div></div>', 'General', 1, NOW(), NOW())");
        echo "Inserted 4 default templates\n";
    } else {
        echo "Templates already exist ($count), skipping\n";
    }

    // Activate module in modules_statuses.json
    $statusFile = '/usr/www/users/smartucbmh/application/modules_statuses.json';
    if (file_exists($statusFile)) {
        $statuses = json_decode(file_get_contents($statusFile), true);
        $statuses['CIMS_Email'] = true;
        file_put_contents($statusFile, json_encode($statuses, JSON_PRETTY_PRINT));
        echo "Module activated in modules_statuses.json\n";
    } else {
        echo "WARNING: modules_statuses.json not found\n";
    }

    // Clear bootstrap cache
    $cacheFiles = glob('/usr/www/users/smartucbmh/application/bootstrap/cache/*.php');
    foreach ($cacheFiles as $f) {
        @unlink($f);
    }
    echo "Bootstrap cache cleared\n";

    echo "\nDONE - CIMS_Email module setup complete!\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
