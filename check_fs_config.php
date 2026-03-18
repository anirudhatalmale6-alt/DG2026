<?php
header('Content-Type: text/plain');

try {
    require __DIR__ . '/application/vendor/autoload.php';
    $app = require_once __DIR__ . '/application/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->handle(Illuminate\Http\Request::capture());

    // Show full filesystems config
    echo "=== Full filesystems config ===\n";
    $all = config('filesystems');
    echo "default: " . $all['default'] . "\n\n";
    foreach ($all['disks'] as $name => $disk) {
        echo "Disk [{$name}]:\n";
        foreach ($disk as $k => $v) {
            echo "  {$k}: {$v}\n";
        }
        echo "\n";
    }

    // Show the actual config file path
    echo "=== Config file location ===\n";
    echo "config_path: " . config_path() . "\n";
    $fsPath = config_path('filesystems.php');
    echo "filesystems.php: {$fsPath}\n";
    echo "exists: " . (file_exists($fsPath) ? 'YES' : 'NO') . "\n";

    // Also check if there's an env variable
    echo "\n=== ENV check ===\n";
    echo "FILESYSTEM_DISK: " . env('FILESYSTEM_DISK', 'not set') . "\n";
    echo "FILESYSTEM_DRIVER: " . env('FILESYSTEM_DRIVER', 'not set') . "\n";

    // The fix we need: make storage_path('app/public/client_docs') point to the same place as disk root/client_docs
    // Option: symlink /usr/www/users/smartucbmh/application/storage/app/public/client_docs -> /usr/www/users/smartucbmh/storage/client_docs
    echo "\n=== Proposed symlink fix ===\n";
    $source = '/usr/www/users/smartucbmh/storage/client_docs';
    $target = storage_path('app/public/client_docs');
    echo "Source (has files): {$source}\n";
    echo "  exists: " . (is_dir($source) ? 'YES' : 'NO') . "\n";
    echo "Target (controller expects): {$target}\n";
    echo "  exists: " . (file_exists($target) ? 'YES' : 'NO') . "\n";
    echo "  is_dir: " . (is_dir($target) ? 'YES' : 'NO') . "\n";
    echo "  is_link: " . (is_link($target) ? 'YES' : 'NO') . "\n";
    if (is_dir($target)) {
        $items = array_diff(scandir($target), ['.','..']);
        echo "  items: " . count($items) . "\n";
        foreach ($items as $i) echo "    {$i}\n";
    }

    // Also need public/storage symlink for blade views
    echo "\n=== public/storage symlink for blade ===\n";
    $publicStorage = public_path('storage');
    echo "public/storage: {$publicStorage}\n";
    echo "  exists: " . (file_exists($publicStorage) ? 'YES' : 'NO') . "\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
