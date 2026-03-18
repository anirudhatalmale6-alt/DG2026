<?php
header('Content-Type: text/plain');

// This script copies files from the custom storage location to the standard storage_path location
// so the controller can find them

$source = '/usr/www/users/smartucbmh/storage/client_docs';
$target = '/usr/www/users/smartucbmh/application/storage/app/public/client_docs';

echo "=== Copying files from custom storage to standard storage_path ===\n";
echo "Source: {$source}\n";
echo "Target: {$target}\n\n";

if (!is_dir($source)) {
    echo "Source does not exist!\n";
    exit;
}

if (!is_dir($target)) {
    mkdir($target, 0755, true);
    echo "Created target directory\n";
}

// Iterate through client folders
$clientDirs = array_diff(scandir($source), ['.', '..']);
$totalCopied = 0;
$totalSkipped = 0;

foreach ($clientDirs as $clientDir) {
    $srcDir = $source . '/' . $clientDir;
    $tgtDir = $target . '/' . $clientDir;

    if (!is_dir($srcDir)) continue;

    if (!is_dir($tgtDir)) {
        mkdir($tgtDir, 0755, true);
    }

    $files = array_diff(scandir($srcDir), ['.', '..']);
    foreach ($files as $file) {
        $srcFile = $srcDir . '/' . $file;
        $tgtFile = $tgtDir . '/' . $file;

        if (is_file($srcFile)) {
            if (file_exists($tgtFile)) {
                // Skip if already exists and same size
                if (filesize($srcFile) === filesize($tgtFile)) {
                    $totalSkipped++;
                    continue;
                }
            }
            if (copy($srcFile, $tgtFile)) {
                $totalCopied++;
                echo "  Copied: {$clientDir}/{$file}\n";
            } else {
                echo "  FAILED: {$clientDir}/{$file}\n";
            }
        }
    }
}

echo "\nCopied: {$totalCopied} files\n";
echo "Skipped (already exist): {$totalSkipped} files\n";

// Also copy sars_rep_docs if it exists
$sarsSrc = '/usr/www/users/smartucbmh/storage/sars_rep_docs';
$sarsTgt = '/usr/www/users/smartucbmh/application/storage/app/public/sars_rep_docs';
if (is_dir($sarsSrc)) {
    echo "\n=== Also copying sars_rep_docs ===\n";
    if (!is_dir($sarsTgt)) mkdir($sarsTgt, 0755, true);
    $files = array_diff(scandir($sarsSrc), ['.', '..']);
    foreach ($files as $f) {
        if (is_file($sarsSrc . '/' . $f)) {
            copy($sarsSrc . '/' . $f, $sarsTgt . '/' . $f);
            echo "  Copied: {$f}\n";
        } elseif (is_dir($sarsSrc . '/' . $f)) {
            $subDir = $sarsTgt . '/' . $f;
            if (!is_dir($subDir)) mkdir($subDir, 0755, true);
            $subFiles = array_diff(scandir($sarsSrc . '/' . $f), ['.', '..']);
            foreach ($subFiles as $sf) {
                if (is_file($sarsSrc . '/' . $f . '/' . $sf)) {
                    copy($sarsSrc . '/' . $f . '/' . $sf, $subDir . '/' . $sf);
                    echo "  Copied: {$f}/{$sf}\n";
                }
            }
        }
    }
}

// Verify - check one document from DB
echo "\n=== Verification ===\n";
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$doc = \DB::table('cims_documents')->where('client_code', 'ATP100')->whereNotNull('file_path')->where('file_path', '!=', '')->first();
if ($doc) {
    $path = storage_path('app/public/' . $doc->file_path);
    echo "Doc {$doc->id}: {$doc->file_path}\n";
    echo "Full path: {$path}\n";
    echo "EXISTS: " . (file_exists($path) ? 'YES - FIX WORKED!' : 'NO - still broken') . "\n";
}
