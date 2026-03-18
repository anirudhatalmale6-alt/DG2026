<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_master_banks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('bank_id')->comment('FK to cims_bank_names.id');
            $table->string('bank_name', 255)->nullable();
            $table->string('bank_account_holder', 255)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->unsignedBigInteger('bank_account_type_id')->nullable()->comment('FK to ref_bank_account_types.id');
            $table->string('bank_account_type_name', 255)->nullable();
            $table->string('account_status', 50)->nullable();
            $table->string('bank_branch_name', 255)->nullable();
            $table->string('bank_branch_code', 50)->nullable();
            $table->string('bank_swift_code', 50)->nullable();
            $table->date('bank_account_date_opened')->nullable();
            $table->boolean('confirmation_of_banking_uploaded')->default(0);
            $table->boolean('is_active')->default(1);
            $table->boolean('is_checked')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('client_id')
                ->references('client_id')
                ->on('client_master')
                ->onDelete('cascade');

            $table->foreign('bank_id')
                ->references('id')
                ->on('cims_bank_names')
                ->onDelete('restrict');

            $table->foreign('bank_account_type_id')
                ->references('id')
                ->on('ref_bank_account_types')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_master_banks');
    }
};
