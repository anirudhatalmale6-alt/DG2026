<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cims_appointments_staff_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('service_id');
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('cims_appointments_staff')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('cims_appointments_services')->onDelete('cascade');
            $table->unique(['staff_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_appointments_staff_services');
    }
};
