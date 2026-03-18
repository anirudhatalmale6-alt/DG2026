<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cims_bank_conversions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('client_code', 50)->nullable();
            $table->string('company_name', 255)->nullable();
            $table->string('bank_type', 30); // fnb, standard, absa, nedbank, capitec
            $table->string('account_number', 50)->nullable();
            $table->string('statement_period', 100)->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('closing_balance', 15, 2)->default(0);
            $table->decimal('total_credits', 15, 2)->default(0);
            $table->decimal('total_debits', 15, 2)->default(0);
            $table->integer('credit_count')->default(0);
            $table->integer('debit_count')->default(0);
            $table->integer('transaction_count')->default(0);
            $table->string('original_filename', 255)->nullable();
            $table->string('csv_filename', 255)->nullable();
            $table->string('converted_by', 255)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('client_id');
            $table->index('bank_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_bank_conversions');
    }
};
