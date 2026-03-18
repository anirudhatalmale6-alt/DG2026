<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cims_tyredash_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->unique();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        $now = now();
        DB::table('cims_tyredash_categories')->insert([
            ['name' => 'Passenger', 'code' => 'PCR', 'description' => 'Passenger car tyres', 'sort_order' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'SUV / 4x4', 'code' => 'SUV', 'description' => 'SUV and 4x4 tyres', 'sort_order' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Light Truck', 'code' => 'LT', 'description' => 'Light truck and van tyres', 'sort_order' => 3, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Heavy Commercial', 'code' => 'TBR', 'description' => 'Truck and bus radial tyres', 'sort_order' => 4, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Agricultural', 'code' => 'AGR', 'description' => 'Farm and agricultural tyres', 'sort_order' => 5, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Industrial / Forklift', 'code' => 'IND', 'description' => 'Industrial and forklift tyres', 'sort_order' => 6, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Motorcycle', 'code' => 'MC', 'description' => 'Motorcycle tyres', 'sort_order' => 7, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_tyredash_categories');
    }
};
