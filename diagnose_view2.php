<?php
// Simple diagnostic - no Laravel bootstrap needed
header('Content-Type: text/plain');

echo "=== Storage Path Diagnostic ===\n\n";

// Check what storage_path would resolve to
$basePath = realpath(__DIR__ . '/application');
echo "Base application path: " . ($basePath ?: 'NOT FOUND') . "\n";
echo "Expected storage: {$basePath}/storage\n";
echo "Expected client_docs: {$basePath}/storage/app/public/client_docs\n\n";

$storagePath = __DIR__ . '/application/storage/app/public/client_docs';
echo "client_docs path: {$storagePath}\n";
echo "client_docs exists: " . (is_dir($storagePath) ? 'YES' : 'NO') . "\n\n";

if (is_dir($storagePath)) {
    echo "=== Client folders in client_docs ===\n";
    $dirs = scandir($storagePath);
    foreach ($dirs as $d) {
        if ($d != '.' && $d != '..') {
            echo "  - {$d}";
            $subPath = $storagePath . '/' . $d;
            if (is_dir($subPath)) {
                $files = scandir($subPath);
                $fileCount = count(array_filter($files, fn($f) => $f != '.' && $f != '..'));
                echo " ({$fileCount} files)";
            }
            echo "\n";
        }
    }
}

echo "\n=== Check storage symlink ===\n";
$publicStorage = __DIR__ . '/public/storage';
echo "public/storage path: {$publicStorage}\n";
echo "public/storage exists: " . (file_exists($publicStorage) ? 'YES' : 'NO') . "\n";
echo "public/storage is_link: " . (is_link($publicStorage) ? 'YES' : 'NO') . "\n";
if (is_link($publicStorage)) {
    echo "Symlink target: " . readlink($publicStorage) . "\n";
}

$storageLink2 = __DIR__ . '/storage';
echo "\nstorage (root level) exists: " . (file_exists($storageLink2) ? 'YES' : 'NO') . "\n";
echo "storage is_link: " . (is_link($storageLink2) ? 'YES' : 'NO') . "\n";
if (is_link($storageLink2)) {
    echo "Symlink target: " . readlink($storageLink2) . "\n";
}

echo "\n=== Database check via Laravel ===\n";
try {
    require __DIR__ . '/application/vendor/autoload.php';
    $app = require_once __DIR__ . '/application/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->handle(Illuminate\Http\Request::capture());

    echo "storage_path(): " . storage_path() . "\n";
    echo "storage_path('app/public/'): " . storage_path('app/public/') . "\n";
    echo "public_path(): " . public_path() . "\n\n";

    // Check DB for documents
    $documents = \DB::table('cims_documents')->limit(5)->get();
    echo "=== First 5 documents in cims_documents ===\n";
    if ($documents->isEmpty()) {
        echo "  TABLE IS EMPTY - no document records!\n";
    } else {
        foreach ($documents as $doc) {
            echo "  ID: {$doc->id}, client_id: {$doc->client_id}, file_path: {$doc->file_path}, file_stored_name: {$doc->file_stored_name}\n";
        }
    }

    echo "\n=== Total document count ===\n";
    $count = \DB::table('cims_documents')->count();
    echo "Total documents: {$count}\n";

    // Check a client record
    echo "\n=== Client records with certificate fields ===\n";
    $clients = \DB::table('cims_client_master')->select('client_id', 'client_code', 'company_name', 'cor_14_3_certificate', 'income_tax_registration')->limit(5)->get();
    foreach ($clients as $c) {
        echo "  Client {$c->client_id} ({$c->client_code}): cor_14_3_certificate=" . ($c->cor_14_3_certificate ?? 'NULL') . ", income_tax=" . ($c->income_tax_registration ?? 'NULL') . "\n";
    }

} catch (\Exception $e) {
    echo "Laravel bootstrap error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
