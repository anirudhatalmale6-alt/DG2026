<?php
require __DIR__.'/application/vendor/autoload.php';
$app = require_once __DIR__.'/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check client_addresses table structure
echo "=== client_addresses columns ===\n";
$cols = DB::select("SHOW COLUMNS FROM client_addresses");
foreach($cols as $c) {
    echo "  {$c->Field} ({$c->Type}) {$c->Null} {$c->Default}\n";
}

// Sample data
echo "\n=== Sample client_addresses (first 3) ===\n";
$addrs = DB::table('client_addresses')->limit(3)->get();
foreach($addrs as $a) {
    foreach((array)$a as $k => $v) {
        $v = is_null($v) ? 'NULL' : (strlen((string)$v) > 80 ? substr($v, 0, 80) . '...' : $v);
        echo "  {$k}: {$v}\n";
    }
    echo "  ---\n";
}

// Check client_master for phone/email columns
echo "\n=== client_master columns (phone/email/tel related) ===\n";
$cols2 = DB::select("SHOW COLUMNS FROM client_master");
foreach($cols2 as $c) {
    if (preg_match('/phone|tel|email|fax|mobile|contact/i', $c->Field)) {
        echo "  {$c->Field} ({$c->Type})\n";
    }
}

// Sample client_master contact data
echo "\n=== Sample client_master contact fields (first 3) ===\n";
$clients = DB::table('client_master')->select('client_id','client_code','company_name','telephone','email','fax')->limit(3)->get();
foreach($clients as $cl) {
    foreach((array)$cl as $k => $v) {
        echo "  {$k}: " . ($v ?: 'NULL') . "\n";
    }
    echo "  ---\n";
}
