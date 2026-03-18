<?php
header('Content-Type: text/plain');

try {
    require __DIR__ . '/application/vendor/autoload.php';
    $app = require_once __DIR__ . '/application/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->handle(Illuminate\Http\Request::capture());

    echo "storage_path(): " . storage_path() . "\n";
    echo "storage_path('app/public/'): " . storage_path('app/public/') . "\n";
    echo "storage_path('app/public/client_docs/'): " . storage_path('app/public/client_docs/') . "\n\n";

    // Check if client_docs directory exists at storage path
    $dir1 = storage_path('app/public/client_docs');
    echo "Dir check 1: {$dir1}\n";
    echo "  exists: " . (file_exists($dir1) ? 'YES' : 'NO') . "\n";
    echo "  is_dir: " . (is_dir($dir1) ? 'YES' : 'NO') . "\n\n";

    // Check alternative paths
    $paths = [
        '/usr/www/users/smartucbmh/application/storage/app/public/client_docs',
        '/usr/www/users/smartucbmh/storage/app/public/client_docs',
        '/usr/www/users/smartucbmh/public_html/storage/app/public/client_docs',
        '/usr/www/users/smartucbmh/public_html/application/storage/app/public/client_docs',
    ];
    echo "=== Alternative path checks ===\n";
    foreach ($paths as $p) {
        echo "{$p}\n";
        echo "  exists: " . (file_exists($p) ? 'YES' : 'NO') . "\n";
        if (is_dir($p)) {
            $contents = array_diff(scandir($p), ['.', '..']);
            echo "  contents: " . implode(', ', $contents) . "\n";
        }
    }

    // Get a specific document file_path from DB
    echo "\n=== Document file paths from DB ===\n";
    $docs = \DB::table('cims_documents')->whereNotNull('file_path')->where('file_path', '!=', '')->get(['id', 'file_path', 'file_stored_name']);
    foreach ($docs as $d) {
        echo "Doc {$d->id}: file_path='{$d->file_path}'\n";
        echo "  stored_name: '{$d->file_stored_name}'\n";

        // Try various base paths
        $try1 = storage_path('app/public/' . $d->file_path);
        $try2 = storage_path('app/public/' . $d->file_path . '/' . $d->file_stored_name);
        $try3 = storage_path('app/public/client_docs/' . $d->file_stored_name);

        echo "  try1 ({$try1}): " . (file_exists($try1) ? 'YES' : 'NO') . "\n";
        echo "  try2 ({$try2}): " . (file_exists($try2) ? 'YES' : 'NO') . "\n";
        echo "  try3 ({$try3}): " . (file_exists($try3) ? 'YES' : 'NO') . "\n";
    }

    // List what IS actually in the storage directories
    echo "\n=== What's in storage_path('app/public/') ===\n";
    $pubDir = storage_path('app/public');
    if (is_dir($pubDir)) {
        $items = array_diff(scandir($pubDir), ['.', '..']);
        foreach ($items as $item) {
            $full = $pubDir . '/' . $item;
            echo "  {$item} (" . (is_dir($full) ? 'DIR' : 'FILE') . ")\n";
            if (is_dir($full)) {
                $sub = array_diff(scandir($full), ['.', '..']);
                foreach (array_slice($sub, 0, 5) as $s) {
                    echo "    {$s}\n";
                }
                if (count($sub) > 5) echo "    ... and " . (count($sub) - 5) . " more\n";
            }
        }
    } else {
        echo "  Directory does not exist!\n";
        echo "  Checking parent: " . storage_path('app') . "\n";
        echo "  Parent exists: " . (is_dir(storage_path('app')) ? 'YES' : 'NO') . "\n";
        if (is_dir(storage_path('app'))) {
            $items = array_diff(scandir(storage_path('app')), ['.', '..']);
            echo "  Parent contents: " . implode(', ', $items) . "\n";
        }
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getFile() . ":" . $e->getLine() . "\n";
}
