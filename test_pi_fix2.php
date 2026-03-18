<?php
require_once __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

echo "<h2>Penalty & Interest Fix Test</h2>";

// 1. Check actual columns in the table
$columns = DB::select("SHOW COLUMNS FROM cims_emp201_declarations");
echo "<h3>All columns:</h3><pre>";
foreach ($columns as $col) {
    echo "{$col->Field} ({$col->Type})\n";
}
echo "</pre>";

// 2. Find records with penalty_interest data
$sample = DB::table('cims_emp201_declarations')
    ->whereNotNull('penalty_interest')
    ->where('penalty_interest', '!=', 0)
    ->limit(5)
    ->get();

echo "<h3>Records with penalty_interest values (" . $sample->count() . " found):</h3><pre>";
if ($sample->isEmpty()) {
    echo "None found. Checking all records...\n";
    $all = DB::table('cims_emp201_declarations')->limit(5)->get();
    foreach ($all as $r) {
        echo "ID:{$r->id} | client_id:{$r->client_id} | penalty_interest:" . ($r->penalty_interest ?? 'NULL') . "\n";
    }
} else {
    foreach ($sample as $r) {
        $period = $r->tax_period ?? $r->emp201_period ?? 'unknown';
        echo "ID:{$r->id} | client:{$r->client_id} | penalty_interest:{$r->penalty_interest}\n";
    }
}
echo "</pre>";

// 3. Test API with first available client
$firstRec = DB::table('cims_emp201_declarations')->first();
if ($firstRec) {
    $clientId = $firstRec->client_id;
    // Find tax year from declarations
    $years = DB::table('cims_emp201_declarations')
        ->where('client_id', $clientId)
        ->select('tax_year')
        ->distinct()
        ->pluck('tax_year');
    
    echo "<h3>Available tax years for client {$clientId}: " . $years->implode(', ') . "</h3>";
    
    if ($years->isNotEmpty()) {
        $taxYear = $years->first();
        echo "<h3>API test for client={$clientId}, year={$taxYear}:</h3>";
        
        $controller = new \Modules\CIMS_EMP201\Http\Controllers\Emp201Controller();
        $req = new \Illuminate\Http\Request(['client_id' => $clientId, 'tax_year' => $taxYear]);
        $response = $controller->apiStatementData($req);
        $data = json_decode($response->getContent(), true);
        
        echo "<pre>";
        if (isset($data['periods'])) {
            foreach ($data['periods'] as $period) {
                echo "Period: {$period['label']}\n";
                foreach ($period['transactions'] as $txn) {
                    echo "  {$txn['type']} | {$txn['description']} | value:{$txn['value']} | balance:{$txn['balance']}\n";
                }
                echo "\n";
            }
        } else {
            echo json_encode($data, JSON_PRETTY_PRINT);
        }
        echo "</pre>";
    }
}

echo "<p style='color:green;font-weight:bold;'>Done.</p>";
