<?php
require_once '/usr/www/users/smartucbmh/application/vendor/autoload.php';
$app = require_once '/usr/www/users/smartucbmh/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Barryvdh\DomPDF\Facade\Pdf;
use Modules\CIMS_EMP201\Http\Controllers\Emp201Controller;

try {
    // Get statement data
    $controller = new Emp201Controller();
    $req = Illuminate\Http\Request::create('/cims/emp201/api/statement-data', 'GET', ['client_id' => 16, 'tax_year' => 2026]);
    $resp = $controller->apiStatementData($req);
    $data = json_decode($resp->getContent(), true);

    echo "Data loaded OK. Periods: " . count($data['periods']) . "\n";

    // Test PDF generation
    $pdf = Pdf::loadView('cims_emp201::emp201.statement_pdf', ['data' => $data]);
    $pdf->setPaper('a4', 'portrait');
    $tempPath = storage_path('app/test_empsa.pdf');
    $pdf->save($tempPath);

    if (file_exists($tempPath)) {
        echo "PDF generated OK! Size: " . filesize($tempPath) . " bytes\n";
        unlink($tempPath);
    } else {
        echo "PDF file NOT created\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
