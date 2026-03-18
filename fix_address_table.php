<?php
header('Content-Type: text/plain');

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "=== Before fix ===\n";
$result = \DB::select("SHOW CREATE TABLE client_master_addresses");
echo $result[0]->{'Create Table'} . "\n\n";

// Fix 1: Make address_type nullable with default empty string
// The unique constraint is on (client_id, address_type) so we need address_type to have a value
// The controller sets address_type_name but not address_type
// We'll change the column to be nullable and drop the unique constraint,
// then recreate it on (client_id, address_type_id) which is the new pattern

echo "=== Applying fixes ===\n";

try {
    // Drop the old unique constraint
    \DB::statement("ALTER TABLE client_master_addresses DROP INDEX unique_client_address_type");
    echo "Dropped old unique constraint\n";
} catch (\Exception $e) {
    echo "Drop constraint: " . $e->getMessage() . "\n";
}

try {
    // Make address_type nullable with default
    \DB::statement("ALTER TABLE client_master_addresses MODIFY COLUMN address_type varchar(30) NULL DEFAULT ''");
    echo "Made address_type nullable\n";
} catch (\Exception $e) {
    echo "Modify column: " . $e->getMessage() . "\n";
}

try {
    // Add new unique constraint on (client_id, address_type_id) - the new pattern
    \DB::statement("ALTER TABLE client_master_addresses ADD UNIQUE KEY unique_client_address_type_id (client_id, address_type_id)");
    echo "Added new unique constraint on (client_id, address_type_id)\n";
} catch (\Exception $e) {
    echo "Add constraint: " . $e->getMessage() . "\n";
}

echo "\n=== After fix ===\n";
$result = \DB::select("SHOW CREATE TABLE client_master_addresses");
echo $result[0]->{'Create Table'} . "\n";
