<?php
header('Content-Type: text/plain');
$pdo = new PDO('mysql:host=localhost;dbname=grow_crm_2026', '5fokp_qnbo1', '4P9716bzm7598A');

// Get the latest document
$stmt = $pdo->query("SELECT id, file_path, file_stored_name, file_size, created_at FROM cims_documents ORDER BY id DESC LIMIT 3");
$docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "LATEST DOCS:\n";
foreach ($docs as $doc) {
    echo "id={$doc['id']} path={$doc['file_path']} size={$doc['file_size']} created={$doc['created_at']}\n";
    $fp = $doc['file_path'];

    // Check all possible locations
    $locations = [
        'app_storage' => '/usr/www/users/smartucbmh/application/storage/app/public/' . $fp,
        'web_storage' => '/usr/www/users/smartucbmh/public_html/storage/' . $fp,
        'extra_storage' => '/usr/www/users/smartucbmh/storage/' . $fp,
    ];
    foreach ($locations as $label => $path) {
        echo "  $label: " . (file_exists($path) ? "EXISTS (" . filesize($path) . "b)" : "MISSING") . "\n";
    }
}

// Check what public_path resolves to in Laravel context
echo "\nPATH CHECK:\n";

// Simulate what Laravel's public_path would return
// Laravel base is /usr/www/users/smartucbmh/application
// public_path typically returns base_path/public
$laravelBase = '/usr/www/users/smartucbmh/application';
$publicPath = $laravelBase . '/public';
echo "Expected public_path(): $publicPath\n";
echo "public dir exists: " . (is_dir($publicPath) ? "YES" : "NO") . "\n";
echo "public/storage dir: " . (is_dir($publicPath . '/storage') ? "YES" : "NO") . "\n";
echo "public/storage/client_docs dir: " . (is_dir($publicPath . '/storage/client_docs') ? "YES" : "NO") . "\n";

// Check if the controller actually wrote to the right place
$expectedDest = $publicPath . '/storage/client_docs/ATP100/';
echo "Expected dest dir: " . (is_dir($expectedDest) ? "EXISTS" : "MISSING") . "\n";
if (is_dir($expectedDest)) {
    echo "Files in dir:\n";
    foreach (scandir($expectedDest) as $f) {
        if ($f === '.' || $f === '..') continue;
        echo "  $f (" . filesize($expectedDest . $f) . "b)\n";
    }
}

// Also check web root storage
echo "\nWEB ROOT STORAGE:\n";
$webRoot = '/usr/www/users/smartucbmh/public_html/storage/client_docs/ATP100/';
echo "Dir exists: " . (is_dir($webRoot) ? "YES" : "NO") . "\n";
if (is_dir($webRoot)) {
    foreach (scandir($webRoot) as $f) {
        if ($f === '.' || $f === '..') continue;
        echo "  $f (" . filesize($webRoot . $f) . "b)\n";
    }
}

// Check what asset('storage/') resolves to
echo "\nURL: asset('storage/') would be https://smartweigh.co.za/storage/\n";
echo "Which maps to: /usr/www/users/smartucbmh/public_html/storage/\n";
