<?php
/**
 * CIMS Payroll Phase 3 Migration — Pay Runs, Loans
 * Run once via browser, then delete.
 */

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "<pre>\n";
echo "=== CIMS PAYROLL PHASE 3 MIGRATION ===\n\n";

try {
    // ─── TABLE 1: Pay Runs ───
    if (!Schema::hasTable('cims_payroll_pay_runs')) {
        DB::statement('CREATE TABLE cims_payroll_pay_runs (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            company_id BIGINT UNSIGNED NOT NULL,
            pay_period VARCHAR(10) NOT NULL,
            period_start DATE NOT NULL,
            period_end DATE NOT NULL,
            description VARCHAR(255) NULL,
            status ENUM(\'draft\',\'processed\',\'approved\',\'cancelled\') NOT NULL DEFAULT \'draft\',
            total_gross DECIMAL(14,2) NOT NULL DEFAULT 0,
            total_deductions DECIMAL(14,2) NOT NULL DEFAULT 0,
            total_employer_cost DECIMAL(14,2) NOT NULL DEFAULT 0,
            total_net_pay DECIMAL(14,2) NOT NULL DEFAULT 0,
            employee_count INT NOT NULL DEFAULT 0,
            processed_at TIMESTAMP NULL,
            approved_at TIMESTAMP NULL,
            approved_by BIGINT UNSIGNED NULL,
            created_by BIGINT UNSIGNED NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX idx_pr_company (company_id),
            INDEX idx_pr_period (pay_period),
            INDEX idx_pr_status (status),
            CONSTRAINT fk_pr_company FOREIGN KEY (company_id) REFERENCES cims_payroll_companies(id) ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        echo "Created cims_payroll_pay_runs\n";
    } else {
        echo "cims_payroll_pay_runs already exists\n";
    }

    // ─── TABLE 2: Pay Run Lines ───
    if (!Schema::hasTable('cims_payroll_pay_run_lines')) {
        DB::statement('CREATE TABLE cims_payroll_pay_run_lines (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            pay_run_id BIGINT UNSIGNED NOT NULL,
            employee_id BIGINT UNSIGNED NOT NULL,
            basic_salary DECIMAL(12,2) NOT NULL DEFAULT 0,
            hourly_rate DECIMAL(10,2) NOT NULL DEFAULT 0,
            normal_hours DECIMAL(8,2) NOT NULL DEFAULT 0,
            overtime_15x_hours DECIMAL(8,2) NOT NULL DEFAULT 0,
            overtime_2x_hours DECIMAL(8,2) NOT NULL DEFAULT 0,
            sunday_hours DECIMAL(8,2) NOT NULL DEFAULT 0,
            public_holiday_hours DECIMAL(8,2) NOT NULL DEFAULT 0,
            gross_pay DECIMAL(12,2) NOT NULL DEFAULT 0,
            total_income DECIMAL(12,2) NOT NULL DEFAULT 0,
            total_deductions DECIMAL(12,2) NOT NULL DEFAULT 0,
            total_employer_contributions DECIMAL(12,2) NOT NULL DEFAULT 0,
            net_pay DECIMAL(12,2) NOT NULL DEFAULT 0,
            paye_tax DECIMAL(12,2) NOT NULL DEFAULT 0,
            uif_employee DECIMAL(12,2) NOT NULL DEFAULT 0,
            uif_employer DECIMAL(12,2) NOT NULL DEFAULT 0,
            sdl_employer DECIMAL(12,2) NOT NULL DEFAULT 0,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX idx_prl_payrun (pay_run_id),
            INDEX idx_prl_employee (employee_id),
            UNIQUE KEY uk_payrun_emp (pay_run_id, employee_id),
            CONSTRAINT fk_prl_payrun FOREIGN KEY (pay_run_id) REFERENCES cims_payroll_pay_runs(id) ON DELETE CASCADE,
            CONSTRAINT fk_prl_employee FOREIGN KEY (employee_id) REFERENCES cims_payroll_employees(id) ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        echo "Created cims_payroll_pay_run_lines\n";
    } else {
        echo "cims_payroll_pay_run_lines already exists\n";
    }

    // ─── TABLE 3: Pay Run Line Items ───
    if (!Schema::hasTable('cims_payroll_pay_run_line_items')) {
        DB::statement('CREATE TABLE cims_payroll_pay_run_line_items (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            pay_run_line_id BIGINT UNSIGNED NOT NULL,
            item_type ENUM(\'income\',\'deduction\',\'employer_contribution\') NOT NULL,
            type_id BIGINT UNSIGNED NULL,
            name VARCHAR(100) NOT NULL,
            calc_type ENUM(\'fixed\',\'percentage\',\'hours\',\'auto\') NOT NULL DEFAULT \'fixed\',
            rate DECIMAL(10,4) NOT NULL DEFAULT 0,
            hours DECIMAL(8,2) NOT NULL DEFAULT 0,
            amount DECIMAL(12,2) NOT NULL DEFAULT 0,
            is_taxable TINYINT(1) NOT NULL DEFAULT 1,
            sort_order INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX idx_prli_line (pay_run_line_id),
            CONSTRAINT fk_prli_line FOREIGN KEY (pay_run_line_id) REFERENCES cims_payroll_pay_run_lines(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        echo "Created cims_payroll_pay_run_line_items\n";
    } else {
        echo "cims_payroll_pay_run_line_items already exists\n";
    }

    // ─── TABLE 4: Loans Register ───
    if (!Schema::hasTable('cims_payroll_loans')) {
        DB::statement('CREATE TABLE cims_payroll_loans (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            employee_id BIGINT UNSIGNED NOT NULL,
            loan_type VARCHAR(100) NOT NULL DEFAULT \'General Loan\',
            loan_amount DECIMAL(12,2) NOT NULL,
            outstanding_balance DECIMAL(12,2) NOT NULL,
            monthly_repayment DECIMAL(12,2) NOT NULL DEFAULT 0,
            start_date DATE NOT NULL,
            end_date DATE NULL,
            status ENUM(\'active\',\'paid_off\',\'written_off\',\'suspended\') NOT NULL DEFAULT \'active\',
            notes TEXT NULL,
            created_by BIGINT UNSIGNED NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX idx_loan_emp (employee_id),
            INDEX idx_loan_status (status),
            CONSTRAINT fk_loan_employee FOREIGN KEY (employee_id) REFERENCES cims_payroll_employees(id) ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        echo "Created cims_payroll_loans\n";
    } else {
        echo "cims_payroll_loans already exists\n";
    }

    // ─── TABLE 5: Loan Repayments ───
    if (!Schema::hasTable('cims_payroll_loan_repayments')) {
        DB::statement('CREATE TABLE cims_payroll_loan_repayments (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            loan_id BIGINT UNSIGNED NOT NULL,
            pay_run_id BIGINT UNSIGNED NULL,
            amount DECIMAL(12,2) NOT NULL,
            repayment_date DATE NOT NULL,
            notes VARCHAR(255) NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX idx_rep_loan (loan_id),
            INDEX idx_rep_payrun (pay_run_id),
            CONSTRAINT fk_rep_loan FOREIGN KEY (loan_id) REFERENCES cims_payroll_loans(id) ON DELETE CASCADE,
            CONSTRAINT fk_rep_payrun FOREIGN KEY (pay_run_id) REFERENCES cims_payroll_pay_runs(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        echo "Created cims_payroll_loan_repayments\n";
    } else {
        echo "cims_payroll_loan_repayments already exists\n";
    }

    echo "\n=== PHASE 3 MIGRATION COMPLETE ===\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "</pre>\n";
