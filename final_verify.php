<?php
header('Content-Type: text/plain');

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "=== Final verification ===\n\n";

// 1. Simulate view_client for client 13, document "cor_14_3_certificate"
echo "--- Simulating view_client(13, 'cor_14_3_certificate') ---\n";
$client = \Modules\cims_pm_pro\Models\ClientMaster::find(13);
if (!$client) {
    echo "Client 13 not found!\n";
} else {
    echo "Client: {$client->client_code} - {$client->company_name}\n";
    $documentField = 'cor_14_3_certificate';
    $storedName = $client->{$documentField};
    echo "cor_14_3_certificate value: '{$storedName}'\n";

    if ($storedName) {
        $document = \Modules\cims_pm_pro\Models\Document::where([
            'client_id' => $client->client_id,
            'file_stored_name' => $storedName
        ])->first();

        if ($document) {
            echo "Document found: id={$document->id}\n";
            echo "file_path: {$document->file_path}\n";

            $filePath = storage_path('app/public/' . $document->file_path);
            echo "Full path: {$filePath}\n";
            echo "file_exists: " . (file_exists($filePath) ? 'YES - WILL WORK!' : 'NO - STILL BROKEN') . "\n";

            // Check web URL
            $webUrl = 'https://smartweigh.co.za/storage/' . $document->file_path;
            echo "Web URL: {$webUrl}\n";
        } else {
            echo "Document NOT found in cims_documents!\n";
        }
    } else {
        echo "Field is empty/null\n";
    }
}

// 2. Check route exists
echo "\n--- Route checks ---\n";
$routes = ['cimsdocmanager.view.client', 'cimsdocmanager.view', 'cimsdocmanager.download', 'cimsdocmanager.index'];
foreach ($routes as $r) {
    $route = \Route::getRoutes()->getByName($r);
    echo "{$r}: " . ($route ? "EXISTS ({$route->uri()})" : "MISSING") . "\n";
}

// 3. Check new filesystem config
echo "\n--- Filesystem public disk ---\n";
echo "root: " . config('filesystems.disks.public.root') . "\n";

// 4. Test that new uploads will also work
echo "\n--- Upload path test ---\n";
$testPath = \Illuminate\Support\Facades\Storage::disk('public')->path('client_docs/ATP100/test.txt');
echo "Storage::disk('public')->path('client_docs/ATP100/test.txt'): {$testPath}\n";
echo "matches storage_path: " . (strpos($testPath, storage_path('app/public')) === 0 ? 'YES' : 'NO') . "\n";
