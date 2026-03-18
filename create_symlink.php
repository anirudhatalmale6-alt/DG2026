<?php
header('Content-Type: text/plain');

// Create symlink: public/storage -> application/storage/app/public
// This allows asset('storage/...') URLs to serve files from storage

$linkPath = '/usr/www/users/smartucbmh/application/public/storage';
$targetPath = '/usr/www/users/smartucbmh/application/storage/app/public';

echo "Creating symlink:\n";
echo "  Link: {$linkPath}\n";
echo "  Target: {$targetPath}\n\n";

if (file_exists($linkPath)) {
    if (is_link($linkPath)) {
        echo "Symlink already exists, removing old one...\n";
        unlink($linkPath);
    } elseif (is_dir($linkPath)) {
        echo "A directory already exists at link path. Contents:\n";
        $items = array_diff(scandir($linkPath), ['.', '..']);
        foreach ($items as $i) echo "  {$i}\n";
        echo "\nRenaming existing directory to storage_backup...\n";
        rename($linkPath, $linkPath . '_backup_' . date('Ymd_His'));
    }
}

if (!file_exists($targetPath)) {
    echo "Target does not exist! Creating it...\n";
    mkdir($targetPath, 0755, true);
}

$result = symlink($targetPath, $linkPath);
echo "Symlink created: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";

// Verify
echo "\nVerification:\n";
echo "  is_link: " . (is_link($linkPath) ? 'YES' : 'NO') . "\n";
echo "  readlink: " . (is_link($linkPath) ? readlink($linkPath) : 'N/A') . "\n";
echo "  is_dir: " . (is_dir($linkPath) ? 'YES' : 'NO') . "\n";

// Test a file access through the symlink
$testFile = $linkPath . '/client_docs/ATP100';
echo "\nTest: {$testFile}\n";
echo "  exists: " . (file_exists($testFile) ? 'YES' : 'NO') . "\n";
if (is_dir($testFile)) {
    $files = array_diff(scandir($testFile), ['.', '..']);
    echo "  files: " . count($files) . "\n";
}

// Also need to handle the web root level for GrowCRM's base href
// GrowCRM uses <base href="https://smartweigh.co.za"> so asset('storage/...')
// generates URLs like https://smartweigh.co.za/storage/...
// The public_html is the web root, so we also need a symlink at public_html/storage
$webRootLink = '/usr/www/users/smartucbmh/storage_link_check';
echo "\n=== Web root check ===\n";
echo "public_html/storage: /usr/www/users/smartucbmh/public_html/storage\n";
$phs = '/usr/www/users/smartucbmh/public_html/storage';
echo "  exists: " . (file_exists($phs) ? 'YES' : 'NO') . "\n";
echo "  is_link: " . (is_link($phs) ? 'YES' : 'NO') . "\n";
echo "  is_dir: " . (is_dir($phs) ? 'YES' : 'NO') . "\n";

// If GrowCRM uses /public/ in URLs, check that too
echo "\npublic_html/public/storage:\n";
$pps = '/usr/www/users/smartucbmh/public_html/public/storage';
echo "  exists: " . (file_exists($pps) ? 'YES' : 'NO') . "\n";
echo "  is_link: " . (is_link($pps) ? 'YES' : 'NO') . "\n";
