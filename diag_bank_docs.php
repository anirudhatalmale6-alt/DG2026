<?php
header('Content-Type: text/plain');

$pdo = new PDO('mysql:host=localhost;dbname=grow_crm_2026', '5fokp_qnbo1', '4P9716bzm7598A');

$stmt = $pdo->query("SELECT id, client_code, title, file_path, file_stored_name, file_original_name, doc_group, created_at FROM cims_documents ORDER BY id DESC LIMIT 10");
$docs = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "=== LAST 10 DOCUMENTS ===\n\n";
foreach ($docs as $doc) {
    echo "ID: {$doc['id']}\n";
    echo "Client: {$doc['client_code']}\n";
    echo "file_path: {$doc['file_path']}\n";
    echo "doc_group: {$doc['doc_group']}\n";
    echo "created_at: {$doc['created_at']}\n";

    $fp = $doc['file_path'];
    $loc1 = "/usr/www/users/smartucbmh/public_html/application/storage/app/public/{$fp}";
    $loc2 = "/usr/www/users/smartucbmh/public_html/application/public/storage/{$fp}";
    $loc3 = "/usr/www/users/smartucbmh/public_html/storage/{$fp}";

    echo "  Laravel storage: " . (file_exists($loc1) ? "EXISTS" : "MISSING") . "\n";
    echo "  public/storage:  " . (file_exists($loc2) ? "EXISTS" : "MISSING") . "\n";
    echo "  web root storage: " . (file_exists($loc3) ? "EXISTS" : "MISSING") . "\n";
    echo "---\n";
}
