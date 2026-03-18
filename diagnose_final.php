<?php
header('Content-Type: text/plain');

try {
    require __DIR__ . '/application/vendor/autoload.php';
    $app = require_once __DIR__ . '/application/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->handle(Illuminate\Http\Request::capture());

    // EXACT paths - no ambiguity
    $p1 = storage_path('app/public/client_docs/ATP100');
    $p2 = config('filesystems.disks.public.root') . '/client_docs/ATP100';

    echo "Path A (storage_path): {$p1}\n";
    echo "  exists: " . (is_dir($p1) ? 'YES' : 'NO') . "\n";
    if (is_dir($p1)) {
        $f = array_diff(scandir($p1), ['.','..']);
        echo "  files: " . count($f) . "\n";
        foreach ($f as $file) echo "    {$file}\n";
    }

    echo "\nPath B (disk root): {$p2}\n";
    echo "  exists: " . (is_dir($p2) ? 'YES' : 'NO') . "\n";
    if (is_dir($p2)) {
        $f = array_diff(scandir($p2), ['.','..']);
        echo "  files: " . count($f) . "\n";
        foreach ($f as $file) echo "    {$file}\n";
    }

    echo "\nAre they the same path? " . ($p1 === $p2 ? 'YES' : 'NO') . "\n";
    echo "realpath A: " . (realpath($p1) ?: 'FALSE') . "\n";
    echo "realpath B: " . (realpath($p2) ?: 'FALSE') . "\n";

    // Specific file check - get first doc from DB
    $doc = \DB::table('cims_documents')->where('client_code', 'ATP100')->whereNotNull('file_path')->where('file_path', '!=', '')->first();
    if ($doc) {
        echo "\nFirst ATP100 doc: id={$doc->id}\n";
        echo "file_path: {$doc->file_path}\n";

        $checkA = storage_path('app/public/' . $doc->file_path);
        $checkB = config('filesystems.disks.public.root') . '/' . $doc->file_path;

        echo "\nCheck A (what controller does): {$checkA}\n";
        echo "  file_exists: " . (file_exists($checkA) ? 'YES' : 'NO') . "\n";

        echo "\nCheck B (where file actually is): {$checkB}\n";
        echo "  file_exists: " . (file_exists($checkB) ? 'YES' : 'NO') . "\n";
    }

    // public_path storage symlink
    echo "\n=== public/storage symlink ===\n";
    $ps = public_path('storage');
    echo "public_path('storage'): {$ps}\n";
    echo "  exists: " . (file_exists($ps) ? 'YES' : 'NO') . "\n";
    echo "  is_link: " . (is_link($ps) ? 'YES' : 'NO') . "\n";
    if (is_link($ps)) echo "  target: " . readlink($ps) . "\n";
    if (is_dir($ps)) {
        echo "  is_dir: YES\n";
        $items = array_diff(scandir($ps), ['.','..']);
        echo "  contents: " . implode(', ', $items) . "\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
