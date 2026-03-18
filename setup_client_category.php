<?php
require_once __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "<pre>";

// 1. Create the table
if (!Schema::hasTable('cims_client_category')) {
    DB::statement("CREATE TABLE cims_client_category (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        category_name VARCHAR(100) NOT NULL,
        isactive TINYINT(1) NOT NULL DEFAULT 1,
        isdelete TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "1. Table cims_client_category CREATED.\n";
} else {
    echo "1. Table already exists.\n";
}

// 2. Seed
$count = DB::table('cims_client_category')->count();
if ($count == 0) {
    DB::table('cims_client_category')->insert([
        ['category_name' => 'Individual', 'isactive' => 1, 'isdelete' => 0],
        ['category_name' => 'Company', 'isactive' => 1, 'isdelete' => 0],
        ['category_name' => 'Trust', 'isactive' => 1, 'isdelete' => 0],
        ['category_name' => 'Partnership', 'isactive' => 1, 'isdelete' => 0],
        ['category_name' => 'Close Corporation', 'isactive' => 1, 'isdelete' => 0],
    ]);
    echo "2. Seeded 5 category records.\n";
} else {
    echo "2. Already has {$count} records.\n";
}

// 3. Add column to client_master
if (!Schema::hasColumn('client_master', 'client_category_id')) {
    DB::statement("ALTER TABLE client_master ADD COLUMN client_category_id INT UNSIGNED NULL DEFAULT NULL");
    echo "3. Added client_category_id to client_master.\n";
} else {
    echo "3. Column already exists.\n";
}

// Verify
$rows = DB::table('cims_client_category')->get();
echo "\nTable contents:\n";
foreach ($rows as $r) { echo "  ID:{$r->id} | {$r->category_name} | active:{$r->isactive}\n"; }
echo "</pre>Done.";
