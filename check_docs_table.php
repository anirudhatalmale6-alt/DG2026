<?php
$basePaths = [__DIR__ . '/../application', __DIR__ . '/../../application', __DIR__ . '/..'];
$bootstrapped = false;
foreach ($basePaths as $base) {
    if (file_exists($base . '/bootstrap/app.php')) {
        if (file_exists($base . '/bootstrap/autoload.php')) require $base . '/bootstrap/autoload.php';
        elseif (file_exists($base . '/vendor/autoload.php')) require $base . '/vendor/autoload.php';
        $app = require_once $base . '/bootstrap/app.php';
        $bootstrapped = true;
        break;
    }
}
if (!$bootstrapped) die("Could not find Laravel bootstrap.");
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "<pre>\n=== DOCUMENT UPLOAD DIAGNOSTICS ===\n\n";

// 1. Check cims_documents columns
echo "=== cims_documents COLUMNS ===\n";
$cols = Schema::getColumnListing('cims_documents');
foreach ($cols as $col) {
    echo "  $col\n";
}

// 2. Check Document model fillable
echo "\n=== Document model fillable ===\n";
$model = new \Modules\ClientMasterNew\Models\Document();
echo "  Fillable: " . implode(', ', $model->getFillable()) . "\n";

// 3. Check if file_size column exists and its type
echo "\n=== Column details for file_size ===\n";
if (Schema::hasColumn('cims_documents', 'file_size')) {
    echo "  file_size column EXISTS\n";
} else {
    echo "  file_size column MISSING!\n";
}

// 4. Check storage write
echo "\n=== Storage test ===\n";
$storageDisk = \Illuminate\Support\Facades\Storage::disk('public');
try {
    $storageDisk->put('client_docs/TEST_UPLOAD/test.txt', 'test');
    $path = $storageDisk->path('client_docs/TEST_UPLOAD/test.txt');
    echo "  Write test: SUCCESS at $path\n";
    echo "  File exists: " . (file_exists($path) ? 'YES' : 'NO') . "\n";
    $storageDisk->deleteDirectory('client_docs/TEST_UPLOAD');
} catch (\Exception $e) {
    echo "  Write test FAILED: " . $e->getMessage() . "\n";
}

// 5. Try to simulate what uploadDocument does - check the Document::create call
echo "\n=== Simulated Document::create() ===\n";
$testData = [
    'client_id' => 1,
    'client_code' => 'TEST',
    'title' => 'test_file.pdf',
    'document_ref' => 'SARS',
    'document_code' => 'ITAX REG',
    'doc_group' => 'SARS',
    'category_id' => 1,
    'type_id' => 24,
    'file_original_name' => 'original.pdf',
    'file_stored_name' => 'stored.pdf',
    'file_path' => 'client_docs/TEST/stored.pdf',
    'file_size' => 12345,
    'file_mime_type' => 'application/pdf',
    'created_at' => now(),
    'updated_at' => now(),
];

// Check which fields from testData don't exist as columns
echo "  Fields vs columns check:\n";
foreach ($testData as $key => $value) {
    $hasCol = Schema::hasColumn('cims_documents', $key);
    echo "    $key: " . ($hasCol ? "OK" : "MISSING COLUMN!") . "\n";
}

// 6. Check the actual Laravel log for recent errors
echo "\n=== Recent Laravel Log Errors ===\n";
$logPath = storage_path('logs/laravel.log');
if (file_exists($logPath)) {
    $log = file_get_contents($logPath);
    // Get last 3000 chars
    $tail = substr($log, -5000);
    // Find error lines
    preg_match_all('/\[[\d\-\s:]+\].*ERROR.*/', $tail, $matches);
    if (!empty($matches[0])) {
        foreach (array_slice($matches[0], -5) as $line) {
            echo "  " . substr($line, 0, 300) . "\n\n";
        }
    } else {
        echo "  No recent ERROR entries found in last 5000 chars\n";
    }
} else {
    echo "  Log file not found at: $logPath\n";
}

echo "\n=== DONE ===\n</pre>";
