<?php
/**
 * TyreDash Database Deployment Script
 *
 * Creates all 12 TyreDash tables and seeds reference data.
 * Place at web root and access via curl to deploy.
 *
 * Usage: curl https://yourdomain.com/deploy_tyredash.php
 *
 * IMPORTANT: Delete this file after successful deployment!
 */

// ─── Output setup ───────────────────────────────────────────────────────────
header('Content-Type: text/plain; charset=utf-8');
ob_implicit_flush(true);
if (ob_get_level()) ob_end_flush();

function out($msg) {
    echo "[" . date('H:i:s') . "] " . $msg . "\n";
    flush();
}

out("=== TyreDash Database Deployment ===");
out("");

// ─── Bootstrap Laravel ──────────────────────────────────────────────────────
try {
    $appBase = __DIR__ . '/application';
    require $appBase . '/vendor/autoload.php';
    $app = require_once $appBase . '/bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    $kernel->handle(\Illuminate\Http\Request::capture());
    out("Laravel bootstrapped successfully.");
} catch (\Throwable $e) {
    out("FATAL: Failed to bootstrap Laravel: " . $e->getMessage());
    exit(1);
}

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$errors = [];
$created = 0;
$skipped = 0;
$seeded = 0;

// ─── Helper: track created tables for migration registration ────────────────
$migrationsToRegister = [];

