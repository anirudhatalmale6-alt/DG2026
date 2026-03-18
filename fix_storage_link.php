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

echo "<pre>\n=== FIX STORAGE LINKS ===\n\n";

// The public disk stores files at: /usr/www/users/smartucbmh/storage/
// The web serves from: /usr/www/users/smartucbmh/public/storage/
// We need a symlink: /usr/www/users/smartucbmh/public/storage/client_docs -> /usr/www/users/smartucbmh/storage/client_docs

$storageRoot = '/usr/www/users/smartucbmh/storage';
$webStorageRoot = '/usr/www/users/smartucbmh/public/storage';

// Ensure client_docs exists in storage root
$clientDocsStorage = $storageRoot . '/client_docs';
if (!file_exists($clientDocsStorage)) {
    mkdir($clientDocsStorage, 0755, true);
    echo "Created: $clientDocsStorage\n";
} else {
    echo "Exists: $clientDocsStorage\n";
}

// Create symlink in web storage
$clientDocsWeb = $webStorageRoot . '/client_docs';
if (is_link($clientDocsWeb)) {
    echo "Symlink already exists: $clientDocsWeb -> " . readlink($clientDocsWeb) . "\n";
} elseif (file_exists($clientDocsWeb)) {
    echo "Path exists but is not a symlink: $clientDocsWeb\n";
} else {
    $result = @symlink($clientDocsStorage, $clientDocsWeb);
    if ($result) {
        echo "Created symlink: $clientDocsWeb -> $clientDocsStorage\n";
    } else {
        echo "FAILED to create symlink. Error: " . error_get_last()['message'] . "\n";
        // Alternative: create the directory directly since files will be written here via the disk
        echo "Trying alternative: creating directory directly...\n";
        mkdir($clientDocsWeb, 0755, true);
        echo "Created directory: $clientDocsWeb\n";
    }
}

// Also ensure sars_rep_docs is linked
$sarsStorage = $storageRoot . '/sars_rep_docs';
$sarsWeb = $webStorageRoot . '/sars_rep_docs';
if (!file_exists($sarsStorage)) {
    mkdir($sarsStorage, 0755, true);
    echo "Created: $sarsStorage\n";
}
if (!file_exists($sarsWeb) && !is_link($sarsWeb)) {
    @symlink($sarsStorage, $sarsWeb);
    echo "Created sars_rep_docs symlink\n";
}

// Verify
echo "\n=== VERIFICATION ===\n";
echo "Storage root contents: " . implode(', ', scandir($storageRoot)) . "\n";
echo "Web storage contents: " . implode(', ', scandir($webStorageRoot)) . "\n";

// Test end-to-end
echo "\n=== END-TO-END TEST ===\n";
\Illuminate\Support\Facades\Storage::disk('public')->put('client_docs/e2e_test.txt', 'test from storage disk');
$diskPath = \Illuminate\Support\Facades\Storage::disk('public')->path('client_docs/e2e_test.txt');
echo "Disk path: $diskPath\n";
echo "File exists at disk path: " . (file_exists($diskPath) ? 'YES' : 'NO') . "\n";

// Check if accessible via web path
$webPath = $webStorageRoot . '/client_docs/e2e_test.txt';
echo "Web path: $webPath\n";
echo "File exists at web path: " . (file_exists($webPath) ? 'YES' : 'NO') . "\n";

\Illuminate\Support\Facades\Storage::disk('public')->delete('client_docs/e2e_test.txt');

echo "\n=== DONE ===\n</pre>";
