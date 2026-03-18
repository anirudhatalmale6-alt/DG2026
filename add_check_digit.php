<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Check if column exists
if (Schema::hasColumn('cims_emp201_declarations', 'check_digit')) {
    echo "Column 'check_digit' already exists.\n";
} else {
    DB::statement("ALTER TABLE cims_emp201_declarations ADD COLUMN check_digit VARCHAR(5) NULL AFTER payment_reference");
    echo "Column 'check_digit' added successfully.\n";
}

// Verify
$cols = DB::select("SHOW COLUMNS FROM cims_emp201_declarations LIKE 'check_digit'");
echo "Verification: " . (count($cols) > 0 ? "Column exists" : "Column NOT found") . "\n";