// ─── Table 1: cims_tyredash_brands ──────────────────────────────────────────
out("");
out("--- [1/12] cims_tyredash_brands ---");
if (Schema::hasTable('cims_tyredash_brands')) {
    out("  SKIP: Table already exists.");
    $skipped++;
} else {
    try {
        Schema::create('cims_tyredash_brands', function ($table) {
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
        $created++;
        $migrationsToRegister[] = '2026_03_18_100001_create_cims_tyredash_brands_table';
        out("  CREATED: cims_tyredash_brands");
    } catch (\Throwable $e) {
        $errors[] = "brands: " . $e->getMessage();
        out("  ERROR: " . $e->getMessage());
    }
}

// Seed brands
if (Schema::hasTable('cims_tyredash_brands') && DB::table('cims_tyredash_brands')->count() === 0) {
    try {
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
        $seeded++;
        out("  SEEDED: 15 brands");
    } catch (\Throwable $e) {
        $errors[] = "brands seed: " . $e->getMessage();
        out("  SEED ERROR: " . $e->getMessage());
    }
} else {
    out("  Seed skip: brands table already has data or does not exist.");
}

// ─── Table 2: cims_tyredash_categories ──────────────────────────────────────
out("");
out("--- [2/12] cims_tyredash_categories ---");
if (Schema::hasTable('cims_tyredash_categories')) {
    out("  SKIP: Table already exists.");
    $skipped++;
} else {
    try {
        Schema::create('cims_tyredash_categories', function ($table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->unique();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        $created++;
        $migrationsToRegister[] = '2026_03_18_100002_create_cims_tyredash_categories_table';
        out("  CREATED: cims_tyredash_categories");
    } catch (\Throwable $e) {
        $errors[] = "categories: " . $e->getMessage();
        out("  ERROR: " . $e->getMessage());
    }
}

// Seed categories
if (Schema::hasTable('cims_tyredash_categories') && DB::table('cims_tyredash_categories')->count() === 0) {
    try {
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
        $seeded++;
        out("  SEEDED: 7 categories");
    } catch (\Throwable $e) {
        $errors[] = "categories seed: " . $e->getMessage();
        out("  SEED ERROR: " . $e->getMessage());
    }
} else {
    out("  Seed skip: categories table already has data or does not exist.");
}

// ─── Table 3: cims_tyredash_sizes ───────────────────────────────────────────
out("");
out("--- [3/12] cims_tyredash_sizes ---");
if (Schema::hasTable('cims_tyredash_sizes')) {
    out("  SKIP: Table already exists.");
    $skipped++;
} else {
    try {
        Schema::create('cims_tyredash_sizes', function ($table) {
            $table->id();
            $table->string('full_size', 50)->comment('e.g. 205/65R16');
            $table->integer('width')->comment('Section width in mm, e.g. 205');
            $table->integer('profile')->comment('Aspect ratio, e.g. 65');
            $table->string('construction', 5)->default('R')->comment('R=Radial, D=Diagonal');
            $table->integer('rim_diameter')->comment('Rim diameter in inches, e.g. 16');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique('full_size');
            $table->index(['width', 'profile', 'rim_diameter']);
        });
        $created++;
        $migrationsToRegister[] = '2026_03_18_100003_create_cims_tyredash_sizes_table';
        out("  CREATED: cims_tyredash_sizes");
    } catch (\Throwable $e) {
        $errors[] = "sizes: " . $e->getMessage();
        out("  ERROR: " . $e->getMessage());
    }
}

// Seed sizes
if (Schema::hasTable('cims_tyredash_sizes') && DB::table('cims_tyredash_sizes')->count() === 0) {
    try {
        $now = now();
        $sizes = [
            // Passenger popular sizes
            ['155/65R14', 155, 65, 'R', 14],
            ['165/65R14', 165, 65, 'R', 14],
            ['175/65R14', 175, 65, 'R', 14],
            ['185/65R14', 185, 65, 'R', 14],
            ['175/65R15', 175, 65, 'R', 15],
            ['185/65R15', 185, 65, 'R', 15],
            ['195/65R15', 195, 65, 'R', 15],
            ['205/65R15', 205, 65, 'R', 15],
            ['195/55R15', 195, 55, 'R', 15],
            ['205/55R16', 205, 55, 'R', 16],
            ['205/65R16', 205, 65, 'R', 16],
            ['215/55R16', 215, 55, 'R', 16],
            ['215/60R16', 215, 60, 'R', 16],
            ['225/55R16', 225, 55, 'R', 16],
            ['205/50R17', 205, 50, 'R', 17],
            ['215/55R17', 215, 55, 'R', 17],
            ['225/45R17', 225, 45, 'R', 17],
            ['225/50R17', 225, 50, 'R', 17],
            ['225/55R17', 225, 55, 'R', 17],
            ['235/45R17', 235, 45, 'R', 17],
            ['225/40R18', 225, 40, 'R', 18],
            ['225/45R18', 225, 45, 'R', 18],
            ['235/40R18', 235, 40, 'R', 18],
            ['245/45R18', 245, 45, 'R', 18],
            ['255/35R18', 255, 35, 'R', 18],
            ['225/35R19', 225, 35, 'R', 19],
            ['235/35R19', 235, 35, 'R', 19],
            ['245/40R19', 245, 40, 'R', 19],
            ['255/35R19', 255, 35, 'R', 19],
            ['245/35R20', 245, 35, 'R', 20],
            ['275/40R20', 275, 40, 'R', 20],
            // SUV / 4x4 popular sizes
            ['215/65R16', 215, 65, 'R', 16],
            ['235/65R17', 235, 65, 'R', 17],
            ['235/60R18', 235, 60, 'R', 18],
            ['245/65R17', 245, 65, 'R', 17],
            ['255/60R18', 255, 60, 'R', 18],
            ['255/65R17', 255, 65, 'R', 17],
            ['265/65R17', 265, 65, 'R', 17],
            ['265/60R18', 265, 60, 'R', 18],
            ['275/65R17', 275, 65, 'R', 17],
            ['275/70R16', 275, 70, 'R', 16],
            ['285/65R17', 285, 65, 'R', 17],
            ['285/60R18', 285, 60, 'R', 18],
            // Light Truck / Van
            ['195R14C', 195, 0, 'R', 14],
            ['205/65R16C', 205, 65, 'R', 16],
            ['215/65R16C', 215, 65, 'R', 16],
            ['225/65R16C', 225, 65, 'R', 16],
            ['235/65R16C', 235, 65, 'R', 16],
        ];

        $insertCount = 0;
        foreach ($sizes as $s) {
            // Use insertOrIgnore to handle the unique constraint on full_size
            // (e.g. 215/65R16 appears as both passenger and SUV)
            $inserted = DB::table('cims_tyredash_sizes')->insertOrIgnore([
                'full_size' => $s[0],
                'width' => $s[1],
                'profile' => $s[2],
                'construction' => $s[3],
                'rim_diameter' => $s[4],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $insertCount += $inserted;
        }
        $seeded++;
        out("  SEEDED: {$insertCount} tyre sizes (some duplicates skipped due to unique constraint)");
    } catch (\Throwable $e) {
        $errors[] = "sizes seed: " . $e->getMessage();
        out("  SEED ERROR: " . $e->getMessage());
    }
} else {
    out("  Seed skip: sizes table already has data or does not exist.");
}

// ─── Table 4: cims_tyredash_products ────────────────────────────────────────
out("");
out("--- [4/12] cims_tyredash_products ---");
if (Schema::hasTable('cims_tyredash_products')) {
    out("  SKIP: Table already exists.");
    $skipped++;
} else {
    try {
        Schema::create('cims_tyredash_products', function ($table) {
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
        $created++;
        $migrationsToRegister[] = '2026_03_18_100004_create_cims_tyredash_products_table';
        out("  CREATED: cims_tyredash_products");
    } catch (\Throwable $e) {
        $errors[] = "products: " . $e->getMessage();
        out("  ERROR: " . $e->getMessage());
    }
}

// Seed products
if (Schema::hasTable('cims_tyredash_products') && DB::table('cims_tyredash_products')->count() === 0) {
    try {
        $now = now();

        // Get IDs for the size and brands
        $sizeId = DB::table('cims_tyredash_sizes')->where('full_size', '205/65R16')->value('id');
        $sizeCId = DB::table('cims_tyredash_sizes')->where('full_size', '205/65R16C')->value('id');
        $passengerCatId = DB::table('cims_tyredash_categories')->where('code', 'PCR')->value('id');
        $ltCatId = DB::table('cims_tyredash_categories')->where('code', 'LT')->value('id');
        $suvCatId = DB::table('cims_tyredash_categories')->where('code', 'SUV')->value('id');

        $contiId = DB::table('cims_tyredash_brands')->where('code', 'CONTI')->value('id');
        $gdyrId = DB::table('cims_tyredash_brands')->where('code', 'GDYR')->value('id');
        $michId = DB::table('cims_tyredash_brands')->where('code', 'MICH')->value('id');
        $sailunId = DB::table('cims_tyredash_brands')->where('code', 'SAILUN')->value('id');
        $rblackId = DB::table('cims_tyredash_brands')->where('code', 'RBLACK')->value('id');
        $bstoneId = DB::table('cims_tyredash_brands')->where('code', 'BSTONE')->value('id');
        $dunlopId = DB::table('cims_tyredash_brands')->where('code', 'DUNLOP')->value('id');
        $hankId = DB::table('cims_tyredash_brands')->where('code', 'HANK')->value('id');

        $productCount = 0;

        if ($sizeId && $passengerCatId) {
            // 205/65R16 products (matching StockFinder screenshots)
            $products = [
                ['brand_id' => $contiId, 'category_id' => $ltCatId ?: $passengerCatId, 'size_id' => $sizeCId ?: $sizeId, 'model_name' => 'ContiVanContact 100', 'product_code' => 'D451287', 'full_description' => '205/65R16C 107/105T ContiVanContact 100 R', 'load_index' => '107/105', 'speed_rating' => 'T', 'pattern_type' => 'Highway', 'cost_price' => 2399.00, 'sell_price' => 2878.00, 'markup_pct' => 20.00],
                ['brand_id' => $gdyrId, 'category_id' => $passengerCatId, 'size_id' => $sizeId, 'model_name' => 'Assurance Triplemax 2', 'product_code' => 'S85062', 'full_description' => '205/65R16 95H Assurance Triplemax 2', 'load_index' => '95', 'speed_rating' => 'H', 'pattern_type' => 'Highway', 'cost_price' => 2466.00, 'sell_price' => 2960.00, 'markup_pct' => 20.00],
                ['brand_id' => $michId, 'category_id' => $passengerCatId, 'size_id' => $sizeId, 'model_name' => 'Primacy 5', 'product_code' => '075935', 'full_description' => '205/65 R16 95W TL Primacy 5 MI', 'load_index' => '95', 'speed_rating' => 'W', 'pattern_type' => 'Highway', 'cost_price' => 2409.00, 'sell_price' => 2891.00, 'markup_pct' => 20.00],
                ['brand_id' => $sailunId, 'category_id' => $passengerCatId, 'size_id' => $sizeId, 'model_name' => 'SH406 BHSL', 'product_code' => '3220005335', 'full_description' => '205/65R16 95H SH406 BHSL', 'load_index' => '95', 'speed_rating' => 'H', 'pattern_type' => 'Highway', 'cost_price' => 1228.00, 'sell_price' => 1473.00, 'markup_pct' => 20.00],
                ['brand_id' => $rblackId, 'category_id' => $passengerCatId, 'size_id' => $sizeId, 'model_name' => 'Royal Comfort', 'product_code' => 'RK888H1', 'full_description' => '205/65R16 Royal Comfort 95H', 'load_index' => '95', 'speed_rating' => 'H', 'pattern_type' => 'Highway', 'cost_price' => 989.00, 'sell_price' => 1187.00, 'markup_pct' => 20.00],
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
                $productCount++;
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
                $productCount++;
            }
        }

        $seeded++;
        out("  SEEDED: {$productCount} products");
    } catch (\Throwable $e) {
        $errors[] = "products seed: " . $e->getMessage();
        out("  SEED ERROR: " . $e->getMessage());
    }
} else {
    out("  Seed skip: products table already has data or does not exist.");
}

// ─── Table 5: cims_tyredash_services ────────────────────────────────────────
out("");
out("--- [5/12] cims_tyredash_services ---");
if (Schema::hasTable('cims_tyredash_services')) {
    out("  SKIP: Table already exists.");
    $skipped++;
} else {
    try {
        Schema::create('cims_tyredash_services', function ($table) {
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
        $created++;
        $migrationsToRegister[] = '2026_03_18_100005_create_cims_tyredash_services_table';
        out("  CREATED: cims_tyredash_services");
    } catch (\Throwable $e) {
        $errors[] = "services: " . $e->getMessage();
        out("  ERROR: " . $e->getMessage());
    }
}

// Seed services
if (Schema::hasTable('cims_tyredash_services') && DB::table('cims_tyredash_services')->count() === 0) {
    try {
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
        $seeded++;
        out("  SEEDED: 9 services");
    } catch (\Throwable $e) {
        $errors[] = "services seed: " . $e->getMessage();
        out("  SEED ERROR: " . $e->getMessage());
    }
} else {
    out("  Seed skip: services table already has data or does not exist.");
}

// ─── Table 6: cims_tyredash_branches ────────────────────────────────────────
out("");
out("--- [6/12] cims_tyredash_branches ---");
if (Schema::hasTable('cims_tyredash_branches')) {
    out("  SKIP: Table already exists.");
    $skipped++;
} else {
    try {
        Schema::create('cims_tyredash_branches', function ($table) {
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
        $created++;
        $migrationsToRegister[] = '2026_03_18_100006_create_cims_tyredash_branches_table';
        out("  CREATED: cims_tyredash_branches");
    } catch (\Throwable $e) {
        $errors[] = "branches: " . $e->getMessage();
        out("  ERROR: " . $e->getMessage());
    }
}

// Seed branches
if (Schema::hasTable('cims_tyredash_branches') && DB::table('cims_tyredash_branches')->count() === 0) {
    try {
        $now = now();
        DB::table('cims_tyredash_branches')->insert([
            ['name' => 'Head Office', 'code' => 'HQ', 'address' => '123 Main Road', 'city' => 'Durban', 'province' => 'KwaZulu-Natal', 'phone' => '031 555 0001', 'email' => 'ho@example.co.za', 'manager_name' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Durban Central', 'code' => 'DBN', 'address' => '456 Smith Street', 'city' => 'Durban', 'province' => 'KwaZulu-Natal', 'phone' => '031 555 0002', 'email' => 'durban@example.co.za', 'manager_name' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Pinetown', 'code' => 'PTWN', 'address' => '78 Old Main Road', 'city' => 'Pinetown', 'province' => 'KwaZulu-Natal', 'phone' => '031 555 0003', 'email' => 'pinetown@example.co.za', 'manager_name' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Waterfall', 'code' => 'WFALL', 'address' => '12 Waterfall Drive', 'city' => 'Waterfall', 'province' => 'KwaZulu-Natal', 'phone' => '031 555 0004', 'email' => 'waterfall@example.co.za', 'manager_name' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Umhlanga', 'code' => 'UMHL', 'address' => '34 Umhlanga Rocks Drive', 'city' => 'Umhlanga', 'province' => 'KwaZulu-Natal', 'phone' => '031 555 0005', 'email' => 'umhlanga@example.co.za', 'manager_name' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
        ]);
        $seeded++;
        out("  SEEDED: 5 branches");
    } catch (\Throwable $e) {
        $errors[] = "branches seed: " . $e->getMessage();
        out("  SEED ERROR: " . $e->getMessage());
    }
} else {
    out("  Seed skip: branches table already has data or does not exist.");
}

// ─── Table 7: cims_tyredash_stock ───────────────────────────────────────────
out("");
out("--- [7/12] cims_tyredash_stock ---");
if (Schema::hasTable('cims_tyredash_stock')) {
    out("  SKIP: Table already exists.");
    $skipped++;
} else {
    try {
        Schema::create('cims_tyredash_stock', function ($table) {
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
        $created++;
        $migrationsToRegister[] = '2026_03_18_100007_create_cims_tyredash_stock_table';
        out("  CREATED: cims_tyredash_stock");
    } catch (\Throwable $e) {
        $errors[] = "stock: " . $e->getMessage();
        out("  ERROR: " . $e->getMessage());
    }
}

// Seed stock
if (Schema::hasTable('cims_tyredash_stock') && DB::table('cims_tyredash_stock')->count() === 0) {
    try {
        $now = now();
        $products = DB::table('cims_tyredash_products')->pluck('id')->toArray();
        $branches = DB::table('cims_tyredash_branches')->pluck('id')->toArray();

        $stockCount = 0;
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
                $stockCount++;
            }
        }
        $seeded++;
        out("  SEEDED: {$stockCount} stock records (" . count($products) . " products x " . count($branches) . " branches)");
    } catch (\Throwable $e) {
        $errors[] = "stock seed: " . $e->getMessage();
        out("  SEED ERROR: " . $e->getMessage());
    }
} else {
    out("  Seed skip: stock table already has data or does not exist.");
}

// ─── Table 8: cims_tyredash_customers ───────────────────────────────────────
out("");
out("--- [8/12] cims_tyredash_customers ---");
if (Schema::hasTable('cims_tyredash_customers')) {
    out("  SKIP: Table already exists.");
    $skipped++;
} else {
    try {
        Schema::create('cims_tyredash_customers', function ($table) {
            $table->id();
            $table->unsignedBigInteger('client_master_id')->nullable()->comment('Link to CIMS client_master');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('company_name', 200)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('cell', 30)->nullable();
            $table->string('vat_number', 50)->nullable();
            $table->string('debtor_account', 50)->nullable();
            $table->string('customer_type', 20)->default('retail')->comment('retail, fleet, corporate, government, insurance');
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('client_master_id');
            $table->index('phone');
            $table->index('cell');
            $table->index('email');
            $table->index('debtor_account');
            $table->index('customer_type');
        });
        $created++;
        $migrationsToRegister[] = '2026_03_18_100008_create_cims_tyredash_customers_table';
        out("  CREATED: cims_tyredash_customers");
    } catch (\Throwable $e) {
        $errors[] = "customers: " . $e->getMessage();
        out("  ERROR: " . $e->getMessage());
    }
}

// ─── Table 9: cims_tyredash_vehicles ────────────────────────────────────────
out("");
out("--- [9/12] cims_tyredash_vehicles ---");
if (Schema::hasTable('cims_tyredash_vehicles')) {
    out("  SKIP: Table already exists.");
    $skipped++;
} else {
    try {
        Schema::create('cims_tyredash_vehicles', function ($table) {
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
        $created++;
        $migrationsToRegister[] = '2026_03_18_100009_create_cims_tyredash_vehicles_table';
        out("  CREATED: cims_tyredash_vehicles");
    } catch (\Throwable $e) {
        $errors[] = "vehicles: " . $e->getMessage();
        out("  ERROR: " . $e->getMessage());
    }
}

// ─── Table 10: cims_tyredash_quotes + quote_options + quote_services ────────
out("");
out("--- [10/12] cims_tyredash_quotes (+ quote_options + quote_services) ---");
$quotesCreated = false;
if (Schema::hasTable('cims_tyredash_quotes')) {
    out("  SKIP: cims_tyredash_quotes already exists.");
    $skipped++;
} else {
    try {
        Schema::create('cims_tyredash_quotes', function ($table) {
            $table->id();
            $table->string('quote_number', 30)->unique();
            $table->string('customer_order_ref', 100)->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('salesman_id')->nullable()->comment('User ID of salesman');
            $table->string('salesman_name', 150)->nullable();
            $table->date('quote_date');
            $table->date('valid_until')->nullable();
            $table->string('status', 20)->default('draft')->comment('draft, sent, accepted, declined, expired, invoiced');
            $table->text('customer_comment')->nullable();
            $table->text('internal_notes')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('customer_id')->references('id')->on('cims_tyredash_customers')->onDelete('set null');
            $table->foreign('vehicle_id')->references('id')->on('cims_tyredash_vehicles')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('cims_tyredash_branches')->onDelete('set null');
            $table->index('quote_number');
            $table->index('status');
            $table->index('quote_date');
            $table->index('branch_id');
            $table->index('salesman_id');
        });
        $created++;
        $quotesCreated = true;
        out("  CREATED: cims_tyredash_quotes");
    } catch (\Throwable $e) {
        $errors[] = "quotes: " . $e->getMessage();
        out("  ERROR: " . $e->getMessage());
    }
}

if (Schema::hasTable('cims_tyredash_quote_options')) {
    out("  SKIP: cims_tyredash_quote_options already exists.");
} else {
    try {
        Schema::create('cims_tyredash_quote_options', function ($table) {
            $table->id();
            $table->unsignedBigInteger('quote_id');
            $table->tinyInteger('option_number')->comment('1-5');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity')->default(4);
            $table->decimal('unit_cost', 15, 2)->default(0)->comment('Cost price at time of quote');
            $table->decimal('unit_price', 15, 2)->default(0)->comment('Sell price incl VAT');
            $table->decimal('markup_pct', 5, 2)->default(20.00);
            $table->decimal('discount_pct', 5, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->boolean('is_selected')->default(false)->comment('Customer selected this option');
            $table->timestamps();
            $table->foreign('quote_id')->references('id')->on('cims_tyredash_quotes')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('cims_tyredash_products')->onDelete('restrict');
            $table->unique(['quote_id', 'option_number']);
            $table->index('quote_id');
        });
        $created++;
        out("  CREATED: cims_tyredash_quote_options");
    } catch (\Throwable $e) {
        $errors[] = "quote_options: " . $e->getMessage();
        out("  ERROR: " . $e->getMessage());
    }
}

if (Schema::hasTable('cims_tyredash_quote_services')) {
    out("  SKIP: cims_tyredash_quote_services already exists.");
} else {
    try {
        Schema::create('cims_tyredash_quote_services', function ($table) {
            $table->id();
            $table->unsignedBigInteger('quote_id');
            $table->unsignedBigInteger('service_id');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->timestamps();
            $table->foreign('quote_id')->references('id')->on('cims_tyredash_quotes')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('cims_tyredash_services')->onDelete('restrict');
            $table->index('quote_id');
        });
        $created++;
        out("  CREATED: cims_tyredash_quote_services");
    } catch (\Throwable $e) {
        $errors[] = "quote_services: " . $e->getMessage();
        out("  ERROR: " . $e->getMessage());
    }
}

if ($quotesCreated || !in_array('2026_03_18_100010_create_cims_tyredash_quotes_table', $migrationsToRegister)) {
    // Only add migration name once for the whole quotes migration file
    if ($quotesCreated) {
        $migrationsToRegister[] = '2026_03_18_100010_create_cims_tyredash_quotes_table';
    }
}

// ─── Table 11: cims_tyredash_job_cards + job_card_tyres + job_card_services ─
out("");
out("--- [11/12] cims_tyredash_job_cards (+ job_card_tyres + job_card_services) ---");
$jobCardsCreated = false;
if (Schema::hasTable('cims_tyredash_job_cards')) {
    out("  SKIP: cims_tyredash_job_cards already exists.");
    $skipped++;
} else {
    try {
        Schema::create('cims_tyredash_job_cards', function ($table) {
            $table->id();
            $table->string('job_card_number', 30)->unique();
            $table->unsignedBigInteger('quote_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('technician_id')->nullable()->comment('User ID of technician');
            $table->string('technician_name', 150)->nullable();
            $table->date('job_date');
            $table->string('status', 20)->default('open')->comment('open, in_progress, awaiting_parts, complete, invoiced, cancelled');
            $table->integer('odometer_in')->nullable();
            $table->integer('odometer_out')->nullable();
            $table->text('vehicle_condition_notes')->nullable();
            $table->text('work_notes')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable()->comment('Grow CRM invoice ID');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('quote_id')->references('id')->on('cims_tyredash_quotes')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('cims_tyredash_customers')->onDelete('set null');
            $table->foreign('vehicle_id')->references('id')->on('cims_tyredash_vehicles')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('cims_tyredash_branches')->onDelete('set null');
            $table->index('status');
            $table->index('job_date');
            $table->index('branch_id');
        });
        $created++;
        $jobCardsCreated = true;
        out("  CREATED: cims_tyredash_job_cards");
    } catch (\Throwable $e) {
        $errors[] = "job_cards: " . $e->getMessage();
        out("  ERROR: " . $e->getMessage());
    }
}

if (Schema::hasTable('cims_tyredash_job_card_tyres')) {
    out("  SKIP: cims_tyredash_job_card_tyres already exists.");
} else {
    try {
        Schema::create('cims_tyredash_job_card_tyres', function ($table) {
            $table->id();
            $table->unsignedBigInteger('job_card_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity')->default(1);
            $table->string('position', 20)->nullable()->comment('FL, FR, RL, RR, spare');
            $table->string('serial_number_new', 50)->nullable();
            $table->string('serial_number_old', 50)->nullable();
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->timestamps();
            $table->foreign('job_card_id')->references('id')->on('cims_tyredash_job_cards')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('cims_tyredash_products')->onDelete('restrict');
            $table->index('job_card_id');
            $table->index('serial_number_new');
        });
        $created++;
        out("  CREATED: cims_tyredash_job_card_tyres");
    } catch (\Throwable $e) {
        $errors[] = "job_card_tyres: " . $e->getMessage();
        out("  ERROR: " . $e->getMessage());
    }
}

if (Schema::hasTable('cims_tyredash_job_card_services')) {
    out("  SKIP: cims_tyredash_job_card_services already exists.");
} else {
    try {
        Schema::create('cims_tyredash_job_card_services', function ($table) {
            $table->id();
            $table->unsignedBigInteger('job_card_id');
            $table->unsignedBigInteger('service_id');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->boolean('completed')->default(false);
            $table->timestamps();
            $table->foreign('job_card_id')->references('id')->on('cims_tyredash_job_cards')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('cims_tyredash_services')->onDelete('restrict');
            $table->index('job_card_id');
        });
        $created++;
        out("  CREATED: cims_tyredash_job_card_services");
    } catch (\Throwable $e) {
        $errors[] = "job_card_services: " . $e->getMessage();
        out("  ERROR: " . $e->getMessage());
    }
}

if ($jobCardsCreated) {
    $migrationsToRegister[] = '2026_03_18_100011_create_cims_tyredash_job_cards_table';
}

// ─── Table 12: cims_tyredash_settings ───────────────────────────────────────
out("");
out("--- [12/12] cims_tyredash_settings ---");
if (Schema::hasTable('cims_tyredash_settings')) {
    out("  SKIP: Table already exists.");
    $skipped++;
} else {
    try {
        Schema::create('cims_tyredash_settings', function ($table) {
            $table->id();
            $table->string('setting_key', 100)->unique();
            $table->text('setting_value')->nullable();
            $table->string('setting_group', 50)->default('general');
            $table->timestamps();
        });
        $created++;
        $migrationsToRegister[] = '2026_03_18_100012_create_cims_tyredash_settings_table';
        out("  CREATED: cims_tyredash_settings");
    } catch (\Throwable $e) {
        $errors[] = "settings: " . $e->getMessage();
        out("  ERROR: " . $e->getMessage());
    }
}

// Seed settings
if (Schema::hasTable('cims_tyredash_settings') && DB::table('cims_tyredash_settings')->count() === 0) {
    try {
        $now = now();
        DB::table('cims_tyredash_settings')->insert([
            // General
            ['setting_key' => 'company_name', 'setting_value' => 'Apex Tyres', 'setting_group' => 'general', 'created_at' => $now, 'updated_at' => $now],
            ['setting_key' => 'company_phone', 'setting_value' => '', 'setting_group' => 'general', 'created_at' => $now, 'updated_at' => $now],
            ['setting_key' => 'company_email', 'setting_value' => '', 'setting_group' => 'general', 'created_at' => $now, 'updated_at' => $now],
            ['setting_key' => 'company_address', 'setting_value' => '', 'setting_group' => 'general', 'created_at' => $now, 'updated_at' => $now],
            ['setting_key' => 'vat_number', 'setting_value' => '', 'setting_group' => 'general', 'created_at' => $now, 'updated_at' => $now],
            // Pricing
            ['setting_key' => 'default_markup_pct', 'setting_value' => '20', 'setting_group' => 'pricing', 'created_at' => $now, 'updated_at' => $now],
            ['setting_key' => 'vat_rate', 'setting_value' => '15', 'setting_group' => 'pricing', 'created_at' => $now, 'updated_at' => $now],
            ['setting_key' => 'currency_symbol', 'setting_value' => 'R', 'setting_group' => 'pricing', 'created_at' => $now, 'updated_at' => $now],
            // Quotes
            ['setting_key' => 'quote_prefix', 'setting_value' => 'TD', 'setting_group' => 'quotes', 'created_at' => $now, 'updated_at' => $now],
            ['setting_key' => 'quote_validity_days', 'setting_value' => '14', 'setting_group' => 'quotes', 'created_at' => $now, 'updated_at' => $now],
            ['setting_key' => 'max_quote_options', 'setting_value' => '5', 'setting_group' => 'quotes', 'created_at' => $now, 'updated_at' => $now],
            // Job Cards
            ['setting_key' => 'job_card_prefix', 'setting_value' => 'JC', 'setting_group' => 'jobcards', 'created_at' => $now, 'updated_at' => $now],
            // Stock
            ['setting_key' => 'default_min_stock', 'setting_value' => '4', 'setting_group' => 'stock', 'created_at' => $now, 'updated_at' => $now],
            ['setting_key' => 'low_stock_alert', 'setting_value' => '1', 'setting_group' => 'stock', 'created_at' => $now, 'updated_at' => $now],
        ]);
        $seeded++;
        out("  SEEDED: 14 settings");
    } catch (\Throwable $e) {
        $errors[] = "settings seed: " . $e->getMessage();
        out("  SEED ERROR: " . $e->getMessage());
    }
} else {
    out("  Seed skip: settings table already has data or does not exist.");
}

// ─── Register migrations in the migrations table ────────────────────────────
out("");
out("--- Registering migrations ---");
if (!empty($migrationsToRegister)) {
    try {
        // Get the next batch number
        $lastBatch = DB::table('migrations')->max('batch') ?? 0;
        $nextBatch = $lastBatch + 1;

        foreach ($migrationsToRegister as $migrationName) {
            // Check if already registered
            $exists = DB::table('migrations')->where('migration', $migrationName)->exists();
            if ($exists) {
                out("  Already registered: {$migrationName}");
                continue;
            }
            DB::table('migrations')->insert([
                'migration' => $migrationName,
                'batch' => $nextBatch,
            ]);
            out("  Registered: {$migrationName} (batch {$nextBatch})");
        }
    } catch (\Throwable $e) {
        $errors[] = "migration registration: " . $e->getMessage();
        out("  ERROR registering migrations: " . $e->getMessage());
    }
} else {
    out("  No new migrations to register (all tables already existed).");
}

// ─── Clear compiled views cache ─────────────────────────────────────────────
out("");
out("--- Clearing compiled views cache ---");
try {
    $viewsCachePath = $appBase . '/storage/framework/views';
    if (is_dir($viewsCachePath)) {
        $files = glob($viewsCachePath . '/*.php');
        $cleared = 0;
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $cleared++;
            }
        }
        out("  Cleared {$cleared} compiled view files.");
    } else {
        out("  Views cache directory not found, skipping.");
    }
} catch (\Throwable $e) {
    $errors[] = "cache clear: " . $e->getMessage();
    out("  ERROR: " . $e->getMessage());
}

// ─── Summary ────────────────────────────────────────────────────────────────
out("");
out("========================================");
out("  DEPLOYMENT SUMMARY");
out("========================================");
out("  Tables created: {$created}");
out("  Tables skipped (already existed): {$skipped}");
out("  Seed operations: {$seeded}");
out("  Errors: " . count($errors));

if (!empty($errors)) {
    out("");
    out("  ERRORS:");
    foreach ($errors as $i => $err) {
        out("    " . ($i + 1) . ". " . $err);
    }
}

out("");
if (count($errors) === 0) {
    out("  STATUS: SUCCESS");
    out("");
    out("  IMPORTANT: Delete this file from the web root after deployment!");
    out("  rm deploy_tyredash.php");
} else {
    out("  STATUS: COMPLETED WITH ERRORS");
    out("  Review the errors above and re-run if needed.");
}

out("");
out("=== Deployment complete ===");
