<?php
header('Content-Type: text/plain');

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

// 1. Check what asset() generates
$doc = \DB::table('cims_documents')->where('client_code', 'ATP100')->whereNotNull('file_path')->where('file_path', '!=', '')->first();
if ($doc) {
    echo "=== Asset URL generation ===\n";
    $assetUrl = asset('storage/' . $doc->file_path);
    echo "asset('storage/' . file_path): {$assetUrl}\n\n";

    // Also check storage_path
    $storagePath = storage_path('app/public/' . $doc->file_path);
    echo "storage_path check: {$storagePath}\n";
    echo "file_exists: " . (file_exists($storagePath) ? 'YES' : 'NO') . "\n\n";
}

// 2. Check web root structure
echo "=== Web root storage check ===\n";
$paths = [
    '/usr/www/users/smartucbmh/public_html/storage/client_docs/ATP100',
    '/usr/www/users/smartucbmh/storage/client_docs/ATP100',
];
foreach ($paths as $p) {
    echo "{$p}\n";
    echo "  exists: " . (is_dir($p) ? 'YES' : 'NO') . "\n";
    if (is_dir($p)) {
        $files = array_diff(scandir($p), ['.', '..']);
        echo "  files: " . count($files) . "\n";
    }
}

// 3. Check .htaccess for rewrites
echo "\n=== .htaccess check ===\n";
$htaccess = '/usr/www/users/smartucbmh/public_html/.htaccess';
if (file_exists($htaccess)) {
    echo "public_html/.htaccess exists\n";
    echo file_get_contents($htaccess) . "\n";
} else {
    echo "No .htaccess in public_html\n";
}

// Also check if there's an .htaccess in /storage/
$storageHtaccess = '/usr/www/users/smartucbmh/public_html/storage/.htaccess';
if (file_exists($storageHtaccess)) {
    echo "\nstorage/.htaccess exists:\n";
    echo file_get_contents($storageHtaccess) . "\n";
} else {
    echo "\nNo .htaccess in storage/\n";
}

// 4. Check the URL config
echo "\n=== APP_URL config ===\n";
echo "config('app.url'): " . config('app.url') . "\n";
echo "url('/'): " . url('/') . "\n";
echo "asset('test'): " . asset('test') . "\n";
