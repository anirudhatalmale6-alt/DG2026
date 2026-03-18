<?php
/**
 * Test script: Verify the generateStatementPdf route and DomPDF generation
 */

// Bootstrap Laravel
require __DIR__ . '/../application/vendor/autoload.php';
$app = require_once __DIR__ . '/../application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "=== Testing EMPSA PDF Generation ===\n\n";

// 1. Check route exists
echo "1. Checking route 'cimsemp201.statement.generate-pdf'...\n";
try {
    $url = route('cimsemp201.statement.generate-pdf');
    echo "   Route URL: {$url}\n";
    echo "   OK\n\n";
} catch (Exception $e) {
    echo "   FAILED: " . $e->getMessage() . "\n\n";
}

// 2. Check DomPDF is available
echo "2. Checking DomPDF...\n";
try {
    $pdf = Barryvdh\DomPDF\Facade\Pdf::loadHTML('<h1>Test</h1>');
    echo "   DomPDF loaded OK\n\n";
} catch (Exception $e) {
    echo "   FAILED: " . $e->getMessage() . "\n\n";
}

// 3. Check document viewer route
echo "3. Checking document viewer route...\n";
try {
    $viewUrl = route('cimsdocmanager.view', 1);
    echo "   Viewer URL: {$viewUrl}\n";
    echo "   OK\n\n";
} catch (Exception $e) {
    echo "   FAILED: " . $e->getMessage() . "\n\n";
}

// 4. Check storage path
echo "4. Checking storage path...\n";
$storagePath = base_path('../storage/client_master_docs/ATP100');
echo "   Storage path: {$storagePath}\n";
echo "   Exists: " . (is_dir($storagePath) ? 'YES' : 'NO') . "\n\n";

// 5. Check Document model
echo "5. Checking Document model...\n";
try {
    $docCount = Modules\cims_pm_pro\Models\Document::count();
    echo "   Document count: {$docCount}\n";
    echo "   OK\n\n";
} catch (Exception $e) {
    echo "   FAILED: " . $e->getMessage() . "\n\n";
}

// 6. Test filename generation
echo "6. Testing filename generation...\n";
$filename = Modules\cims_pm_pro\Models\Document::generateStoredFilename('ATP100', 'EMPSA - Statement of Account', 'pdf');
echo "   Generated: {$filename}\n\n";

// 7. Try generating PDF with the actual statement template
echo "7. Testing statement_pdf template rendering...\n";
try {
    // Build minimal test data
    $testData = [
        'client' => [
            'company_name' => 'Test Client',
            'client_code' => 'ATP100',
            'paye_number' => '7130737883',
            'sdl_number' => 'L123456',
            'uif_number' => 'U123456',
            'tax_reg_date' => '2020-01-01',
            'address' => [
                'street_number' => '123',
                'street_name' => 'Test Street',
                'suburb' => 'Test Suburb',
                'city' => 'Test City',
                'province' => 'Test Province',
                'postal_code' => '1234',
            ],
        ],
        'tax_year' => 2026,
        'today' => '2026/02/28',
        'periods' => [
            [
                'period_label' => '202601',
                'month_label' => 'Mar 2025',
                'transactions' => [
                    [
                        'type' => 'declaration',
                        'date' => '2025/03/07',
                        'reference' => '7130737883LX202601',
                        'description' => 'EMP201 DECLARATION',
                        'value' => 15000.00,
                        'paye' => 10000.00,
                        'sdl' => 3000.00,
                        'uif' => 2000.00,
                        'balance' => 15000.00,
                    ],
                ],
                'balance_paye' => 10000.00,
                'balance_sdl' => 3000.00,
                'balance_uif' => 2000.00,
                'balance_total' => 15000.00,
            ],
        ],
        'summary' => [
            'prev_year_balance' => 0,
            'current_year_balance' => 15000.00,
            'closing_balance' => 15000.00,
        ],
        'aging' => [
            'current' => 0,
            'days30' => 0,
            'days60' => 0,
            'days90' => 0,
            'days120' => 15000.00,
            'total' => 15000.00,
        ],
        'compliance' => [
            'outstanding_emp201' => 'None',
        ],
    ];

    $pdf = Barryvdh\DomPDF\Facade\Pdf::loadView('cims_emp201::emp201.statement_pdf', ['data' => $testData]);
    $pdf->setPaper('a4', 'portrait');
    $content = $pdf->output();
    $size = strlen($content);
    echo "   PDF generated: {$size} bytes\n";

    // Save test PDF
    $testPath = base_path('../storage/client_master_docs/ATP100/test_empsa_gen.pdf');
    file_put_contents($testPath, $content);
    echo "   Saved to: {$testPath}\n";
    echo "   File exists: " . (file_exists($testPath) ? 'YES' : 'NO') . "\n";
    echo "   OK\n\n";

} catch (Exception $e) {
    echo "   FAILED: " . $e->getMessage() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n\n";
}

echo "=== Done ===\n";
