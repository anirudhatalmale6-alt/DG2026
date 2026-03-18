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

echo "<pre>\n=== STORAGE PATH DETAILS ===\n\n";

// Check the actual public storage location
$paths = [
    '/usr/www/users/smartucbmh/storage',
    '/usr/www/users/smartucbmh/storage/client_docs',
    '/usr/www/users/smartucbmh/public/storage',
    '/usr/www/users/smartucbmh/public/storage/client_docs',
    '/usr/www/users/smartucbmh/application/storage/app/public',
    '/usr/www/users/smartucbmh/application/storage/app/public/client_docs',
    '/usr/www/users/smartucbmh/application/public/storage',
];

foreach ($paths as $p) {
    $exists = file_exists($p);
    $isLink = is_link($p);
    $isDir = is_dir($p);
    $target = $isLink ? readlink($p) : 'N/A';
    echo "  $p\n";
    echo "    exists=$exists, is_link=$isLink, is_dir=$isDir, target=$target\n";
}

// Test actual file write to public disk
echo "\n=== ACTUAL FILE WRITE TEST ===\n";
\Illuminate\Support\Facades\Storage::disk('public')->put('client_docs/test_pdf.txt', 'test content');
$fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path('client_docs/test_pdf.txt');
echo "  File saved at: $fullPath\n";
echo "  File exists there: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";

// Check if it's accessible via web
$url = \Illuminate\Support\Facades\Storage::disk('public')->url('client_docs/test_pdf.txt');
echo "  URL would be: $url\n";

// Clean up
\Illuminate\Support\Facades\Storage::disk('public')->delete('client_docs/test_pdf.txt');

// Check the web public storage link target
echo "\n=== SYMLINK DETAILS ===\n";
$webPublicStorage = '/usr/www/users/smartucbmh/public/storage';
if (is_link($webPublicStorage)) {
    echo "  $webPublicStorage -> " . readlink($webPublicStorage) . "\n";
    // List contents
    $items = scandir($webPublicStorage);
    echo "  Contents: " . implode(', ', $items) . "\n";
} elseif (is_dir($webPublicStorage)) {
    echo "  $webPublicStorage (regular dir)\n";
    $items = scandir($webPublicStorage);
    echo "  Contents: " . implode(', ', $items) . "\n";
}

// Check filesystem config
echo "\n=== FILESYSTEM DISKS ===\n";
$disks = config('filesystems.disks');
foreach ($disks as $name => $disk) {
    echo "  [$name]\n";
    foreach ($disk as $k => $v) {
        echo "    $k: $v\n";
    }
}

echo "\n</pre>";
