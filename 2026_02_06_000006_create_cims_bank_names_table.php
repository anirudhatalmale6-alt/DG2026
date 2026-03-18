<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cims_bank_names', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name', 80);
            $table->string('branch_name', 80)->nullable();
            $table->string('branch_code', 20);
            $table->string('swift_code', 20)->nullable();
            $table->string('bank_logo')->nullable();
            $table->boolean('is_active')->default(true);
             $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_bank_names');
    }
};
