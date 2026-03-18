<?php
/**
 * CIMS Payroll Phase 2 Migration — Leave & Timesheets
 * Run once via browser, then delete.
 */

require __DIR__ . '/application/vendor/autoload.php';

$app = require_once __DIR__ . '/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "<pre>\n";
echo "=== CIMS PAYROLL PHASE 2 MIGRATION ===\n\n";

try {
    // ─── TABLE 1: Leave Types ───
    if (!Schema::hasTable('cims_payroll_leave_types')) {
        DB::statement("CREATE TABLE cims_payroll_leave_types (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            code VARCHAR(20) NOT NULL,
            days_per_year DECIMAL(8,2) NOT NULL DEFAULT 0,
            cycle_years INT NOT NULL DEFAULT 1 COMMENT '1=annual, 3=3-year cycle (sick leave)',
            is_paid TINYINT(1) NOT NULL DEFAULT 1,
            is_statutory TINYINT(1) NOT NULL DEFAULT 0,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            description TEXT NULL,
            sort_order INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "✓ Created cims_payroll_leave_types\n";
    } else {
        echo "→ cims_payroll_leave_types already exists\n";
    }

    // ─── TABLE 2: Leave Balances ───
    if (!Schema::hasTable('cims_payroll_leave_balances')) {
        DB::statement("CREATE TABLE cims_payroll_leave_balances (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            employee_id BIGINT UNSIGNED NOT NULL,
            leave_type_id BIGINT UNSIGNED NOT NULL,
            year VARCHAR(4) NOT NULL DEFAULT '2026',
            entitled_days DECIMAL(8,2) NOT NULL DEFAULT 0,
            taken_days DECIMAL(8,2) NOT NULL DEFAULT 0,
            pending_days DECIMAL(8,2) NOT NULL DEFAULT 0,
            carried_forward DECIMAL(8,2) NOT NULL DEFAULT 0,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX idx_emp_year (employee_id, year),
            INDEX idx_leave_type (leave_type_id),
            CONSTRAINT fk_lb_employee FOREIGN KEY (employee_id) REFERENCES cims_payroll_employees(id) ON DELETE CASCADE,
            CONSTRAINT fk_lb_leave_type FOREIGN KEY (leave_type_id) REFERENCES cims_payroll_leave_types(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "✓ Created cims_payroll_leave_balances\n";
    } else {
        echo "→ cims_payroll_leave_balances already exists\n";
    }

    // ─── TABLE 3: Leave Applications ───
    if (!Schema::hasTable('cims_payroll_leave_applications')) {
        DB::statement("CREATE TABLE cims_payroll_leave_applications (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            employee_id BIGINT UNSIGNED NOT NULL,
            leave_type_id BIGINT UNSIGNED NOT NULL,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            days_requested DECIMAL(8,2) NOT NULL,
            reason TEXT NULL,
            status ENUM('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
            approved_by BIGINT UNSIGNED NULL,
            approved_at TIMESTAMP NULL,
            rejection_reason TEXT NULL,
            created_by BIGINT UNSIGNED NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX idx_la_employee (employee_id),
            INDEX idx_la_status (status),
            INDEX idx_la_dates (start_date, end_date),
            CONSTRAINT fk_la_employee FOREIGN KEY (employee_id) REFERENCES cims_payroll_employees(id) ON DELETE CASCADE,
            CONSTRAINT fk_la_leave_type FOREIGN KEY (leave_type_id) REFERENCES cims_payroll_leave_types(id) ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "✓ Created cims_payroll_leave_applications\n";
    } else {
        echo "→ cims_payroll_leave_applications already exists\n";
    }

    // ─── TABLE 4: Timesheets ───
    if (!Schema::hasTable('cims_payroll_timesheets')) {
        DB::statement("CREATE TABLE cims_payroll_timesheets (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            employee_id BIGINT UNSIGNED NOT NULL,
            period_start DATE NOT NULL,
            period_end DATE NOT NULL,
            normal_hours DECIMAL(8,2) NOT NULL DEFAULT 0,
            overtime_15x_hours DECIMAL(8,2) NOT NULL DEFAULT 0 COMMENT 'OT at 1.5x',
            overtime_2x_hours DECIMAL(8,2) NOT NULL DEFAULT 0 COMMENT 'OT at 2x',
            sunday_hours DECIMAL(8,2) NOT NULL DEFAULT 0 COMMENT 'Sunday work',
            public_holiday_hours DECIMAL(8,2) NOT NULL DEFAULT 0 COMMENT 'Public holiday work',
            days_worked DECIMAL(8,2) NOT NULL DEFAULT 0,
            days_absent DECIMAL(8,2) NOT NULL DEFAULT 0,
            days_leave DECIMAL(8,2) NOT NULL DEFAULT 0,
            status ENUM('draft','submitted','approved') NOT NULL DEFAULT 'draft',
            notes TEXT NULL,
            created_by BIGINT UNSIGNED NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX idx_ts_employee (employee_id),
            INDEX idx_ts_period (period_start, period_end),
            INDEX idx_ts_status (status),
            UNIQUE KEY uk_emp_period (employee_id, period_start, period_end),
            CONSTRAINT fk_ts_employee FOREIGN KEY (employee_id) REFERENCES cims_payroll_employees(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "✓ Created cims_payroll_timesheets\n";
    } else {
        echo "→ cims_payroll_timesheets already exists\n";
    }

    // ─── SEED: Leave Types (BCEA Compliant) ───
    $leaveCount = DB::table('cims_payroll_leave_types')->count();
    if ($leaveCount == 0) {
        DB::table('cims_payroll_leave_types')->insert([
            [
                'name' => 'Annual Leave',
                'code' => 'ANNUAL',
                'days_per_year' => 15.00,
                'cycle_years' => 1,
                'is_paid' => 1,
                'is_statutory' => 1,
                'is_active' => 1,
                'description' => 'BCEA Section 20 — 21 consecutive days (15 working days) per annual leave cycle',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sick Leave',
                'code' => 'SICK',
                'days_per_year' => 30.00,
                'cycle_years' => 3,
                'is_paid' => 1,
                'is_statutory' => 1,
                'is_active' => 1,
                'description' => 'BCEA Section 22 — 30 days over a 3-year cycle. In first 6 months: 1 day per 26 days worked.',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Family Responsibility Leave',
                'code' => 'FAMILY',
                'days_per_year' => 3.00,
                'cycle_years' => 1,
                'is_paid' => 1,
                'is_statutory' => 1,
                'is_active' => 1,
                'description' => 'BCEA Section 27 — Birth of child, child illness, death of family member',
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Maternity Leave',
                'code' => 'MATERNITY',
                'days_per_year' => 120.00,
                'cycle_years' => 1,
                'is_paid' => 0,
                'is_statutory' => 1,
                'is_active' => 1,
                'description' => 'BCEA Section 25 — 4 consecutive months. Unpaid (UIF claims available).',
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Paternity Leave',
                'code' => 'PATERNITY',
                'days_per_year' => 10.00,
                'cycle_years' => 1,
                'is_paid' => 0,
                'is_statutory' => 1,
                'is_active' => 1,
                'description' => 'Labour Laws Amendment Act — 10 consecutive days. UIF claimable.',
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Study Leave',
                'code' => 'STUDY',
                'days_per_year' => 0.00,
                'cycle_years' => 1,
                'is_paid' => 1,
                'is_statutory' => 0,
                'is_active' => 1,
                'description' => 'Company discretionary — days as per company policy',
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Unpaid Leave',
                'code' => 'UNPAID',
                'days_per_year' => 0.00,
                'cycle_years' => 1,
                'is_paid' => 0,
                'is_statutory' => 0,
                'is_active' => 1,
                'description' => 'Unpaid leave as agreed between employer and employee',
                'sort_order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        echo "✓ Seeded 7 leave types (BCEA compliant)\n";
    } else {
        echo "→ Leave types already seeded ($leaveCount records)\n";
    }

    echo "\n=== PHASE 2 MIGRATION COMPLETE ===\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "File: " . $e->getFile() . "\n";
}

echo "</pre>\n";
