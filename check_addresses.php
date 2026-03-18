<?php
header('Content-Type: text/plain');

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

// 1. Check table structure
echo "=== client_master_addresses columns ===\n";
$cols = \DB::getSchemaBuilder()->getColumnListing('client_master_addresses');
echo implode(', ', $cols) . "\n\n";

// 2. Check indexes/keys on this table
echo "=== Table indexes ===\n";
$indexes = \DB::select("SHOW INDEX FROM client_master_addresses");
foreach ($indexes as $idx) {
    echo "  Key: {$idx->Key_name}, Column: {$idx->Column_name}, Unique: " . ($idx->Non_unique ? 'NO' : 'YES') . "\n";
}

// 3. Check existing records for client 13
echo "\n=== Existing addresses for client_id=13 ===\n";
$addresses = \DB::table('client_master_addresses')->where('client_id', 13)->get();
if ($addresses->isEmpty()) {
    echo "  NONE\n";
} else {
    foreach ($addresses as $a) {
        echo "  ---\n";
        foreach (get_object_vars($a) as $k => $v) {
            echo "  {$k}: " . ($v ?? 'NULL') . "\n";
        }
    }
}

// 4. Check total records in table
echo "\n=== Total records ===\n";
$total = \DB::table('client_master_addresses')->count();
echo "Total: {$total}\n";

// 5. Show all records
echo "\n=== All records ===\n";
$all = \DB::table('client_master_addresses')->get();
foreach ($all as $a) {
    echo "  id=" . ($a->id ?? '?') . ", client_id={$a->client_id}, address_id={$a->address_id}\n";
}
