<?php
header('Content-Type: text/plain');

try {
    require __DIR__ . '/application/vendor/autoload.php';
    $app = require_once __DIR__ . '/application/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->handle(Illuminate\Http\Request::capture());

    // 1. List ACTUAL files in ATP100 folder
    $atp100 = storage_path('app/public/client_docs/ATP100');
    echo "=== Files in ATP100 folder ===\n";
    echo "Path: {$atp100}\n";
    echo "Exists: " . (is_dir($atp100) ? 'YES' : 'NO') . "\n";
    if (is_dir($atp100)) {
        $files = array_diff(scandir($atp100), ['.', '..']);
        foreach ($files as $f) {
            $full = $atp100 . '/' . $f;
            echo "  [{$f}] size=" . filesize($full) . "\n";
        }
    }

    // 2. Show DB file_path for ATP100 documents
    echo "\n=== DB records for ATP100 (client_code) ===\n";
    $docs = \DB::table('cims_documents')->where('client_code', 'ATP100')->get();
    foreach ($docs as $d) {
        echo "  id={$d->id}\n";
        echo "    file_path: [{$d->file_path}]\n";
        echo "    file_stored_name: [{$d->file_stored_name}]\n";
        echo "    file_original_name: [{$d->file_original_name}]\n";

        // The controller does: storage_path('app/public/' . $document->file_path)
        $controllerPath = storage_path('app/public/' . $d->file_path);
        echo "    controller_check: {$controllerPath}\n";
        echo "    controller_exists: " . (file_exists($controllerPath) ? 'YES' : 'NO') . "\n";

        // Maybe file_path already includes the filename?
        // Or maybe file_path is just the directory?
        // Let's try: storage_path('app/public/' . $d->file_path . '/' . $d->file_stored_name)
        if ($d->file_path && $d->file_stored_name) {
            $altPath = storage_path('app/public/' . $d->file_path . '/' . $d->file_stored_name);
            echo "    alt_check: {$altPath}\n";
            echo "    alt_exists: " . (file_exists($altPath) ? 'YES' : 'NO') . "\n";
        }

        // Maybe file_path is the full relative path including filename
        // In that case we need to check if the file in the folder matches file_original_name
        if ($d->file_original_name) {
            $origPath = $atp100 . '/' . $d->file_original_name;
            echo "    orig_check: {$origPath}\n";
            echo "    orig_exists: " . (file_exists($origPath) ? 'YES' : 'NO') . "\n";
        }
    }

    // 3. Show what the view_client blade expects
    echo "\n=== view_client.blade.php asset path ===\n";
    echo "The blade uses: asset('storage/' . document->file_path)\n";
    echo "Which generates: https://smartweigh.co.za/storage/client_docs/ATP100/...\n";
    echo "This needs a symlink from public/storage -> storage/app/public\n";

    // 4. Check the storage symlink
    $publicPath = public_path('storage');
    echo "\npublic_path('storage'): {$publicPath}\n";
    echo "  exists: " . (file_exists($publicPath) ? 'YES' : 'NO') . "\n";
    echo "  is_link: " . (is_link($publicPath) ? 'YES' : 'NO') . "\n";
    echo "  is_dir: " . (is_dir($publicPath) ? 'YES' : 'NO') . "\n";
    if (is_link($publicPath)) {
        echo "  target: " . readlink($publicPath) . "\n";
    }
    if (is_dir($publicPath)) {
        $items = array_diff(scandir($publicPath), ['.', '..']);
        echo "  contents: " . implode(', ', array_slice($items, 0, 10)) . "\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getFile() . ":" . $e->getLine() . "\n";
}
