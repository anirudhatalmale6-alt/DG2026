<?php
header('Content-Type: text/plain');

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "=== Download path check ===\n";
echo "public_path(): " . public_path() . "\n";
echo "public_path('storage/'): " . public_path('storage/') . "\n\n";

// Get a document to test
$doc = \DB::table('cims_documents')->where('client_code', 'ATP100')->whereNotNull('file_path')->where('file_path', '!=', '')->first();
if ($doc) {
    $downloadPath = public_path("storage/" . $doc->file_path);
    echo "Document: {$doc->file_stored_name}\n";
    echo "file_path: {$doc->file_path}\n";
    echo "Download check: {$downloadPath}\n";
    echo "file_exists: " . (file_exists($downloadPath) ? 'YES' : 'NO') . "\n\n";

    // Check where files actually are
    echo "=== Where files exist ===\n";
    $paths = [
        public_path("storage/" . $doc->file_path),
        storage_path("app/public/" . $doc->file_path),
        '/usr/www/users/smartucbmh/public_html/storage/' . $doc->file_path,
    ];
    foreach ($paths as $p) {
        echo "{$p}\n  exists: " . (file_exists($p) ? 'YES' : 'NO') . "\n";
    }
}

// Check if public/storage directory exists
echo "\n=== public/storage directory ===\n";
$ps = public_path('storage');
echo "Path: {$ps}\n";
echo "exists: " . (file_exists($ps) ? 'YES' : 'NO') . "\n";
echo "is_dir: " . (is_dir($ps) ? 'YES' : 'NO') . "\n";
echo "is_link: " . (is_link($ps) ? 'YES' : 'NO') . "\n";
