<?php
header('Content-Type: text/plain');

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

// 1. Show full CREATE TABLE statement
echo "=== CREATE TABLE ===\n";
$result = \DB::select("SHOW CREATE TABLE client_master_addresses");
echo $result[0]->{'Create Table'} . "\n\n";

// 2. Show ALL records with all columns
echo "=== All records with address_type ===\n";
$all = \DB::table('client_master_addresses')->get(['id', 'client_id', 'address_id', 'address_type', 'address_type_id', 'address_type_name', 'is_default']);
foreach ($all as $a) {
    echo "  id={$a->id}, client_id={$a->client_id}, address_id={$a->address_id}, address_type=" . ($a->address_type ?? 'NULL') . ", type_id=" . ($a->address_type_id ?? 'NULL') . ", type_name=" . ($a->address_type_name ?? 'NULL') . "\n";
}

// 3. Check auto_increment
echo "\n=== Auto increment ===\n";
$status = \DB::select("SHOW TABLE STATUS LIKE 'client_master_addresses'");
echo "Auto_increment: " . ($status[0]->Auto_increment ?? 'NULL') . "\n";

// 4. Check what ClientMasterAddress model looks like
echo "\n=== ClientMasterAddress model ===\n";
try {
    $model = new \Modules\cims_pm_pro\Models\ClientMasterAddress();
    echo "Table: " . $model->getTable() . "\n";
    echo "PK: " . $model->getKeyName() . "\n";
    echo "Fillable: " . implode(', ', $model->getFillable()) . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
