<?php
header('Content-Type: text/plain');

try {
    require __DIR__ . '/application/vendor/autoload.php';
    $app = require_once __DIR__ . '/application/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->handle(Illuminate\Http\Request::capture());

    // 1. Check filesystems config
    echo "=== Filesystem 'public' disk config ===\n";
    $config = config('filesystems.disks.public');
    print_r($config);

    echo "\n=== Default disk ===\n";
    echo config('filesystems.default') . "\n";

    // 2. Check if the public disk root exists and is writable
    $publicRoot = config('filesystems.disks.public.root');
    echo "\nPublic disk root: {$publicRoot}\n";
    echo "Exists: " . (file_exists($publicRoot) ? 'YES' : 'NO') . "\n";
    echo "Is dir: " . (is_dir($publicRoot) ? 'YES' : 'NO') . "\n";
    echo "Writable: " . (is_writable($publicRoot) ? 'YES' : 'NO') . "\n";

    // 3. Check client_docs dir
    $clientDocs = $publicRoot . '/client_docs';
    echo "\nclient_docs: {$clientDocs}\n";
    echo "Exists: " . (file_exists($clientDocs) ? 'YES' : 'NO') . "\n";
    echo "Is dir: " . (is_dir($clientDocs) ? 'YES' : 'NO') . "\n";
    echo "Writable: " . (is_writable($clientDocs) ? 'YES' : 'NO') . "\n";

    $atp100 = $clientDocs . '/ATP100';
    echo "\nATP100: {$atp100}\n";
    echo "Exists: " . (file_exists($atp100) ? 'YES' : 'NO') . "\n";
    echo "Writable: " . (is_writable($atp100) ? 'YES' : 'NO') . "\n";

    // 4. Try a test write
    echo "\n=== Test write ===\n";
    $testFile = $atp100 . '/test_write.txt';
    $result = @file_put_contents($testFile, 'test');
    echo "Write test: " . ($result !== false ? 'SUCCESS' : 'FAILED') . "\n";
    if ($result !== false) {
        @unlink($testFile);
    }

    // 5. Try storeAs
    echo "\n=== Test storeAs with Storage facade ===\n";
    $testContent = 'test content';
    $stored = \Illuminate\Support\Facades\Storage::disk('public')->put('client_docs/ATP100/test_storage.txt', $testContent);
    echo "Storage::put result: " . ($stored ? 'SUCCESS' : 'FAILED') . "\n";
    $storedExists = \Illuminate\Support\Facades\Storage::disk('public')->exists('client_docs/ATP100/test_storage.txt');
    echo "File exists after put: " . ($storedExists ? 'YES' : 'NO') . "\n";
    if ($storedExists) {
        \Illuminate\Support\Facades\Storage::disk('public')->delete('client_docs/ATP100/test_storage.txt');
    }

    // 6. List actual ATP100 files vs DB expected
    echo "\n=== ATP100 actual files ===\n";
    if (is_dir($atp100)) {
        $files = array_diff(scandir($atp100), ['.', '..']);
        foreach ($files as $f) {
            echo "  DISK: {$f}\n";
        }
    }

    echo "\n=== ATP100 DB records ===\n";
    $docs = \DB::table('cims_documents')->where('client_code', 'ATP100')->get(['id', 'file_path', 'file_stored_name']);
    foreach ($docs as $d) {
        echo "  DB id={$d->id}: {$d->file_stored_name}\n";
        // Extract just filename from file_path
        $expectedFile = basename($d->file_path);
        echo "    Expected file: {$expectedFile}\n";
        $exists = file_exists($atp100 . '/' . $expectedFile);
        echo "    On disk: " . ($exists ? 'YES' : 'NO') . "\n";
    }

    // 7. Check Laravel logs for upload errors
    echo "\n=== Recent Laravel log entries ===\n";
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $log = file_get_contents($logFile);
        $lines = explode("\n", $log);
        // Show last 30 lines
        $last = array_slice($lines, -30);
        foreach ($last as $l) {
            echo $l . "\n";
        }
    } else {
        echo "No laravel.log found\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getFile() . ":" . $e->getLine() . "\n";
}
