<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cims_appointments_availability', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->tinyInteger('day_of_week')->comment('0=Monday, 1=Tuesday, 2=Wednesday, 3=Thursday, 4=Friday, 5=Saturday');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('cims_appointments_staff')->onDelete('cascade');
            $table->index(['staff_id', 'day_of_week']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_appointments_availability');
    }
};
