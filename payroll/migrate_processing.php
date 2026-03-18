<?php
/**
 * CIMS Payroll Processing Migration — New employee fields + Medical Aid + Private RA tables
 * Run once via browser, then delete.
 */

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "<pre>\n";
echo "=== CIMS PAYROLL PROCESSING MIGRATION ===\n\n";

try {
    // ─── 1: ADD NEW COLUMNS TO cims_payroll_employees ───
    echo "--- Adding new columns to cims_payroll_employees ---\n";

    $newColumns = [
        'title' => "VARCHAR(10) NULL AFTER `id_number`",
        'initials' => "VARCHAR(10) NULL AFTER `title`",
        'second_name' => "VARCHAR(100) NULL AFTER `first_name`",
        'known_as' => "VARCHAR(100) NULL AFTER `last_name`",
        'employee_type' => "ENUM('person','psp') NOT NULL DEFAULT 'person' AFTER `known_as`",
        'passport_number' => "VARCHAR(50) NULL AFTER `id_number`",
        'passport_country' => "VARCHAR(100) NULL AFTER `passport_number`",
        'working_hours_per_day' => "DECIMAL(4,2) NOT NULL DEFAULT 9.00 AFTER `pay_type`",
        'working_days_per_week' => "DECIMAL(4,2) NOT NULL DEFAULT 5.00 AFTER `working_hours_per_day`",
        'must_capture_hours' => "TINYINT(1) NOT NULL DEFAULT 0 AFTER `working_days_per_week`",
        'address_line1' => "VARCHAR(255) NULL AFTER `address`",
        'address_line2' => "VARCHAR(255) NULL AFTER `address_line1`",
        'city' => "VARCHAR(100) NULL AFTER `address_line2`",
        'province' => "VARCHAR(100) NULL AFTER `city`",
        'postal_code' => "VARCHAR(20) NULL AFTER `province`",
        'pay_method' => "ENUM('electronic','cheque','cash') NOT NULL DEFAULT 'electronic' AFTER `bank_account_type`",
        // ETI fields
        'eti_prescribed_min_wage' => "TINYINT(1) NOT NULL DEFAULT 0 AFTER `pay_method`",
        'eti_national_min_wage' => "TINYINT(1) NOT NULL DEFAULT 0 AFTER `eti_prescribed_min_wage`",
        'eti_min_rate' => "DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER `eti_national_min_wage`",
        'eti_fixed_hours' => "TINYINT(1) NOT NULL DEFAULT 0 AFTER `eti_min_rate`",
        'eti_sez' => "TINYINT(1) NOT NULL DEFAULT 0 AFTER `eti_fixed_hours`",
        'eti_connected' => "TINYINT(1) NOT NULL DEFAULT 0 AFTER `eti_sez`",
        'eti_domestic' => "TINYINT(1) NOT NULL DEFAULT 0 AFTER `eti_connected`",
        'eti_labour_broker' => "TINYINT(1) NOT NULL DEFAULT 0 AFTER `eti_domestic`",
    ];

    foreach ($newColumns as $column => $definition) {
        if (!Schema::hasColumn('cims_payroll_employees', $column)) {
            DB::statement("ALTER TABLE cims_payroll_employees ADD COLUMN `{$column}` {$definition}");
            echo "  Added column: {$column}\n";
        } else {
            echo "  Column already exists: {$column}\n";
        }
    }

    // ─── 2: CREATE Medical Aid Table ───
    echo "\n--- Creating cims_payroll_medical_aid table ---\n";

    if (!Schema::hasTable('cims_payroll_medical_aid')) {
        DB::statement('CREATE TABLE cims_payroll_medical_aid (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            employee_id BIGINT UNSIGNED NOT NULL,
            tax_year VARCHAR(10) NOT NULL,
            month TINYINT UNSIGNED NOT NULL,
            scheme_name VARCHAR(200) NULL,
            plan_type VARCHAR(100) NULL,
            member_number VARCHAR(50) NULL,
            main_member TINYINT(1) NOT NULL DEFAULT 1,
            adult_dependants TINYINT UNSIGNED NOT NULL DEFAULT 0,
            child_dependants TINYINT UNSIGNED NOT NULL DEFAULT 0,
            employee_contribution DECIMAL(10,2) NOT NULL DEFAULT 0,
            employer_contribution DECIMAL(10,2) NOT NULL DEFAULT 0,
            tax_credit DECIMAL(10,2) NOT NULL DEFAULT 0,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX idx_ma_employee (employee_id),
            INDEX idx_ma_year_month (tax_year, month),
            UNIQUE KEY uk_ma_emp_year_month (employee_id, tax_year, month),
            CONSTRAINT fk_ma_employee FOREIGN KEY (employee_id) REFERENCES cims_payroll_employees(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        echo "  Created cims_payroll_medical_aid\n";
    } else {
        echo "  cims_payroll_medical_aid already exists\n";
    }

    // ─── 3: CREATE Private RA Table ───
    echo "\n--- Creating cims_payroll_private_ra table ---\n";

    if (!Schema::hasTable('cims_payroll_private_ra')) {
        DB::statement('CREATE TABLE cims_payroll_private_ra (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            employee_id BIGINT UNSIGNED NOT NULL,
            provider_name VARCHAR(200) NOT NULL,
            policy_number VARCHAR(100) NULL,
            contribution_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
            contribution_type ENUM(\'fixed\',\'percentage\') NOT NULL DEFAULT \'fixed\',
            percentage_of_salary DECIMAL(5,2) NOT NULL DEFAULT 0,
            start_date DATE NULL,
            end_date DATE NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            notes TEXT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX idx_ra_employee (employee_id),
            CONSTRAINT fk_ra_employee FOREIGN KEY (employee_id) REFERENCES cims_payroll_employees(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        echo "  Created cims_payroll_private_ra\n";
    } else {
        echo "  cims_payroll_private_ra already exists\n";
    }

    echo "\n=== PROCESSING MIGRATION COMPLETE ===\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "</pre>\n";
