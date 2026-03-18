<?php
require_once __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

echo "<h2>Penalty & Interest Fix Test</h2>";

// Find a client with penalty_interest data
$rec = DB::table('cims_emp201_declarations')
    ->where('penalty_interest', '!=', 0)
    ->whereNotNull('penalty_interest')
    ->first();

if (!$rec) {
    echo "<p style='color:red;'>No records with penalty_interest found.</p>";
    exit;
}

echo "<p>Found record ID:{$rec->id} | client_id:{$rec->client_id} | financial_year:{$rec->financial_year} | period_combo:{$rec->period_combo} | penalty_interest:{$rec->penalty_interest}</p>";

// Test the API
$controller = new \Modules\CIMS_EMP201\Http\Controllers\Emp201Controller();
$req = new \Illuminate\Http\Request(['client_id' => $rec->client_id, 'tax_year' => $rec->financial_year]);
$response = $controller->apiStatementData($req);
$data = json_decode($response->getContent(), true);

echo "<h3>Statement API Result:</h3><pre>";
if (isset($data['periods'])) {
    $foundPI = false;
    foreach ($data['periods'] as $period) {
        echo "=== Period: {$period['label']} ===\n";
        foreach ($period['transactions'] as $txn) {
            $marker = ($txn['type'] === 'penalty_interest') ? ' *** PENALTY_INTEREST ***' : '';
            echo sprintf("  %-20s | %-30s | value: %10.2f | balance: %10.2f%s\n", 
                $txn['type'], $txn['description'], $txn['value'], $txn['balance'], $marker);
            if ($txn['type'] === 'penalty_interest') $foundPI = true;
        }
        echo "\n";
    }
    echo "\n";
    if ($foundPI) {
        echo "*** SUCCESS: PENALTIES AND INTEREST line IS appearing in the statement! ***\n";
    } else {
        echo "*** WARNING: PENALTIES AND INTEREST line NOT found - check if the record's period matches the tax year ***\n";
    }
} else {
    echo json_encode($data, JSON_PRETTY_PRINT);
}
echo "</pre>";
echo "<p style='color:green;font-weight:bold;'>Done.</p>";
