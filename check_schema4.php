<?php
error_reporting(0);
define('LARAVEL_START', microtime(true));
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "<pre>";

// Check tblcustomfieldsvalues for client code
echo "=== CUSTOM FIELD DEFINITIONS ===\n";
$fields = \DB::table('tblcustomfields')->get();
foreach ($fields as $f) {
    $arr = (array)$f;
    echo json_encode($arr) . "\n";
}

echo "\n=== CUSTOM FIELD VALUES (first 10) ===\n";
$vals = \DB::table('tblcustomfieldsvalues')->limit(10)->get();
foreach ($vals as $v) {
    $arr = (array)$v;
    echo json_encode($arr) . "\n";
}

// Check customfields table
echo "\n=== CUSTOMFIELDS TABLE ===\n";
$cfs = \DB::table('customfields')->limit(10)->get();
foreach ($cfs as $cf) {
    $arr = (array)$cf;
    echo json_encode($arr) . "\n";
}

// Invoice bill_status meaning
echo "\n=== INVOICE STATUS MAPPING ===\n";
echo "1 = Draft\n";
echo "2 = Due (published)\n";
echo "3 = Overdue\n";
echo "4 = Partially Paid\n";
echo "5 = Paid\n";
echo "6 = Cancelled\n";

echo "</pre>";
