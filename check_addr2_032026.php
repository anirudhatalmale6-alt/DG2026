<?php
require __DIR__.'/application/vendor/autoload.php';
$app = require_once __DIR__.'/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Find tables with 'address' in the name
echo "=== Tables with 'address' ===\n";
$tables = DB::select("SHOW TABLES LIKE '%address%'");
foreach($tables as $t) {
    $vals = array_values((array)$t);
    echo "  " . $vals[0] . "\n";
}

// Find tables with 'client' in the name
echo "\n=== Tables with 'client' ===\n";
$tables2 = DB::select("SHOW TABLES LIKE '%client%'");
foreach($tables2 as $t) {
    $vals = array_values((array)$t);
    echo "  " . $vals[0] . "\n";
}

// Check client_master columns for address-related fields
echo "\n=== client_master ALL columns ===\n";
$cols = DB::select("SHOW COLUMNS FROM client_master");
foreach($cols as $c) {
    echo "  {$c->Field} ({$c->Type})\n";
}

// Sample client with all fields
echo "\n=== Sample client (client_id=1 or first) ===\n";
$cl = DB::table('client_master')->first();
if ($cl) {
    foreach((array)$cl as $k => $v) {
        $v = is_null($v) ? 'NULL' : (strlen((string)$v) > 100 ? substr($v, 0, 100) . '...' : $v);
        echo "  {$k}: {$v}\n";
    }
}
