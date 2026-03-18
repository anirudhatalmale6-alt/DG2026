<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Companies
        Schema::create('cims_payroll_companies', function (Blueprint $table) {
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

        // Employees
        Schema::create('cims_payroll_employees', function (Blueprint $table) {
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

        // Income Types
        Schema::create('cims_payroll_income_types', function (Blueprint $table) {
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

        // Deduction Types
        Schema::create('cims_payroll_deduction_types', function (Blueprint $table) {
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

        // Company Contribution Types
        Schema::create('cims_payroll_company_contribution_types', function (Blueprint $table) {
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

        // Tax Brackets
        Schema::create('cims_payroll_tax_brackets', function (Blueprint $table) {
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

        // Tax Rebates
        Schema::create('cims_payroll_tax_rebates', function (Blueprint $table) {
            $table->id();
            $table->string('tax_year', 10);
            $table->string('rebate_type', 20);
            $table->decimal('amount', 10, 2);
            $table->integer('age_threshold')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
            $table->index('tax_year');
        });

        // Tax Thresholds
        Schema::create('cims_payroll_tax_thresholds', function (Blueprint $table) {
            $table->id();
            $table->string('tax_year', 10);
            $table->string('age_group', 30);
            $table->decimal('threshold_amount', 15, 2);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
            $table->index('tax_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_payroll_tax_thresholds');
        Schema::dropIfExists('cims_payroll_tax_rebates');
        Schema::dropIfExists('cims_payroll_tax_brackets');
        Schema::dropIfExists('cims_payroll_company_contribution_types');
        Schema::dropIfExists('cims_payroll_deduction_types');
        Schema::dropIfExists('cims_payroll_income_types');
        Schema::dropIfExists('cims_payroll_employees');
        Schema::dropIfExists('cims_payroll_companies');
    }
};
