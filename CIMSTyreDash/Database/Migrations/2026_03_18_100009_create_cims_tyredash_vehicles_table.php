<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cims_tyredash_vehicles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('registration', 20)->nullable();
            $table->string('make', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('variant', 100)->nullable();
            $table->integer('year')->nullable();
            $table->integer('odometer_km')->nullable();
            $table->string('vin', 50)->nullable()->comment('Vehicle Identification Number');
            $table->string('colour', 50)->nullable();
            $table->string('current_tyre_size', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('cims_tyredash_customers')->onDelete('set null');
            $table->index('registration');
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_tyredash_vehicles');
    }
};
