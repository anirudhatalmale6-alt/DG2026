<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cims_tyredash_brands', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->unique();
            $table->string('logo_url', 500)->nullable();
            $table->string('country', 100)->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
            $table->index('code');
        });

        // Seed popular South African tyre brands
        $now = now();
        DB::table('cims_tyredash_brands')->insert([
            ['name' => 'Bridgestone', 'code' => 'BSTONE', 'country' => 'Japan', 'logo_url' => null, 'description' => 'Premium Japanese tyre manufacturer', 'sort_order' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Continental', 'code' => 'CONTI', 'country' => 'Germany', 'logo_url' => null, 'description' => 'German premium tyre brand', 'sort_order' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Dunlop', 'code' => 'DUNLOP', 'country' => 'United Kingdom', 'logo_url' => null, 'description' => 'Popular in South Africa, owned by Sumitomo', 'sort_order' => 3, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Goodyear', 'code' => 'GDYR', 'country' => 'USA', 'logo_url' => null, 'description' => 'American tyre manufacturer', 'sort_order' => 4, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Michelin', 'code' => 'MICH', 'country' => 'France', 'logo_url' => null, 'description' => 'French premium tyre brand', 'sort_order' => 5, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Pirelli', 'code' => 'PIREL', 'country' => 'Italy', 'logo_url' => null, 'description' => 'Italian premium tyre brand', 'sort_order' => 6, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Hankook', 'code' => 'HANK', 'country' => 'South Korea', 'logo_url' => null, 'description' => 'Korean mid-range tyre brand', 'sort_order' => 7, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Yokohama', 'code' => 'YOKO', 'country' => 'Japan', 'logo_url' => null, 'description' => 'Japanese tyre manufacturer', 'sort_order' => 8, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Sailun', 'code' => 'SAILUN', 'country' => 'China', 'logo_url' => null, 'description' => 'Budget-friendly Chinese brand', 'sort_order' => 9, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Royal Black', 'code' => 'RBLACK', 'country' => 'China', 'logo_url' => null, 'description' => 'Economy tyre brand', 'sort_order' => 10, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Sumitomo', 'code' => 'SUMI', 'country' => 'Japan', 'logo_url' => null, 'description' => 'Japanese tyre manufacturer', 'sort_order' => 11, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Firestone', 'code' => 'FSTONE', 'country' => 'USA', 'logo_url' => null, 'description' => 'Bridgestone subsidiary, popular commercial brand', 'sort_order' => 12, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Toyo', 'code' => 'TOYO', 'country' => 'Japan', 'logo_url' => null, 'description' => 'Japanese tyre brand', 'sort_order' => 13, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Maxxis', 'code' => 'MAXXIS', 'country' => 'Taiwan', 'logo_url' => null, 'description' => 'Taiwanese mid-range brand', 'sort_order' => 14, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'BFGoodrich', 'code' => 'BFG', 'country' => 'USA', 'logo_url' => null, 'description' => 'Michelin subsidiary, popular for off-road', 'sort_order' => 15, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_tyredash_brands');
    }
};
