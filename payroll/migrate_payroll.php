<?php
/**
 * One-time migration runner for CIMS_PAYROLL module.
 * Access via browser: https://smartweigh.co.za/migrate_payroll.php
 * DELETE THIS FILE AFTER RUNNING.
 */

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "<pre style='font-family:monospace;font-size:14px;'>\n";
echo "=== CIMS PAYROLL Migration ===\n\n";

try {
    // ─── Companies ───
    if (!Schema::hasTable('cims_payroll_companies')) {
        Schema::create('cims_payroll_companies', function ($table) {
            $table->id();
            $table->string('company_name', 255);
            $table->string('registration_number', 50)->nullable();
            $table->string('trading_name', 255)->nullable();
            $table->string('address_line1', 255)->nullable();
            $table->string('address_line2', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('paye_reference', 50)->nullable();
            $table->string('uif_reference', 50)->nullable();
            $table->string('sdl_reference', 50)->nullable();
            $table->string('pay_frequency', 20)->default('monthly');
            $table->decimal('normal_hours_month', 5, 2)->default(195.00);
            $table->decimal('normal_days_month', 5, 2)->default(21.67);
            $table->decimal('normal_hours_day', 5, 2)->default(9.00);
            $table->tinyInteger('is_active')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
        echo "[OK] Created: cims_payroll_companies\n";
    } else {
        echo "[SKIP] Already exists: cims_payroll_companies\n";
    }

    // ─── Employees ───
    if (!Schema::hasTable('cims_payroll_employees')) {
        Schema::create('cims_payroll_employees', function ($table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('employee_number', 20);
            $table->string('id_number', 13)->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->text('address')->nullable();
            $table->string('job_title', 100)->nullable();
            $table->string('department', 100)->nullable();
            $table->date('start_date');
            $table->date('termination_date')->nullable();
            $table->string('termination_reason', 255)->nullable();
            $table->string('tax_number', 20)->nullable();
            $table->string('tax_status', 20)->default('normal');
            $table->string('pay_type', 10)->default('salaried');
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->decimal('hourly_rate', 10, 2)->default(0);
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_branch_code', 10)->nullable();
            $table->string('bank_account_number', 20)->nullable();
            $table->string('bank_account_type', 20)->nullable();
            $table->string('status', 20)->default('active');
            $table->tinyInteger('is_active')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->index('company_id');
            $table->index('status');
            $table->index('employee_number');
        });
        echo "[OK] Created: cims_payroll_employees\n";
    } else {
        echo "[SKIP] Already exists: cims_payroll_employees\n";
    }

    // ─── Income Types ───
    if (!Schema::hasTable('cims_payroll_income_types')) {
        Schema::create('cims_payroll_income_types', function ($table) {
            $table->id();
            $table->string('name', 100);
            $table->string('sars_code', 10)->nullable();
            $table->tinyInteger('is_taxable')->default(1);
            $table->tinyInteger('is_uif_applicable')->default(1);
            $table->text('description')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
        echo "[OK] Created: cims_payroll_income_types\n";

        // Seed default income types
        DB::table('cims_payroll_income_types')->insert([
            ['name' => 'Basic Salary', 'sars_code' => '3601', 'is_taxable' => 1, 'is_uif_applicable' => 1, 'description' => 'Basic monthly salary or hourly wages', 'is_active' => 1, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Overtime', 'sars_code' => '3601', 'is_taxable' => 1, 'is_uif_applicable' => 1, 'description' => 'Overtime pay (1.5x weekday, 2x Sunday/PH)', 'is_active' => 1, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Commission', 'sars_code' => '3606', 'is_taxable' => 1, 'is_uif_applicable' => 1, 'description' => 'Sales commission', 'is_active' => 1, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bonus / 13th Cheque', 'sars_code' => '3605', 'is_taxable' => 1, 'is_uif_applicable' => 0, 'description' => 'Annual bonus or 13th cheque', 'is_active' => 1, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Travel Allowance', 'sars_code' => '3701', 'is_taxable' => 1, 'is_uif_applicable' => 0, 'description' => 'Travel/transport allowance', 'is_active' => 1, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cell Phone Allowance', 'sars_code' => '3713', 'is_taxable' => 1, 'is_uif_applicable' => 0, 'description' => 'Cell phone/communication allowance', 'is_active' => 1, 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Housing Allowance', 'sars_code' => '3707', 'is_taxable' => 1, 'is_uif_applicable' => 0, 'description' => 'Housing/accommodation allowance', 'is_active' => 1, 'sort_order' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Acting Allowance', 'sars_code' => '3601', 'is_taxable' => 1, 'is_uif_applicable' => 1, 'description' => 'Allowance for acting in higher position', 'is_active' => 1, 'sort_order' => 8, 'created_at' => now(), 'updated_at' => now()],
        ]);
        echo "[OK] Seeded: 8 default income types\n";
    } else {
        echo "[SKIP] Already exists: cims_payroll_income_types\n";
    }

    // ─── Deduction Types ───
    if (!Schema::hasTable('cims_payroll_deduction_types')) {
        Schema::create('cims_payroll_deduction_types', function ($table) {
            $table->id();
            $table->string('name', 100);
            $table->string('sars_code', 10)->nullable();
            $table->string('calc_type', 20)->default('fixed');
            $table->decimal('default_value', 10, 2)->default(0);
            $table->tinyInteger('is_statutory')->default(0);
            $table->tinyInteger('is_auto_calculated')->default(0);
            $table->text('description')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
        echo "[OK] Created: cims_payroll_deduction_types\n";

        // Seed default deduction types
        DB::table('cims_payroll_deduction_types')->insert([
            ['name' => 'PAYE Income Tax', 'sars_code' => '4101', 'calc_type' => 'percentage', 'default_value' => 0, 'is_statutory' => 1, 'is_auto_calculated' => 1, 'description' => 'Pay As You Earn — auto-calculated from SARS tax tables', 'is_active' => 1, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'UIF Employee', 'sars_code' => '4141', 'calc_type' => 'percentage', 'default_value' => 1.00, 'is_statutory' => 1, 'is_auto_calculated' => 1, 'description' => 'Unemployment Insurance Fund — 1% of gross (capped)', 'is_active' => 1, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Provident Fund Employee', 'sars_code' => '4001', 'calc_type' => 'percentage', 'default_value' => 0, 'is_statutory' => 0, 'is_auto_calculated' => 0, 'description' => 'Employee provident fund contribution', 'is_active' => 1, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pension Fund Employee', 'sars_code' => '4001', 'calc_type' => 'percentage', 'default_value' => 0, 'is_statutory' => 0, 'is_auto_calculated' => 0, 'description' => 'Employee pension fund contribution', 'is_active' => 1, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Medical Aid Employee', 'sars_code' => '4005', 'calc_type' => 'fixed', 'default_value' => 0, 'is_statutory' => 0, 'is_auto_calculated' => 0, 'description' => 'Employee medical aid contribution', 'is_active' => 1, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Loan Repayment', 'sars_code' => null, 'calc_type' => 'fixed', 'default_value' => 0, 'is_statutory' => 0, 'is_auto_calculated' => 0, 'description' => 'Monthly loan repayment deduction', 'is_active' => 1, 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Garnishee Order', 'sars_code' => null, 'calc_type' => 'fixed', 'default_value' => 0, 'is_statutory' => 0, 'is_auto_calculated' => 0, 'description' => 'Court-ordered garnishee deduction', 'is_active' => 1, 'sort_order' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Union Fees', 'sars_code' => null, 'calc_type' => 'fixed', 'default_value' => 0, 'is_statutory' => 0, 'is_auto_calculated' => 0, 'description' => 'Trade union membership fees', 'is_active' => 1, 'sort_order' => 8, 'created_at' => now(), 'updated_at' => now()],
        ]);
        echo "[OK] Seeded: 8 default deduction types\n";
    } else {
        echo "[SKIP] Already exists: cims_payroll_deduction_types\n";
    }

    // ─── Company Contribution Types ───
    if (!Schema::hasTable('cims_payroll_company_contribution_types')) {
        Schema::create('cims_payroll_company_contribution_types', function ($table) {
            $table->id();
            $table->string('name', 100);
            $table->string('sars_code', 10)->nullable();
            $table->string('calc_type', 20)->default('percentage');
            $table->decimal('default_value', 10, 2)->default(0);
            $table->unsignedBigInteger('linked_deduction_id')->nullable();
            $table->tinyInteger('is_statutory')->default(0);
            $table->tinyInteger('is_auto_calculated')->default(0);
            $table->text('description')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
        echo "[OK] Created: cims_payroll_company_contribution_types\n";

        // Seed default company contribution types
        DB::table('cims_payroll_company_contribution_types')->insert([
            ['name' => 'UIF Employer', 'sars_code' => '4141', 'calc_type' => 'percentage', 'default_value' => 1.00, 'linked_deduction_id' => null, 'is_statutory' => 1, 'is_auto_calculated' => 1, 'description' => 'Employer UIF — 1% matching employee contribution', 'is_active' => 1, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'SDL', 'sars_code' => '4142', 'calc_type' => 'percentage', 'default_value' => 1.00, 'linked_deduction_id' => null, 'is_statutory' => 1, 'is_auto_calculated' => 1, 'description' => 'Skills Development Levy — 1% of gross payroll', 'is_active' => 1, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Provident Fund Employer', 'sars_code' => '4001', 'calc_type' => 'percentage', 'default_value' => 0, 'linked_deduction_id' => null, 'is_statutory' => 0, 'is_auto_calculated' => 0, 'description' => 'Employer provident fund contribution', 'is_active' => 1, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pension Fund Employer', 'sars_code' => '4001', 'calc_type' => 'percentage', 'default_value' => 0, 'linked_deduction_id' => null, 'is_statutory' => 0, 'is_auto_calculated' => 0, 'description' => 'Employer pension fund contribution', 'is_active' => 1, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Medical Aid Employer', 'sars_code' => '4005', 'calc_type' => 'fixed', 'default_value' => 0, 'linked_deduction_id' => null, 'is_statutory' => 0, 'is_auto_calculated' => 0, 'description' => 'Employer medical aid contribution', 'is_active' => 1, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);
        echo "[OK] Seeded: 5 default company contribution types\n";
    } else {
        echo "[SKIP] Already exists: cims_payroll_company_contribution_types\n";
    }

    // ─── Tax Brackets (2025/2026 tax year) ───
    if (!Schema::hasTable('cims_payroll_tax_brackets')) {
        Schema::create('cims_payroll_tax_brackets', function ($table) {
            $table->id();
            $table->string('tax_year', 10);
            $table->decimal('min_amount', 15, 2);
            $table->decimal('max_amount', 15, 2);
            $table->decimal('rate', 5, 2);
            $table->decimal('base_tax', 15, 2)->default(0);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
            $table->index('tax_year');
        });
        echo "[OK] Created: cims_payroll_tax_brackets\n";

        // SARS 2025/2026 tax brackets
        DB::table('cims_payroll_tax_brackets')->insert([
            ['tax_year' => '2026', 'min_amount' => 1, 'max_amount' => 237100, 'rate' => 18, 'base_tax' => 0, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tax_year' => '2026', 'min_amount' => 237101, 'max_amount' => 370500, 'rate' => 26, 'base_tax' => 42678, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tax_year' => '2026', 'min_amount' => 370501, 'max_amount' => 512800, 'rate' => 31, 'base_tax' => 77362, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tax_year' => '2026', 'min_amount' => 512801, 'max_amount' => 673000, 'rate' => 36, 'base_tax' => 121475, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tax_year' => '2026', 'min_amount' => 673001, 'max_amount' => 857900, 'rate' => 39, 'base_tax' => 179147, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tax_year' => '2026', 'min_amount' => 857901, 'max_amount' => 1817000, 'rate' => 41, 'base_tax' => 251258, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tax_year' => '2026', 'min_amount' => 1817001, 'max_amount' => 99999999, 'rate' => 45, 'base_tax' => 644489, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
        echo "[OK] Seeded: 7 tax brackets (2025/2026)\n";
    } else {
        echo "[SKIP] Already exists: cims_payroll_tax_brackets\n";
    }

    // ─── Tax Rebates ───
    if (!Schema::hasTable('cims_payroll_tax_rebates')) {
        Schema::create('cims_payroll_tax_rebates', function ($table) {
            $table->id();
            $table->string('tax_year', 10);
            $table->string('rebate_type', 20);
            $table->decimal('amount', 10, 2);
            $table->integer('age_threshold')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
            $table->index('tax_year');
        });
        echo "[OK] Created: cims_payroll_tax_rebates\n";

        // SARS 2025/2026 rebates
        DB::table('cims_payroll_tax_rebates')->insert([
            ['tax_year' => '2026', 'rebate_type' => 'primary', 'amount' => 17235, 'age_threshold' => null, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tax_year' => '2026', 'rebate_type' => 'secondary', 'amount' => 9444, 'age_threshold' => 65, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tax_year' => '2026', 'rebate_type' => 'tertiary', 'amount' => 3145, 'age_threshold' => 75, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
        echo "[OK] Seeded: 3 tax rebates (2025/2026)\n";
    } else {
        echo "[SKIP] Already exists: cims_payroll_tax_rebates\n";
    }

    // ─── Tax Thresholds ───
    if (!Schema::hasTable('cims_payroll_tax_thresholds')) {
        Schema::create('cims_payroll_tax_thresholds', function ($table) {
            $table->id();
            $table->string('tax_year', 10);
            $table->string('age_group', 30);
            $table->decimal('threshold_amount', 15, 2);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
            $table->index('tax_year');
        });
        echo "[OK] Created: cims_payroll_tax_thresholds\n";

        // SARS 2025/2026 thresholds
        DB::table('cims_payroll_tax_thresholds')->insert([
            ['tax_year' => '2026', 'age_group' => 'below_65', 'threshold_amount' => 95750, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tax_year' => '2026', 'age_group' => '65_to_74', 'threshold_amount' => 148217, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tax_year' => '2026', 'age_group' => '75_and_over', 'threshold_amount' => 165689, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
        echo "[OK] Seeded: 3 tax thresholds (2025/2026)\n";
    } else {
        echo "[SKIP] Already exists: cims_payroll_tax_thresholds\n";
    }

    // Clear caches
    if (function_exists('opcache_reset')) { opcache_reset(); }
    echo "\n[OK] All CIMS Payroll tables created successfully!\n";
    echo "[INFO] You can now access: /cims/payroll\n";
    echo "\n*** DELETE THIS FILE NOW ***\n";

} catch (\Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    echo "[TRACE] " . $e->getTraceAsString() . "\n";
}

echo "</pre>";
