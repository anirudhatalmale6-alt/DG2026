<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cims_appointments_blocked_dates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id')->nullable()->comment('Null = applies to all staff');
            $table->date('blocked_date');
            $table->string('reason', 255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('cims_appointments_staff')->onDelete('cascade');
            $table->index('blocked_date');
            $table->index('staff_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_appointments_blocked_dates');
    }
};
