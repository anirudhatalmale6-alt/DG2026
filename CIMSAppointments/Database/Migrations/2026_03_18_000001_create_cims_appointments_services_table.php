<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cims_appointments_services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->integer('default_duration_minutes')->default(60);
            $table->integer('min_duration_minutes')->default(60);
            $table->integer('max_duration_minutes')->default(240);
            $table->boolean('is_chargeable')->default(false);
            $table->decimal('price_per_hour', 15, 2)->default(0);
            $table->string('color', 20)->nullable()->comment('Hex color for calendar display');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_appointments_services');
    }
};
