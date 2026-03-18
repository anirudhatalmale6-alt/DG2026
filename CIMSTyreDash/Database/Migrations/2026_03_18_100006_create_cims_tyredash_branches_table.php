<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cims_tyredash_branches', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('code', 20)->unique();
            $table->string('address', 500)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('manager_name', 150)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Seed sample branches (Apex Tyres KZN based)
        $now = now();
        DB::table('cims_tyredash_branches')->insert([
            ['name' => 'Head Office', 'code' => 'HQ', 'address' => '123 Main Road', 'city' => 'Durban', 'province' => 'KwaZulu-Natal', 'phone' => '031 555 0001', 'email' => 'ho@example.co.za', 'manager_name' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Durban Central', 'code' => 'DBN', 'address' => '456 Smith Street', 'city' => 'Durban', 'province' => 'KwaZulu-Natal', 'phone' => '031 555 0002', 'email' => 'durban@example.co.za', 'manager_name' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Pinetown', 'code' => 'PTWN', 'address' => '78 Old Main Road', 'city' => 'Pinetown', 'province' => 'KwaZulu-Natal', 'phone' => '031 555 0003', 'email' => 'pinetown@example.co.za', 'manager_name' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Waterfall', 'code' => 'WFALL', 'address' => '12 Waterfall Drive', 'city' => 'Waterfall', 'province' => 'KwaZulu-Natal', 'phone' => '031 555 0004', 'email' => 'waterfall@example.co.za', 'manager_name' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Umhlanga', 'code' => 'UMHL', 'address' => '34 Umhlanga Rocks Drive', 'city' => 'Umhlanga', 'province' => 'KwaZulu-Natal', 'phone' => '031 555 0005', 'email' => 'umhlanga@example.co.za', 'manager_name' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_tyredash_branches');
    }
};
