<?php
require_once __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());
use Illuminate\Support\Facades\DB;

echo "<h2>Penalty & Interest Fix Test</h2>";

$rec = DB::table('cims_emp201_declarations')
    ->where('penalty_interest', '!=', 0)
    ->whereNotNull('penalty_interest')
    ->first();

if (!$rec) { echo "<p style='color:red;'>No records with penalty_interest found.</p>"; exit; }

echo "<p>Record: ID:{$rec->id} client:{$rec->client_id} year:{$rec->financial_year} combo:{$rec->period_combo} PI:{$rec->penalty_interest}</p>";

$controller = new \Modules\CIMS_EMP201\Http\Controllers\Emp201Controller();
$req = new \Illuminate\Http\Request(['client_id' => $rec->client_id, 'tax_year' => $rec->financial_year]);
$response = $controller->apiStatementData($req);
$data = json_decode($response->getContent(), true);

echo "<pre>";
$foundPI = false;
if (isset($data['periods'])) {
    foreach ($data['periods'] as $period) {
        echo "=== " . ($period['month_label'] ?? $period['period_label'] ?? '?') . " ===\n";
        foreach ($period['transactions'] as $txn) {
            $marker = ($txn['type'] === 'penalty_interest') ? ' <<< FOUND!' : '';
            echo sprintf("  %-20s | %-30s | val:%10.2f | bal:%10.2f%s\n", $txn['type'], $txn['description'], $txn['value'], $txn['balance'], $marker);
            if ($txn['type'] === 'penalty_interest') $foundPI = true;
        }
        echo "\n";
    }
}
echo $foundPI ? "\n*** SUCCESS: Penalties and Interest line IS showing! ***\n" : "\n*** FAIL: Not found ***\n";
echo "</pre>";
