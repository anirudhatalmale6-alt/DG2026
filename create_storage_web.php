<?php
header('Content-Type: text/plain');

// Since symlinks fail on this hosting, we'll copy files to web-accessible location
// The blade uses asset('storage/...') which resolves to /storage/...
// With <base href="https://smartweigh.co.za">, this means /public_html/storage/...
// But wait - GrowCRM serves from public_html which IS the web root
// So we need files at: public_html/storage/client_docs/...

// BUT this is wasteful - copying all files. Instead, let's use a PHP passthrough
// Create a storage/index.php that serves files from the actual storage location

$webStorageDir = '/usr/www/users/smartucbmh/public_html/storage';
$clientDocsDir = $webStorageDir . '/client_docs';

echo "=== Creating web-accessible storage directory ===\n";
echo "Target: {$webStorageDir}\n\n";

// Create directory structure
if (!is_dir($webStorageDir)) {
    mkdir($webStorageDir, 0755, true);
    echo "Created: {$webStorageDir}\n";
}

// Copy client_docs from actual storage
$source = '/usr/www/users/smartucbmh/application/storage/app/public/client_docs';
if (!is_dir($source)) {
    echo "Source not found: {$source}\n";
    exit;
}

echo "Source: {$source}\n\n";

// Recursively copy
function copyDir($src, $dst) {
    $count = 0;
    if (!is_dir($dst)) mkdir($dst, 0755, true);

    $items = array_diff(scandir($src), ['.', '..']);
    foreach ($items as $item) {
        $srcPath = $src . '/' . $item;
        $dstPath = $dst . '/' . $item;

        if (is_dir($srcPath)) {
            $count += copyDir($srcPath, $dstPath);
        } else {
            if (!file_exists($dstPath) || filesize($srcPath) !== filesize($dstPath)) {
                copy($srcPath, $dstPath);
                echo "  Copied: {$item}\n";
                $count++;
            }
        }
    }
    return $count;
}

$copied = copyDir($source, $clientDocsDir);
echo "\nTotal files copied: {$copied}\n";

// Also copy sars_rep_docs if exists
$sarsSrc = '/usr/www/users/smartucbmh/application/storage/app/public/sars_rep_docs';
if (is_dir($sarsSrc)) {
    $sarsDst = $webStorageDir . '/sars_rep_docs';
    echo "\n=== Copying sars_rep_docs ===\n";
    $copied2 = copyDir($sarsSrc, $sarsDst);
    echo "sars_rep_docs copied: {$copied2}\n";
}

// Also copy signatures if exists
$sigSrc = '/usr/www/users/smartucbmh/application/storage/app/public/signatures';
if (is_dir($sigSrc)) {
    $sigDst = $webStorageDir . '/signatures';
    echo "\n=== Copying signatures ===\n";
    $copied3 = copyDir($sigSrc, $sigDst);
    echo "signatures copied: {$copied3}\n";
}

// Verify
echo "\n=== Verification ===\n";
echo "Web URL would be: https://smartweigh.co.za/storage/client_docs/ATP100/...\n";
$testDir = $clientDocsDir . '/ATP100';
if (is_dir($testDir)) {
    $files = array_diff(scandir($testDir), ['.', '..']);
    echo "ATP100 files: " . count($files) . "\n";
    foreach ($files as $f) echo "  {$f}\n";
}
