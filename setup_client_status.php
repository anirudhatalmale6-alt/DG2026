<?php
require_once __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "<h2>Setup cims_client_status</h2><pre>";

// 1. Create the table
if (!Schema::hasTable('cims_client_status')) {
    DB::statement("CREATE TABLE cims_client_status (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        status_name VARCHAR(100) NOT NULL,
        isactive TINYINT(1) NOT NULL DEFAULT 1,
        isdelete TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "1. Table cims_client_status CREATED.\n";
} else {
    echo "1. Table cims_client_status already exists.\n";
}

// 2. Seed data
$count = DB::table('cims_client_status')->count();
if ($count == 0) {
    DB::table('cims_client_status')->insert([
        ['status_name' => 'Active', 'isactive' => 1, 'isdelete' => 0],
        ['status_name' => 'Inactive', 'isactive' => 1, 'isdelete' => 0],
        ['status_name' => 'Suspended', 'isactive' => 1, 'isdelete' => 0],
        ['status_name' => 'Prospect', 'isactive' => 1, 'isdelete' => 0],
        ['status_name' => 'Cancelled', 'isactive' => 1, 'isdelete' => 0],
    ]);
    echo "2. Seeded 5 status records.\n";
} else {
    echo "2. Table already has {$count} records, skipping seed.\n";
}

// 3. Add client_status_id column to client_master if not exists
$hasCol = Schema::hasColumn('client_master', 'client_status_id');
if (!$hasCol) {
    DB::statement("ALTER TABLE client_master ADD COLUMN client_status_id INT UNSIGNED NULL DEFAULT NULL");
    echo "3. Added client_status_id column to client_master.\n";
} else {
    echo "3. client_master already has client_status_id column.\n";
}

// Verify
$rows = DB::table('cims_client_status')->get();
echo "\nTable contents:\n";
foreach ($rows as $r) {
    echo "  ID:{$r->id} | {$r->status_name} | active:{$r->isactive} | deleted:{$r->isdelete}\n";
}
echo "</pre><p style='color:green;font-weight:bold;'>Done.</p>";
