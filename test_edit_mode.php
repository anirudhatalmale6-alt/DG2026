<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "<h3>1. Table columns for cims_emp201_declarations:</h3><pre>";
$cols = DB::select("SHOW COLUMNS FROM cims_emp201_declarations");
foreach ($cols as $c) {
    echo $c->Field . " | " . $c->Type . " | Null: " . $c->Null . " | Default: " . ($c->Default ?? 'NULL') . "\n";
}
echo "</pre>";

echo "<h3>2. Existing records:</h3><pre>";
$records = DB::table('cims_emp201_declarations')->limit(5)->get();
foreach ($records as $r) {
    echo "ID: " . $r->id . "\n";
    echo "  client_id: " . ($r->client_id ?? 'NULL') . "\n";
    echo "  company_name: " . ($r->company_name ?? 'NULL') . "\n";
    echo "  financial_year: " . ($r->financial_year ?? 'NULL') . "\n";
    echo "  period_combo: " . ($r->period_combo ?? 'NULL') . "\n";
    echo "  pay_period: " . ($r->pay_period ?? 'NULL') . "\n";
    echo "  payment_period: " . ($r->payment_period ?? 'NULL') . "\n";
    echo "  payment_reference: " . ($r->payment_reference ?? 'NULL') . "\n";
    echo "  check_digit: " . ($r->check_digit ?? 'NULL') . "\n";
    echo "  prepared_by: " . ($r->prepared_by ?? 'NULL') . "\n";
    echo "  first_name: " . ($r->first_name ?? 'NULL') . "\n";
    echo "  surname: " . ($r->surname ?? 'NULL') . "\n";
    echo "  position: " . ($r->position ?? 'NULL') . "\n";
    echo "  paye_number: " . ($r->paye_number ?? 'NULL') . "\n";
    echo "  payment_method: " . ($r->payment_method ?? 'NULL') . "\n";
    echo "  declaration_date: " . ($r->declaration_date ?? 'NULL') . "\n";
    // Check if period_id column exists
    echo "  period_id: " . (property_exists($r, 'period_id') ? ($r->period_id ?? 'NULL') : 'COLUMN NOT EXISTS') . "\n";
    echo "---\n";
}
echo "</pre>";

// Check if period_id column exists
echo "<h3>3. Does period_id column exist?</h3><pre>";
echo Schema::hasColumn('cims_emp201_declarations', 'period_id') ? "YES" : "NO";
echo "</pre>";

// Check fillable on model
echo "<h3>4. Model fillable check:</h3><pre>";
try {
    $model = new \Modules\CIMS_EMP201\Models\Emp201Declaration();
    echo "Fillable: " . implode(', ', $model->getFillable()) . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "</pre>";
