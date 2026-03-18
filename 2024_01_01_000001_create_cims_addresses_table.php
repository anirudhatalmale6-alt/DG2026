<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cims_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('unit_number', 50)->nullable();
            $table->string('complex_name', 150)->nullable();
            $table->string('street_number', 50)->nullable();
            $table->string('street_name', 150)->nullable();
            $table->string('suburb', 150)->nullable();
            $table->string('city', 150)->nullable();
            $table->string('postal_code', 50)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('municipality', 150)->nullable();
            $table->string('ward', 100)->nullable();
            $table->string('country', 100)->default('South Africa');
            $table->string('long_address', 255);
            $table->text('google_address')->nullable();
            $table->string('latitude', 50)->nullable();
            $table->string('longitude', 50)->nullable();
            $table->text('map_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_addresses');
    }
};
