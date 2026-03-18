<?php
// Test: Verify penalty_interest is now read correctly in statement data
require_once __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

echo "<h2>Penalty & Interest Fix Test</h2>";

// 1. Check the penalty_interest column exists and has data
$sample = DB::table('cims_emp201_declarations')
    ->whereNotNull('penalty_interest')
    ->where('penalty_interest', '!=', 0)
    ->select('id', 'client_id', 'period', 'penalty_interest', 'penalty', 'interest')
    ->limit(5)
    ->get();

echo "<h3>Records with penalty_interest values:</h3>";
if ($sample->isEmpty()) {
    echo "<p style='color:orange;'>No records found with non-zero penalty_interest. Let me check all records...</p>";
    $allSample = DB::table('cims_emp201_declarations')
        ->select('id', 'client_id', 'period', 'penalty_interest', 'penalty', 'interest')
        ->limit(10)
        ->get();
    echo "<pre>";
    foreach ($allSample as $r) {
        echo "ID:{$r->id} | client:{$r->client_id} | period:{$r->period} | penalty_interest:" . ($r->penalty_interest ?? 'NULL') . " | penalty:" . ($r->penalty ?? 'NULL') . " | interest:" . ($r->interest ?? 'NULL') . "\n";
    }
    echo "</pre>";
} else {
    echo "<pre>";
    foreach ($sample as $r) {
        echo "ID:{$r->id} | client:{$r->client_id} | period:{$r->period} | penalty_interest:{$r->penalty_interest} | penalty:" . ($r->penalty ?? 'NULL') . " | interest:" . ($r->interest ?? 'NULL') . "\n";
    }
    echo "</pre>";
}

// 2. Check the actual column names in the table
$columns = DB::select("SHOW COLUMNS FROM cims_emp201_declarations LIKE '%penalty%'");
echo "<h3>Penalty-related columns:</h3><pre>";
foreach ($columns as $col) {
    echo "Column: {$col->Field} | Type: {$col->Type} | Default: " . ($col->Default ?? 'NULL') . "\n";
}
echo "</pre>";

$columns2 = DB::select("SHOW COLUMNS FROM cims_emp201_declarations LIKE '%interest%'");
echo "<h3>Interest-related columns:</h3><pre>";
foreach ($columns2 as $col) {
    echo "Column: {$col->Field} | Type: {$col->Type} | Default: " . ($col->Default ?? 'NULL') . "\n";
}
echo "</pre>";

// 3. Test the API endpoint with a client that has penalty_interest data
if (!$sample->isEmpty()) {
    $testClient = $sample->first()->client_id;
    $testPeriod = $sample->first()->period;
    $taxYear = substr($testPeriod, 0, 4);
    echo "<h3>Testing API for client_id={$testClient}, tax_year={$taxYear}:</h3>";
    
    $controller = new \Modules\CIMS_EMP201\Http\Controllers\Emp201Controller();
    $req = new \Illuminate\Http\Request(['client_id' => $testClient, 'tax_year' => $taxYear]);
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
    }
    echo "</pre>";
} else {
    echo "<p>No penalty_interest data to test API with.</p>";
}

echo "<p style='color:green;font-weight:bold;'>Test complete.</p>";
