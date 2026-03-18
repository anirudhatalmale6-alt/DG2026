<?php
require_once __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());
use Illuminate\Support\Facades\DB;

$rec = DB::table('cims_emp201_declarations')
    ->where('penalty_interest', '!=', 0)->whereNotNull('penalty_interest')->first();
if (!$rec) { echo "No PI data found"; exit; }

$controller = new \Modules\CIMS_EMP201\Http\Controllers\Emp201Controller();
$req = new \Illuminate\Http\Request(['client_id' => $rec->client_id, 'tax_year' => $rec->financial_year]);
$response = $controller->apiStatementData($req);
$data = json_decode($response->getContent(), true);

echo "<pre>";
foreach ($data['periods'] as $period) {
    echo "=== " . ($period['month_label']) . " | bal_other:" . ($period['balance_other'] ?? 'MISSING') . " ===\n";
    foreach ($period['transactions'] as $txn) {
        $hasOther = isset($txn['other']) ? 'YES' : 'NO';
        echo sprintf("  %-20s | val:%8.2f | paye:%8.2f | sdl:%8.2f | uif:%8.2f | other:%8s | bal:%8.2f\n",
            $txn['description'], $txn['value'], $txn['paye'], $txn['sdl'], $txn['uif'],
            isset($txn['other']) ? number_format($txn['other'],2) : 'MISSING', $txn['balance']);
    }
}
echo "</pre>";
