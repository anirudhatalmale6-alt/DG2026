<?php
require __DIR__.'/application/vendor/autoload.php';
$app = require_once __DIR__.'/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check cims_addresses table
echo "=== cims_addresses columns ===\n";
$cols = DB::select("SHOW COLUMNS FROM cims_addresses");
foreach($cols as $c) {
    echo "  {$c->Field} ({$c->Type})\n";
}

echo "\n=== cims_addresses sample (first 5) ===\n";
$rows = DB::table('cims_addresses')->limit(5)->get();
foreach($rows as $r) {
    foreach((array)$r as $k => $v) {
        $v = is_null($v) ? 'NULL' : (strlen((string)$v) > 120 ? substr($v, 0, 120) . '...' : $v);
        echo "  {$k}: {$v}\n";
    }
    echo "  ---\n";
}

// Check what address_id=4 has (the one linked from client_master_addresses for ATP100)
echo "\n=== cims_addresses where id=4 ===\n";
$addr = DB::table('cims_addresses')->where('id', 4)->first();
if ($addr) {
    foreach((array)$addr as $k => $v) {
        $v = is_null($v) ? 'NULL' : (strlen((string)$v) > 120 ? substr($v, 0, 120) . '...' : $v);
        echo "  {$k}: {$v}\n";
    }
}

// Also check cims_client_addresses for ATP100 client_id
echo "\n=== cims_client_addresses for client_id matching ATP100 ===\n";
$cl = DB::table('client_master')->where('client_code', 'ATP100')->first();
if ($cl) {
    echo "ATP100 client_id = {$cl->client_id}\n";
    $caddrs = DB::table('cims_client_addresses')->where('client_id', $cl->client_id)->get();
    foreach($caddrs as $ca) {
        foreach((array)$ca as $k => $v) {
            echo "  {$k}: " . ($v ?? 'NULL') . "\n";
        }
        echo "  ---\n";
        // Look up the address
        if ($ca->address_id) {
            $addr2 = DB::table('cims_addresses')->where('id', $ca->address_id)->first();
            if ($addr2) {
                echo "  >> Linked address:\n";
                foreach((array)$addr2 as $k2 => $v2) {
                    $v2 = is_null($v2) ? 'NULL' : (strlen((string)$v2) > 120 ? substr($v2, 0, 120) . '...' : $v2);
                    echo "    {$k2}: {$v2}\n";
                }
            }
        }
    }
}

// Check client_master phone fields
echo "\n=== client_master phone-related columns ===\n";
$cols3 = DB::select("SHOW COLUMNS FROM client_master");
foreach($cols3 as $c) {
    if (preg_match('/phone|tel|mobile|fax|email|contact|web/i', $c->Field)) {
        // Get value for ATP100
        $val = DB::table('client_master')->where('client_code', 'ATP100')->value($c->Field);
        echo "  {$c->Field}: " . ($val ?: 'NULL') . "\n";
    }
}
