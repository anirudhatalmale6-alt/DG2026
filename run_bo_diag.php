<?php
header('Content-Type: text/plain');

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CIMS DIAGNOSTIC: Sample Person id=1 from cims_persons ===\n\n";

$person = DB::table('cims_persons')->where('id', 1)->first();
if ($person) {
    $data = (array) $person;
    echo "--- NON-NULL/NON-EMPTY VALUES ---\n";
    $nonNull = 0;
    foreach ($data as $key => $val) {
        if ($val !== null && $val !== '') {
            echo "  $key = " . (strlen((string)$val) > 150 ? substr((string)$val, 0, 150) . '...' : $val) . "\n";
            $nonNull++;
        }
    }
    echo "\n  Total non-null: $nonNull / " . count($data) . "\n";

    echo "\n--- NULL/EMPTY VALUES ---\n";
    foreach ($data as $key => $val) {
        if ($val === null || $val === '') {
            echo "  $key = " . ($val === null ? 'NULL' : "''") . "\n";
        }
    }
} else {
    echo "  No record with id=1\n";
    $first = DB::table('cims_persons')->first();
    if ($first) {
        echo "  First available: id=" . $first->id . " (" . ($first->firstname ?? '') . " " . ($first->surname ?? '') . ")\n";
    }
}

// Also check cims_addresses and client_master_addresses structure
echo "\n" . str_repeat('=', 60) . "\n";
echo "TABLE: cims_addresses (22 rows)\n";
echo str_repeat('=', 60) . "\n";
$cols = DB::select("SHOW COLUMNS FROM cims_addresses");
foreach ($cols as $c) {
    echo "  {$c->Field} ({$c->Type})" . ($c->Null === 'YES' ? ' NULL' : ' NOT NULL') . "\n";
}
$sample = DB::table('cims_addresses')->first();
if ($sample) {
    echo "\n--- Sample record ---\n";
    foreach ((array)$sample as $k => $v) {
        if ($v !== null && $v !== '') echo "  $k = $v\n";
    }
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "TABLE: client_master_addresses (22 rows)\n";
echo str_repeat('=', 60) . "\n";
$cols2 = DB::select("SHOW COLUMNS FROM client_master_addresses");
foreach ($cols2 as $c) {
    echo "  {$c->Field} ({$c->Type})" . ($c->Null === 'YES' ? ' NULL' : ' NOT NULL') . "\n";
}
$sample2 = DB::table('client_master_addresses')->first();
if ($sample2) {
    echo "\n--- Sample record ---\n";
    foreach ((array)$sample2 as $k => $v) {
        if ($v !== null && $v !== '') echo "  $k = $v\n";
    }
}

echo "\n=== DONE ===\n";
