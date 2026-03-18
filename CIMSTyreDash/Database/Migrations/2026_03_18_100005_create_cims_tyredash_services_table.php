<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cims_tyredash_services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('code', 20)->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2)->default(0)->comment('Default price incl VAT');
            $table->boolean('price_per_tyre')->default(false)->comment('If true, multiply by qty of tyres');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Seed standard tyre shop services (matching StockFinder pricing)
        $now = now();
        DB::table('cims_tyredash_services')->insert([
            ['name' => 'Wheel Alignment Front & Rear', 'code' => 'WAFR', 'description' => 'Full 4-wheel alignment check and adjust', 'price' => 300.00, 'price_per_tyre' => false, 'sort_order' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Wheel Alignment Front Only', 'code' => 'WAF', 'description' => '2-wheel front alignment', 'price' => 200.00, 'price_per_tyre' => false, 'sort_order' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Wheel Balancing Mag', 'code' => 'WBMAG', 'description' => 'Wheel balancing for mag/alloy wheels', 'price' => 45.00, 'price_per_tyre' => true, 'sort_order' => 3, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Wheel Balancing Steel', 'code' => 'WBSTL', 'description' => 'Wheel balancing for steel wheels', 'price' => 35.00, 'price_per_tyre' => true, 'sort_order' => 4, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'New Valve, Fit & Strip', 'code' => 'VB', 'description' => 'New valve, strip old tyre, fit new tyre', 'price' => 17.00, 'price_per_tyre' => true, 'sort_order' => 5, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Nitrogen', 'code' => 'NIT', 'description' => 'Nitrogen fill per tyre', 'price' => 20.00, 'price_per_tyre' => true, 'sort_order' => 6, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Puncture Repair', 'code' => 'PNCT', 'description' => 'Puncture repair (tubeless)', 'price' => 120.00, 'price_per_tyre' => false, 'sort_order' => 7, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'TPMS Sensor Reset', 'code' => 'TPMS', 'description' => 'Tyre Pressure Monitoring System reset', 'price' => 150.00, 'price_per_tyre' => false, 'sort_order' => 8, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Tyre Disposal', 'code' => 'DISP', 'description' => 'Old tyre disposal fee', 'price' => 25.00, 'price_per_tyre' => true, 'sort_order' => 9, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_tyredash_services');
    }
};
