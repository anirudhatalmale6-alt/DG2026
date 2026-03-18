<?php
header('Content-Type: text/plain');
$pdo = new PDO('mysql:host=localhost;dbname=grow_crm_2026', '5fokp_qnbo1', '4P9716bzm7598A');

// Get latest documents
$stmt = $pdo->query("SELECT id, file_path, file_stored_name, doc_group, created_at FROM cims_documents ORDER BY id DESC LIMIT 5");
$docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "LATEST DOCUMENTS:\n";
foreach ($docs as $doc) {
    echo "id={$doc['id']} group={$doc['doc_group']} created={$doc['created_at']}\n";
    echo "  path={$doc['file_path']}\n";
    $fp = $doc['file_path'];
    $locs = [
        'app_storage' => '/usr/www/users/smartucbmh/application/storage/app/public/' . $fp,
        'app_public'  => '/usr/www/users/smartucbmh/application/public/storage/' . $fp,
        'web_root'    => '/usr/www/users/smartucbmh/public_html/storage/' . $fp,
    ];
    foreach ($locs as $label => $path) {
        echo "  $label: " . (file_exists($path) ? "EXISTS (" . filesize($path) . "b)" : "MISSING") . "\n";
    }
    echo "---\n";
}

// Check what base_path resolves to in Laravel
echo "\nPATH RESOLUTION:\n";
echo "base_path would be: /usr/www/users/smartucbmh/application\n";
echo "base_path('../public_html/storage/') = /usr/www/users/smartucbmh/public_html/storage/\n";
$resolved = realpath('/usr/www/users/smartucbmh/application/../public_html/storage/');
echo "realpath of that: " . ($resolved ?: 'CANNOT RESOLVE') . "\n";

// Check Laravel error log for recent errors
$logFile = '/usr/www/users/smartucbmh/application/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $last = array_slice($lines, -50);
    $errors = array_filter($last, function($l) { return strpos($l, 'ERROR') !== false || strpos($l, 'copy') !== false || strpos($l, 'mkdir') !== false; });
    if ($errors) {
        echo "\nRECENT LOG ERRORS:\n";
        foreach ($errors as $e) echo "  " . trim($e) . "\n";
    } else {
        echo "\nNo recent copy/mkdir errors in log.\n";
    }
}
