<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre>\n";
echo "=== CIMSAppointments - Direct SQL Deploy ===\n\n";

$appBase = __DIR__ . '/application';
require $appBase . '/vendor/autoload.php';
$app = require $appBase . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$db = DB::connection();
$pdo = $db->getPdo();

// Helper
function runSQL($pdo, $sql, $label) {
    try {
        $pdo->exec($sql);
        echo "OK: $label\n";
        return true;
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'already exists') !== false) {
            echo "SKIP (already exists): $label\n";
            return true;
        }
        echo "ERROR: $label - " . $e->getMessage() . "\n";
        return false;
    }
}

// =============================================
// TABLE 1: cims_appointments_services
// =============================================
runSQL($pdo, "CREATE TABLE IF NOT EXISTS `cims_appointments_services` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` varchar(150) NOT NULL,
    `description` text NULL,
    `default_duration_minutes` int NOT NULL DEFAULT 60,
    `min_duration_minutes` int NOT NULL DEFAULT 60,
    `max_duration_minutes` int NOT NULL DEFAULT 240,
    `is_chargeable` tinyint(1) NOT NULL DEFAULT 0,
    `price_per_hour` decimal(15,2) NOT NULL DEFAULT 0.00,
    `color` varchar(20) NULL COMMENT 'Hex color for calendar display',
    `sort_order` int NOT NULL DEFAULT 0,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `created_by` bigint unsigned NULL,
    `updated_by` bigint unsigned NULL,
    `created_at` timestamp NULL,
    `updated_at` timestamp NULL,
    `deleted_at` timestamp NULL,
    INDEX `idx_services_active` (`is_active`),
    INDEX `idx_services_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", "cims_appointments_services");

// =============================================
// TABLE 2: cims_appointments_staff
// =============================================
runSQL($pdo, "CREATE TABLE IF NOT EXISTS `cims_appointments_staff` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` bigint unsigned NULL COMMENT 'Links to Laravel users table',
    `name` varchar(150) NOT NULL,
    `email` varchar(255) NULL,
    `phone` varchar(30) NULL,
    `position` varchar(100) NULL,
    `color` varchar(20) NULL COMMENT 'Hex color for calendar display',
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `created_by` bigint unsigned NULL,
    `updated_by` bigint unsigned NULL,
    `created_at` timestamp NULL,
    `updated_at` timestamp NULL,
    `deleted_at` timestamp NULL,
    INDEX `idx_staff_user` (`user_id`),
    INDEX `idx_staff_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", "cims_appointments_staff");

// =============================================
// TABLE 3: cims_appointments_staff_services (pivot)
// =============================================
runSQL($pdo, "CREATE TABLE IF NOT EXISTS `cims_appointments_staff_services` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `staff_id` bigint unsigned NOT NULL,
    `service_id` bigint unsigned NOT NULL,
    `created_at` timestamp NULL,
    `updated_at` timestamp NULL,
    UNIQUE KEY `uniq_staff_service` (`staff_id`, `service_id`),
    CONSTRAINT `fk_ss_staff` FOREIGN KEY (`staff_id`) REFERENCES `cims_appointments_staff` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ss_service` FOREIGN KEY (`service_id`) REFERENCES `cims_appointments_services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", "cims_appointments_staff_services");

// =============================================
// TABLE 4: cims_appointments_availability
// =============================================
runSQL($pdo, "CREATE TABLE IF NOT EXISTS `cims_appointments_availability` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `staff_id` bigint unsigned NOT NULL,
    `day_of_week` tinyint NOT NULL COMMENT '0=Monday, 1=Tuesday, 2=Wednesday, 3=Thursday, 4=Friday, 5=Saturday',
    `start_time` time NOT NULL,
    `end_time` time NOT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` timestamp NULL,
    `updated_at` timestamp NULL,
    INDEX `idx_avail_staff_day` (`staff_id`, `day_of_week`),
    INDEX `idx_avail_active` (`is_active`),
    CONSTRAINT `fk_avail_staff` FOREIGN KEY (`staff_id`) REFERENCES `cims_appointments_staff` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", "cims_appointments_availability");

// =============================================
// TABLE 5: cims_appointments_blocked_dates
// =============================================
runSQL($pdo, "CREATE TABLE IF NOT EXISTS `cims_appointments_blocked_dates` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `staff_id` bigint unsigned NULL COMMENT 'Null = applies to all staff',
    `blocked_date` date NOT NULL,
    `reason` varchar(255) NULL,
    `created_by` bigint unsigned NULL,
    `created_at` timestamp NULL,
    `updated_at` timestamp NULL,
    INDEX `idx_blocked_date` (`blocked_date`),
    INDEX `idx_blocked_staff` (`staff_id`),
    CONSTRAINT `fk_blocked_staff` FOREIGN KEY (`staff_id`) REFERENCES `cims_appointments_staff` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", "cims_appointments_blocked_dates");

// =============================================
// TABLE 6: cims_appointments (main)
// =============================================
runSQL($pdo, "CREATE TABLE IF NOT EXISTS `cims_appointments` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `client_id` bigint unsigned NULL,
    `client_code` varchar(50) NULL,
    `client_name` varchar(255) NULL,
    `client_email` varchar(255) NULL,
    `client_phone` varchar(30) NULL,
    `staff_id` bigint unsigned NOT NULL,
    `service_id` bigint unsigned NOT NULL,
    `appointment_date` date NOT NULL,
    `start_time` time NOT NULL,
    `end_time` time NOT NULL,
    `duration_hours` int NOT NULL DEFAULT 1,
    `status` varchar(20) NOT NULL DEFAULT 'pending',
    `notes` text NULL,
    `internal_notes` text NULL,
    `is_chargeable` tinyint(1) NOT NULL DEFAULT 0,
    `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
    `payment_status` varchar(20) NOT NULL DEFAULT 'unpaid' COMMENT 'unpaid, paid, waived, invoiced',
    `invoice_id` bigint unsigned NULL COMMENT 'Links to Grow CRM invoices',
    `confirmation_sent_at` timestamp NULL,
    `reminder_sent_at` timestamp NULL,
    `cancelled_at` timestamp NULL,
    `cancellation_reason` varchar(500) NULL,
    `completed_at` timestamp NULL,
    `created_by` bigint unsigned NULL,
    `updated_by` bigint unsigned NULL,
    `created_at` timestamp NULL,
    `updated_at` timestamp NULL,
    `deleted_at` timestamp NULL,
    INDEX `idx_appt_client` (`client_id`),
    INDEX `idx_appt_code` (`client_code`),
    INDEX `idx_appt_staff` (`staff_id`),
    INDEX `idx_appt_service` (`service_id`),
    INDEX `idx_appt_date` (`appointment_date`),
    INDEX `idx_appt_status` (`status`),
    INDEX `idx_appt_payment` (`payment_status`),
    INDEX `idx_appt_date_staff` (`appointment_date`, `staff_id`),
    INDEX `idx_appt_date_status` (`appointment_date`, `status`),
    CONSTRAINT `fk_appt_staff` FOREIGN KEY (`staff_id`) REFERENCES `cims_appointments_staff` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_appt_service` FOREIGN KEY (`service_id`) REFERENCES `cims_appointments_services` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", "cims_appointments");

// =============================================
// TABLE 7: cims_appointments_settings
// =============================================
runSQL($pdo, "CREATE TABLE IF NOT EXISTS `cims_appointments_settings` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `setting_key` varchar(100) NOT NULL,
    `setting_value` text NULL,
    `setting_group` varchar(50) NOT NULL DEFAULT 'general',
    `created_at` timestamp NULL,
    `updated_at` timestamp NULL,
    UNIQUE KEY `uniq_setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", "cims_appointments_settings");

// =============================================
// SEED DEFAULT SETTINGS
// =============================================
echo "\n--- Seeding Settings ---\n";
$settingsCount = $pdo->query("SELECT COUNT(*) FROM cims_appointments_settings")->fetchColumn();
if ($settingsCount == 0) {
    $now = date('Y-m-d H:i:s');
    $settings = [
        ['confirmation_email_enabled', '1', 'email'],
        ['reminder_email_enabled', '1', 'email'],
        ['reminder_hours_before', '24', 'email'],
        ['cancellation_email_enabled', '1', 'email'],
        ['booking_buffer_hours', '2', 'booking'],
        ['cancellation_policy_hours', '24', 'booking'],
        ['default_slot_duration', '60', 'booking'],
        ['company_name', 'ATP Services', 'general'],
        ['company_email', 'info@atpservices.co.za', 'general'],
        ['company_phone', '(031) 101 3876', 'general'],
        ['company_address', '29 Coedmore Road, Bellair, Durban 4094', 'general'],
    ];
    $stmt = $pdo->prepare("INSERT INTO cims_appointments_settings (setting_key, setting_value, setting_group, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
    foreach ($settings as $s) {
        $stmt->execute([$s[0], $s[1], $s[2], $now, $now]);
    }
    echo "Seeded " . count($settings) . " default settings\n";
} else {
    echo "Settings already seeded ($settingsCount records)\n";
}

// =============================================
// REGISTER IN MIGRATIONS TABLE
// =============================================
echo "\n--- Registering Migrations ---\n";
$migrations = [
    '2026_03_18_000001_create_cims_appointments_services_table',
    '2026_03_18_000002_create_cims_appointments_staff_table',
    '2026_03_18_000003_create_cims_appointments_staff_services_table',
    '2026_03_18_000004_create_cims_appointments_availability_table',
    '2026_03_18_000005_create_cims_appointments_blocked_dates_table',
    '2026_03_18_000006_create_cims_appointments_table',
    '2026_03_18_000007_create_cims_appointments_settings_table',
];

// Get max batch number
$maxBatch = $pdo->query("SELECT COALESCE(MAX(batch), 0) FROM migrations")->fetchColumn();
$newBatch = $maxBatch + 1;

foreach ($migrations as $m) {
    $exists = $pdo->prepare("SELECT COUNT(*) FROM migrations WHERE migration = ?");
    $exists->execute([$m]);
    if ($exists->fetchColumn() > 0) {
        echo "SKIP (already registered): $m\n";
    } else {
        $ins = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
        $ins->execute([$m, $newBatch]);
        echo "REGISTERED: $m (batch $newBatch)\n";
    }
}

// =============================================
// VERIFY
// =============================================
echo "\n--- Verification ---\n";
$tables = [
    'cims_appointments_services',
    'cims_appointments_staff',
    'cims_appointments_staff_services',
    'cims_appointments_availability',
    'cims_appointments_blocked_dates',
    'cims_appointments',
    'cims_appointments_settings',
];
foreach ($tables as $t) {
    $exists = \Illuminate\Support\Facades\Schema::hasTable($t);
    echo "$t: " . ($exists ? "OK" : "MISSING") . "\n";
}

// Check routes
echo "\n--- Routes ---\n";
$routes = ['cimsappointments.dashboard', 'cimsappointments.appointments.index', 'cimsappointments.appointments.create', 'cimsappointments.services.index', 'cimsappointments.staff.index'];
foreach ($routes as $name) {
    echo "$name: " . (\Illuminate\Support\Facades\Route::has($name) ? "REGISTERED" : "NOT FOUND") . "\n";
}

// Check settings
echo "\n--- Settings ---\n";
$count = DB::table('cims_appointments_settings')->count();
echo "Settings records: $count\n";

echo "\n=== Deployment Complete ===\n";
echo "</pre>";
