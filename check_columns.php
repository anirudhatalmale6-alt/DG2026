<?php
$basePaths = [__DIR__ . '/../application', __DIR__ . '/../../application', __DIR__ . '/..'];
$bootstrapped = false;
foreach ($basePaths as $base) {
    if (file_exists($base . '/bootstrap/app.php')) {
        if (file_exists($base . '/bootstrap/autoload.php')) require $base . '/bootstrap/autoload.php';
        elseif (file_exists($base . '/vendor/autoload.php')) require $base . '/vendor/autoload.php';
        $app = require_once $base . '/bootstrap/app.php';
        $bootstrapped = true;
        break;
    }
}
if (!$bootstrapped) die("Could not find Laravel bootstrap.");
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "<pre>\n=== CLIENT_MASTER TABLE COLUMNS ===\n\n";
$cols = Schema::getColumnListing('client_master');
foreach ($cols as $col) {
    echo "  $col\n";
}

// Check for any saved clients
echo "\n=== SAMPLE CLIENT DATA ===\n";
$client = DB::table('client_master')->first();
if ($client) {
    foreach ((array) $client as $key => $value) {
        echo "  $key: " . ($value !== null ? substr((string) $value, 0, 80) : 'NULL') . "\n";
    }
} else {
    echo "  No clients in database.\n";
}

// Check client_master_addresses columns
echo "\n=== CLIENT_MASTER_ADDRESSES COLUMNS ===\n";
$cols = Schema::getColumnListing('client_master_addresses');
foreach ($cols as $col) {
    echo "  $col\n";
}

// Check client_master_directors columns
echo "\n=== CLIENT_MASTER_DIRECTORS COLUMNS ===\n";
$cols = Schema::getColumnListing('client_master_directors');
foreach ($cols as $col) {
    echo "  $col\n";
}

// Check client_master_banks columns
echo "\n=== CLIENT_MASTER_BANKS COLUMNS ===\n";
$cols = Schema::getColumnListing('client_master_banks');
foreach ($cols as $col) {
    echo "  $col\n";
}

echo "\n=== DONE ===\n</pre>";
