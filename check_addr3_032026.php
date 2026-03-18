<?php
require __DIR__.'/application/vendor/autoload.php';
$app = require_once __DIR__.'/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check cims_client_addresses
echo "=== cims_client_addresses columns ===\n";
$cols = DB::select("SHOW COLUMNS FROM cims_client_addresses");
foreach($cols as $c) {
    echo "  {$c->Field} ({$c->Type}) null={$c->Null}\n";
}

echo "\n=== cims_client_addresses sample (first 3) ===\n";
$rows = DB::table('cims_client_addresses')->limit(3)->get();
foreach($rows as $r) {
    foreach((array)$r as $k => $v) {
        $v = is_null($v) ? 'NULL' : (strlen((string)$v) > 100 ? substr($v, 0, 100) . '...' : $v);
        echo "  {$k}: {$v}\n";
    }
    echo "  ---\n";
}

// Also check client_master_addresses
echo "\n=== client_master_addresses columns ===\n";
$cols2 = DB::select("SHOW COLUMNS FROM client_master_addresses");
foreach($cols2 as $c) {
    echo "  {$c->Field} ({$c->Type}) null={$c->Null}\n";
}

echo "\n=== client_master_addresses sample (first 3) ===\n";
$rows2 = DB::table('client_master_addresses')->limit(3)->get();
foreach($rows2 as $r) {
    foreach((array)$r as $k => $v) {
        $v = is_null($v) ? 'NULL' : (strlen((string)$v) > 100 ? substr($v, 0, 100) . '...' : $v);
        echo "  {$k}: {$v}\n";
    }
    echo "  ---\n";
}

// client_master contact columns
echo "\n=== client_master phone/email fields for ATP100 ===\n";
$cl = DB::table('client_master')->where('client_code', 'ATP100')->first();
if ($cl) {
    echo "  telephone: " . ($cl->telephone ?? 'NULL') . "\n";
    echo "  telephone_business: " . ($cl->telephone_business ?? 'NULL') . "\n";
    echo "  mobile: " . ($cl->mobile ?? 'NULL') . "\n";
    echo "  email: " . ($cl->email ?? 'NULL') . "\n";
    echo "  website: " . ($cl->website ?? 'NULL') . "\n";
}
