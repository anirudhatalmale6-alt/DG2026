<?php
require_once __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "<pre>";

// 1. Create cims_master_services table
if (!Schema::hasTable('cims_master_services')) {
    DB::statement("CREATE TABLE cims_master_services (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        service_name VARCHAR(100) NOT NULL,
        isactive TINYINT(1) NOT NULL DEFAULT 1,
        isdelete TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "1. Table cims_master_services CREATED.\n";
} else {
    echo "1. Table cims_master_services already exists.\n";
}

// 2. Create pivot table cims_client_service
if (!Schema::hasTable('cims_client_service')) {
    DB::statement("CREATE TABLE cims_client_service (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        client_id INT UNSIGNED NOT NULL,
        service_id INT UNSIGNED NOT NULL,
        isactive TINYINT(1) NOT NULL DEFAULT 1,
        isdelete TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_client_id (client_id),
        INDEX idx_service_id (service_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "2. Pivot table cims_client_service CREATED.\n";
} else {
    echo "2. Pivot table cims_client_service already exists.\n";
}

// 3. Seed master services
$count = DB::table('cims_master_services')->count();
if ($count == 0) {
    DB::table('cims_master_services')->insert([
        ['service_name' => 'Accounting', 'isactive' => 1, 'isdelete' => 0],
        ['service_name' => 'Bookkeeping', 'isactive' => 1, 'isdelete' => 0],
        ['service_name' => 'Annual Financial Statements', 'isactive' => 1, 'isdelete' => 0],
        ['service_name' => 'Management Accounts', 'isactive' => 1, 'isdelete' => 0],
        ['service_name' => 'Income Tax', 'isactive' => 1, 'isdelete' => 0],
        ['service_name' => 'Provisional Tax', 'isactive' => 1, 'isdelete' => 0],
        ['service_name' => 'VAT', 'isactive' => 1, 'isdelete' => 0],
        ['service_name' => 'Payroll', 'isactive' => 1, 'isdelete' => 0],
        ['service_name' => 'EMP201', 'isactive' => 1, 'isdelete' => 0],
        ['service_name' => 'EMP501', 'isactive' => 1, 'isdelete' => 0],
        ['service_name' => 'UIF', 'isactive' => 1, 'isdelete' => 0],
        ['service_name' => 'COIDA / WCA', 'isactive' => 1, 'isdelete' => 0],
        ['service_name' => 'SARS eFiling', 'isactive' => 1, 'isdelete' => 0],
        ['service_name' => 'Tax Clearance', 'isactive' => 1, 'isdelete' => 0],
        ['service_name' => 'Secretarial', 'isactive' => 1, 'isdelete' => 0],
        ['service_name' => 'CIPC Annual Returns', 'isactive' => 1, 'isdelete' => 0],
        ['service_name' => 'Company Registration', 'isactive' => 1, 'isdelete' => 0],
        ['service_name' => 'B-BBEE Compliance', 'isactive' => 1, 'isdelete' => 0],
        ['service_name' => 'Audit', 'isactive' => 1, 'isdelete' => 0],
        ['service_name' => 'Advisory', 'isactive' => 1, 'isdelete' => 0],
    ]);
    echo "3. Seeded 20 service records.\n";
} else {
    echo "3. Already has {$count} records.\n";
}

// 4. Drop old client_services_id column if exists
if (Schema::hasColumn('client_master', 'client_services_id')) {
    DB::statement("ALTER TABLE client_master DROP COLUMN client_services_id");
    echo "4. Removed client_services_id from client_master.\n";
} else {
    echo "4. client_services_id not found (already removed).\n";
}

// 5. Drop old cims_client_services table if exists
if (Schema::hasTable('cims_client_services')) {
    DB::statement("DROP TABLE cims_client_services");
    echo "5. Dropped old cims_client_services table.\n";
} else {
    echo "5. Old table not found.\n";
}

// Verify
$rows = DB::table('cims_master_services')->orderBy('id')->get();
echo "\ncims_master_services contents:\n";
foreach ($rows as $r) { echo "  ID:{$r->id} | {$r->service_name}\n"; }
echo "</pre>Done.";
