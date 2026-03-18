<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

// Get model fillable
$model = new \Modules\CIMS_EMP201\Models\Emp201Declaration();
$fillable = $model->getFillable();
echo "<h3>Model Fillable (" . count($fillable) . " fields):</h3><pre>";
echo implode("\n", $fillable);
echo "</pre>";

// Get all saved records with all fields
echo "<h3>Records detail:</h3><pre>";
$records = DB::table('cims_emp201_declarations')->limit(3)->get();
foreach ($records as $r) {
    $arr = (array) $r;
    foreach ($arr as $k => $v) {
        if ($v !== null && $v !== '' && $v !== '0.00') {
            echo "$k => $v\n";
        }
    }
    echo "===\n";
}
echo "</pre>";

// Check exact column list
echo "<h3>Column names:</h3><pre>";
$cols = DB::select("SHOW COLUMNS FROM cims_emp201_declarations");
$colNames = array_map(function($c) { return $c->Field; }, $cols);
echo implode("\n", $colNames);
echo "</pre>";
