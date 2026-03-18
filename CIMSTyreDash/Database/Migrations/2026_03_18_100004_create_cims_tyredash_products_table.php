<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cims_tyredash_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('size_id');
            $table->string('model_name', 200)->comment('e.g. Primacy 5, ContiVanContact 100');
            $table->string('product_code', 50)->unique()->comment('Stock/supplier code');
            $table->string('full_description', 500)->nullable()->comment('Full tyre description');
            $table->string('load_index', 20)->nullable()->comment('e.g. 95, 107/105');
            $table->string('speed_rating', 5)->nullable()->comment('e.g. H, V, W, T');
            $table->string('pattern_type', 50)->nullable()->comment('e.g. Highway, All-Terrain, Mud-Terrain');
            $table->decimal('cost_price', 15, 2)->default(0)->comment('Excl VAT cost from supplier');
            $table->decimal('sell_price', 15, 2)->default(0)->comment('Incl VAT recommended sell price');
            $table->decimal('markup_pct', 5, 2)->default(20.00)->comment('Default markup percentage');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('brand_id')->references('id')->on('cims_tyredash_brands')->onDelete('restrict');
            $table->foreign('category_id')->references('id')->on('cims_tyredash_categories')->onDelete('restrict');
            $table->foreign('size_id')->references('id')->on('cims_tyredash_sizes')->onDelete('restrict');

            $table->index('brand_id');
            $table->index('category_id');
            $table->index('size_id');
            $table->index('is_active');
            $table->index('product_code');
        });

        // Seed sample products for 205/65R16 (the size shown in StockFinder screenshots)
        $now = now();

        // Get IDs for the size and brands
        $sizeId = DB::table('cims_tyredash_sizes')->where('full_size', '205/65R16')->value('id');
        $sizeCId = DB::table('cims_tyredash_sizes')->where('full_size', '205/65R16C')->value('id');
        $passengerCatId = DB::table('cims_tyredash_categories')->where('code', 'PCR')->value('id');
        $ltCatId = DB::table('cims_tyredash_categories')->where('code', 'LT')->value('id');

        $contiId = DB::table('cims_tyredash_brands')->where('code', 'CONTI')->value('id');
        $gdyrId = DB::table('cims_tyredash_brands')->where('code', 'GDYR')->value('id');
        $michId = DB::table('cims_tyredash_brands')->where('code', 'MICH')->value('id');
        $sailunId = DB::table('cims_tyredash_brands')->where('code', 'SAILUN')->value('id');
        $rblackId = DB::table('cims_tyredash_brands')->where('code', 'RBLACK')->value('id');
        $bstoneId = DB::table('cims_tyredash_brands')->where('code', 'BSTONE')->value('id');
        $dunlopId = DB::table('cims_tyredash_brands')->where('code', 'DUNLOP')->value('id');
        $hankId = DB::table('cims_tyredash_brands')->where('code', 'HANK')->value('id');

        if ($sizeId && $passengerCatId) {
            $products = [
                // 205/65R16 products (matching StockFinder screenshots)
                ['brand_id' => $contiId, 'category_id' => $ltCatId ?: $passengerCatId, 'size_id' => $sizeCId ?: $sizeId, 'model_name' => 'ContiVanContact 100', 'product_code' => 'D451287', 'full_description' => '205/65R16C 107/105T ContiVanContact 100 R', 'load_index' => '107/105', 'speed_rating' => 'T', 'pattern_type' => 'Highway', 'cost_price' => 2399.00, 'sell_price' => 2878.00, 'markup_pct' => 20.00],
                ['brand_id' => $gdyrId, 'category_id' => $passengerCatId, 'size_id' => $sizeId, 'model_name' => 'Assurance Triplemax 2', 'product_code' => 'S85062', 'full_description' => '205/65R16 95H Assurance Triplemax 2', 'load_index' => '95', 'speed_rating' => 'H', 'pattern_type' => 'Highway', 'cost_price' => 2466.00, 'sell_price' => 2960.00, 'markup_pct' => 20.00],
                ['brand_id' => $michId, 'category_id' => $passengerCatId, 'size_id' => $sizeId, 'model_name' => 'Primacy 5', 'product_code' => '075935', 'full_description' => '205/65 R16 95W TL Primacy 5 MI', 'load_index' => '95', 'speed_rating' => 'W', 'pattern_type' => 'Highway', 'cost_price' => 2409.00, 'sell_price' => 2891.00, 'markup_pct' => 20.00],
                ['brand_id' => $sailunId, 'category_id' => $passengerCatId, 'size_id' => $sizeId, 'model_name' => 'SH406 BHSL', 'product_code' => '3220005335', 'full_description' => '205/65R16 95H SH406 BHSL', 'load_index' => '95', 'speed_rating' => 'H', 'pattern_type' => 'Highway', 'cost_price' => 1228.00, 'sell_price' => 1473.00, 'markup_pct' => 20.00],
                ['brand_id' => $rblackId, 'category_id' => $passengerCatId, 'size_id' => $sizeId, 'model_name' => 'Royal Comfort', 'product_code' => 'RK888H1', 'full_description' => '205/65R16 Royal Comfort 95H', 'load_index' => '95', 'speed_rating' => 'H', 'pattern_type' => 'Highway', 'cost_price' => 989.00, 'sell_price' => 1187.00, 'markup_pct' => 20.00],

                // More 205/65R16 products
                ['brand_id' => $bstoneId, 'category_id' => $passengerCatId, 'size_id' => $sizeId, 'model_name' => 'Turanza T005', 'product_code' => 'BS20565T005', 'full_description' => '205/65R16 95W Turanza T005', 'load_index' => '95', 'speed_rating' => 'W', 'pattern_type' => 'Highway', 'cost_price' => 2150.00, 'sell_price' => 2580.00, 'markup_pct' => 20.00],
                ['brand_id' => $dunlopId, 'category_id' => $passengerCatId, 'size_id' => $sizeId, 'model_name' => 'SP Sport LM705', 'product_code' => 'DL20565LM705', 'full_description' => '205/65R16 95H SP Sport LM705', 'load_index' => '95', 'speed_rating' => 'H', 'pattern_type' => 'Highway', 'cost_price' => 1850.00, 'sell_price' => 2220.00, 'markup_pct' => 20.00],
                ['brand_id' => $hankId, 'category_id' => $passengerCatId, 'size_id' => $sizeId, 'model_name' => 'Ventus Prime 4', 'product_code' => 'HK20565VP4', 'full_description' => '205/65R16 95H Ventus Prime 4 K135', 'load_index' => '95', 'speed_rating' => 'H', 'pattern_type' => 'Highway', 'cost_price' => 1650.00, 'sell_price' => 1980.00, 'markup_pct' => 20.00],
            ];

            foreach ($products as $p) {
                $p['is_active'] = true;
                $p['created_at'] = $now;
                $p['updated_at'] = $now;
                $p['deleted_at'] = null;
                $p['created_by'] = null;
                $p['updated_by'] = null;
                DB::table('cims_tyredash_products')->insert($p);
            }
        }

        // Seed products for other popular sizes
        $otherSizes = [
            '265/65R17' => [
                ['brand_id' => $bstoneId, 'model_name' => 'Dueler A/T 694', 'product_code' => 'BS26565AT694', 'full_description' => '265/65R17 112S Dueler A/T 694', 'load_index' => '112', 'speed_rating' => 'S', 'pattern_type' => 'All-Terrain', 'cost_price' => 2850.00, 'sell_price' => 3420.00],
                ['brand_id' => $contiId, 'model_name' => 'CrossContact LX2', 'product_code' => 'CC26565LX2', 'full_description' => '265/65R17 112H CrossContact LX2', 'load_index' => '112', 'speed_rating' => 'H', 'pattern_type' => 'Highway', 'cost_price' => 3100.00, 'sell_price' => 3720.00],
                ['brand_id' => $gdyrId, 'model_name' => 'Wrangler AT/SA+', 'product_code' => 'GY26565ATSA', 'full_description' => '265/65R17 112T Wrangler AT/SA+', 'load_index' => '112', 'speed_rating' => 'T', 'pattern_type' => 'All-Terrain', 'cost_price' => 2750.00, 'sell_price' => 3300.00],
                ['brand_id' => $dunlopId, 'model_name' => 'Grandtrek AT5', 'product_code' => 'DL26565AT5', 'full_description' => '265/65R17 112S Grandtrek AT5', 'load_index' => '112', 'speed_rating' => 'S', 'pattern_type' => 'All-Terrain', 'cost_price' => 2600.00, 'sell_price' => 3120.00],
            ],
            '225/45R17' => [
                ['brand_id' => $michId, 'model_name' => 'Pilot Sport 5', 'product_code' => 'MI22545PS5', 'full_description' => '225/45R17 94Y Pilot Sport 5', 'load_index' => '94', 'speed_rating' => 'Y', 'pattern_type' => 'Performance', 'cost_price' => 3200.00, 'sell_price' => 3840.00],
                ['brand_id' => $contiId, 'model_name' => 'PremiumContact 7', 'product_code' => 'CC22545PC7', 'full_description' => '225/45R17 91Y PremiumContact 7', 'load_index' => '91', 'speed_rating' => 'Y', 'pattern_type' => 'Performance', 'cost_price' => 2900.00, 'sell_price' => 3480.00],
                ['brand_id' => $hankId, 'model_name' => 'Ventus S1 evo3', 'product_code' => 'HK22545S1E3', 'full_description' => '225/45R17 94Y Ventus S1 evo3 K127', 'load_index' => '94', 'speed_rating' => 'Y', 'pattern_type' => 'Performance', 'cost_price' => 1950.00, 'sell_price' => 2340.00],
            ],
        ];

        $suvCatId = DB::table('cims_tyredash_categories')->where('code', 'SUV')->value('id');

        foreach ($otherSizes as $sizeName => $prods) {
            $sid = DB::table('cims_tyredash_sizes')->where('full_size', $sizeName)->value('id');
            if (!$sid) continue;

            $catId = in_array($sizeName, ['265/65R17', '235/65R17', '285/65R17']) ? $suvCatId : $passengerCatId;

            foreach ($prods as $p) {
                $p['category_id'] = $catId;
                $p['size_id'] = $sid;
                $p['markup_pct'] = 20.00;
                $p['is_active'] = true;
                $p['created_at'] = $now;
                $p['updated_at'] = $now;
                $p['deleted_at'] = null;
                $p['created_by'] = null;
                $p['updated_by'] = null;
                DB::table('cims_tyredash_products')->insert($p);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_tyredash_products');
    }
};
