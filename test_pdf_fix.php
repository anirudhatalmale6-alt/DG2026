<?php
require_once '/usr/www/users/smartucbmh/application/vendor/autoload.php';
$app = require_once '/usr/www/users/smartucbmh/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

// Clear compiled views first
foreach (glob('/usr/www/users/smartucbmh/application/storage/framework/views/*.php') as $f) { unlink($f); }

use Barryvdh\DomPDF\Facade\Pdf;
use Modules\CIMS_EMP201\Http\Controllers\Emp201Controller;

try {
    $controller = new Emp201Controller();
    $req = Illuminate\Http\Request::create('/cims/emp201/api/statement-data', 'GET', ['client_id' => 16, 'tax_year' => 2026]);
    $resp = $controller->apiStatementData($req);
    $data = json_decode($resp->getContent(), true);

    $pdf = Pdf::loadView('cims_emp201::emp201.statement_pdf', ['data' => $data]);
    $pdf->setPaper('a4', 'portrait');

    // Output to browser for visual check
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="test_empsa.pdf"');
    echo $pdf->output();
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
