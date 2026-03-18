<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting test...\n";

try {
    // Bootstrap Laravel
    $basePath = __DIR__ . '/application';
    require $basePath . '/vendor/autoload.php';
    $app = require_once $basePath . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle($request = Illuminate\Http\Request::capture());

    echo "Laravel booted OK\n";
    echo "base_path(): " . base_path() . "\n";

    // Check route
    echo "\n--- Route check ---\n";
    $routes = app('router')->getRoutes();
    foreach ($routes as $route) {
        if (strpos($route->getName() ?: '', 'generate-pdf') !== false) {
            echo "Found route: " . $route->getName() . " => " . $route->uri() . " [" . implode(',', $route->methods()) . "]\n";
        }
    }

    // Check DomPDF
    echo "\n--- DomPDF check ---\n";
    $pdf = Barryvdh\DomPDF\Facade\Pdf::loadHTML('<h1>Hello</h1>');
    $content = $pdf->output();
    echo "DomPDF OK, output size: " . strlen($content) . " bytes\n";

    // Check storage path
    echo "\n--- Storage path ---\n";
    $sp = base_path('../storage/client_master_docs/ATP100');
    echo "Path: $sp\n";
    echo "Exists: " . (is_dir($sp) ? 'YES' : 'NO') . "\n";

    // Check Document model
    echo "\n--- Document model ---\n";
    $fn = Modules\cims_pm_pro\Models\Document::generateStoredFilename('ATP100', 'EMPSA - Statement of Account', 'pdf');
    echo "Filename: $fn\n";

    // Test statement_pdf view rendering
    echo "\n--- statement_pdf template ---\n";
    $testData = [
        'client' => [
            'company_name' => 'Test',
            'client_code' => 'ATP100',
            'paye_number' => '7130737883',
            'sdl_number' => 'L123',
            'uif_number' => 'U123',
            'tax_reg_date' => '2020-01-01',
            'address' => ['street_number'=>'1','street_name'=>'Main','suburb'=>'CBD','city'=>'JHB','province'=>'GP','postal_code'=>'2000'],
        ],
        'tax_year' => 2026,
        'today' => '2026/02/28',
        'periods' => [[
            'period_label' => '202601',
            'transactions' => [[
                'type'=>'declaration','date'=>'2025/03/07','reference'=>'TEST','description'=>'DECL',
                'value'=>1000,'paye'=>700,'sdl'=>200,'uif'=>100,'balance'=>1000,
            ]],
            'balance_paye'=>700,'balance_sdl'=>200,'balance_uif'=>100,'balance_total'=>1000,
        ]],
        'summary' => ['prev_year_balance'=>0,'current_year_balance'=>1000,'closing_balance'=>1000],
        'aging' => ['current'=>0,'days30'=>0,'days60'=>0,'days90'=>0,'days120'=>1000,'total'=>1000],
        'compliance' => ['outstanding_emp201'=>'None'],
    ];

    $pdf2 = Barryvdh\DomPDF\Facade\Pdf::loadView('cims_emp201::emp201.statement_pdf', ['data' => $testData]);
    $pdf2->setPaper('a4', 'portrait');
    $out = $pdf2->output();
    echo "Template PDF size: " . strlen($out) . " bytes\n";

    // Save it
    $savePath = base_path('../storage/client_master_docs/ATP100/TEST_EMPSA_AUTOGEN.pdf');
    file_put_contents($savePath, $out);
    echo "Saved to: $savePath\n";
    echo "File exists: " . (file_exists($savePath) ? 'YES (' . filesize($savePath) . ' bytes)' : 'NO') . "\n";

    // Check document viewer route
    echo "\n--- Document viewer ---\n";
    $viewUrl = route('cimsdocmanager.view', 1);
    echo "Viewer URL: $viewUrl\n";

    echo "\n=== ALL TESTS PASSED ===\n";

} catch (Throwable $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo $e->getTraceAsString() . "\n";
}
