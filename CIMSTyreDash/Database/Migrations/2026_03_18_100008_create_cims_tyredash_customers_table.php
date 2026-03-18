<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cims_tyredash_customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_master_id')->nullable()->comment('Link to CIMS client_master');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('company_name', 200)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('cell', 30)->nullable();
            $table->string('vat_number', 50)->nullable();
            $table->string('debtor_account', 50)->nullable();
            $table->string('customer_type', 20)->default('retail')->comment('retail, fleet, corporate, government, insurance');
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('client_master_id');
            $table->index('phone');
            $table->index('cell');
            $table->index('email');
            $table->index('debtor_account');
            $table->index('customer_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_tyredash_customers');
    }
};
