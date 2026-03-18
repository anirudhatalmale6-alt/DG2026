<?php
header('Content-Type: text/plain');

try {
    require __DIR__ . '/application/vendor/autoload.php';
    $app = require_once __DIR__ . '/application/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->handle(Illuminate\Http\Request::capture());

    echo "=== Path comparison ===\n";
    echo "storage_path(): " . storage_path() . "\n";
    echo "storage_path('app/public/'): " . storage_path('app/public/') . "\n";
    echo "Public disk root: " . config('filesystems.disks.public.root') . "\n\n";

    echo "Controller checks: " . storage_path('app/public/') . "client_docs/ATP100/...\n";
    echo "Files are at: " . config('filesystems.disks.public.root') . "/client_docs/ATP100/...\n\n";

    // Check if storage_path('app/public') exists
    $expected = storage_path('app/public');
    echo "storage/app/public exists: " . (file_exists($expected) ? 'YES' : 'NO') . "\n";
    echo "storage/app/public is_dir: " . (is_dir($expected) ? 'YES' : 'NO') . "\n";
    echo "storage/app/public is_link: " . (is_link($expected) ? 'YES' : 'NO') . "\n";
    if (is_link($expected)) {
        echo "Symlink target: " . readlink($expected) . "\n";
    }
    if (is_dir($expected)) {
        $items = array_diff(scandir($expected), ['.', '..']);
        echo "Contents: " . implode(', ', $items) . "\n";
    }

    // Check storage/app exists
    echo "\nstorage/app exists: " . (is_dir(storage_path('app')) ? 'YES' : 'NO') . "\n";
    if (is_dir(storage_path('app'))) {
        $items = array_diff(scandir(storage_path('app')), ['.', '..']);
        echo "Contents: " . implode(', ', $items) . "\n";
    }

    // Can we create a symlink?
    echo "\n=== Solution options ===\n";
    echo "Option 1: Symlink " . storage_path('app/public/client_docs') . " -> " . config('filesystems.disks.public.root') . "/client_docs\n";
    echo "Option 2: Change filesystems.php public disk root to " . storage_path('app/public') . "\n";
    echo "Option 3: Fix DocManagerController to use Storage::disk('public')->path() instead of storage_path()\n";

    // Check if client_docs already exists in storage/app/public
    $clientDocsInStoragePath = storage_path('app/public/client_docs');
    echo "\nclient_docs in storage_path: {$clientDocsInStoragePath}\n";
    echo "  exists: " . (file_exists($clientDocsInStoragePath) ? 'YES' : 'NO') . "\n";
    echo "  is_dir: " . (is_dir($clientDocsInStoragePath) ? 'YES' : 'NO') . "\n";
    echo "  is_link: " . (is_link($clientDocsInStoragePath) ? 'YES' : 'NO') . "\n";

    // Also check what's in /usr/www/users/smartucbmh/storage/
    $customStorage = config('filesystems.disks.public.root');
    echo "\nCustom storage root: {$customStorage}\n";
    if (is_dir($customStorage)) {
        $items = array_diff(scandir($customStorage), ['.', '..']);
        echo "Contents: " . implode(', ', $items) . "\n";
    }

    // Also check the view_client blade - it uses asset('storage/' . $document->file_path)
    echo "\n=== Blade asset path check ===\n";
    echo "Blade uses: asset('storage/client_docs/ATP100/...')\n";
    echo "This resolves to URL: https://smartweigh.co.za/storage/client_docs/ATP100/...\n";
    echo "public_path(): " . public_path() . "\n";
    echo "public_path('storage'): " . public_path('storage') . "\n";
    echo "public/storage exists: " . (file_exists(public_path('storage')) ? 'YES' : 'NO') . "\n";
    echo "public/storage is_link: " . (is_link(public_path('storage')) ? 'YES' : 'NO') . "\n";
    echo "public/storage is_dir: " . (is_dir(public_path('storage')) ? 'YES' : 'NO') . "\n";
    if (is_link(public_path('storage'))) {
        echo "  target: " . readlink(public_path('storage')) . "\n";
    }
    if (is_dir(public_path('storage'))) {
        $items = array_diff(scandir(public_path('storage')), ['.', '..']);
        echo "  contents: " . implode(', ', array_slice($items, 0, 10)) . "\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getFile() . ":" . $e->getLine() . "\n";
}
