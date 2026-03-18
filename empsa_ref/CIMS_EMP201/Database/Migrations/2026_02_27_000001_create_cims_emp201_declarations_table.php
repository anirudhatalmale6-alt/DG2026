<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cims_emp201_declarations', function (Blueprint $table) {
            $table->id();

            // Client reference
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('client_code', 50)->nullable();
            $table->string('company_name', 255)->nullable();
            $table->string('company_number', 100)->nullable();
            $table->string('vat_number', 50)->nullable();
            $table->string('income_tax_number', 50)->nullable();
            $table->string('paye_number', 50)->nullable();
            $table->string('sdl_number', 50)->nullable();
            $table->string('uif_number', 50)->nullable();

            // Public Officer / Contact
            $table->string('title', 20)->nullable();
            $table->string('initial', 10)->nullable();
            $table->string('first_name', 100)->nullable();
            $table->string('surname', 100)->nullable();
            $table->string('position', 100)->nullable();
            $table->string('telephone_number', 30)->nullable();
            $table->string('mobile_number', 30)->nullable();
            $table->string('whatsapp_number', 30)->nullable();
            $table->string('home_number', 30)->nullable();
            $table->string('email', 255)->nullable();

            // Period
            $table->string('pay_period', 50)->nullable();
            $table->string('financial_year', 10)->nullable();
            $table->string('period_combo', 20)->nullable();

            // Payroll Tax
            $table->decimal('paye_liability', 15, 2)->default(0);
            $table->decimal('sdl_liability', 15, 2)->default(0);
            $table->decimal('uif_liability', 15, 2)->default(0);

            // Penalties
            $table->decimal('penalty', 15, 2)->default(0);
            $table->decimal('interest', 15, 2)->default(0);
            $table->decimal('other', 15, 2)->default(0);

            // Calculated totals
            $table->decimal('tax_payable', 15, 2)->default(0);

            // Payment Reference
            $table->string('payment_reference', 100)->nullable();
            $table->string('payment_reference_number', 20)->nullable();

            // Payment
            $table->date('payment_date')->nullable();
            $table->string('payment_type', 50)->nullable();
            $table->decimal('payment_amount', 15, 2)->default(0);
            $table->decimal('balance_outstanding', 15, 2)->default(0);

            // File uploads
            $table->string('file_emp201_return', 255)->nullable();
            $table->string('file_emp201_statement', 255)->nullable();
            $table->string('file_working_papers', 255)->nullable();
            $table->string('file_emp201_pack', 255)->nullable();
            $table->string('emp_201_file', 255)->nullable();

            // Status: 1=Active, 0=Inactive
            $table->tinyInteger('status')->default(1);

            $table->timestamps();
            $table->softDeletes();

            $table->index('client_id');
            $table->index('financial_year');
            $table->index('pay_period');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_emp201_declarations');
    }
};
