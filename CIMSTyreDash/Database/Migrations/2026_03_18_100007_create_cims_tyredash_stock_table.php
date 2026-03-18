<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cims_tyredash_stock', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('branch_id');
            $table->integer('quantity')->default(0);
            $table->integer('min_quantity')->default(4)->comment('Minimum stock alert threshold');
            $table->integer('reserved')->default(0)->comment('Quantity reserved for pending quotes');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('cims_tyredash_products')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('cims_tyredash_branches')->onDelete('cascade');
            $table->unique(['product_id', 'branch_id']);
            $table->index('quantity');
        });

        // Seed sample stock (random quantities across branches for seeded products)
        $now = now();
        $products = DB::table('cims_tyredash_products')->pluck('id')->toArray();
        $branches = DB::table('cims_tyredash_branches')->pluck('id')->toArray();

        foreach ($products as $productId) {
            foreach ($branches as $branchId) {
                DB::table('cims_tyredash_stock')->insert([
                    'product_id' => $productId,
                    'branch_id' => $branchId,
                    'quantity' => rand(0, 40),
                    'min_quantity' => 4,
                    'reserved' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_tyredash_stock');
    }
};
