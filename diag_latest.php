<?php
header('Content-Type: text/plain');
$pdo = new PDO('mysql:host=localhost;dbname=grow_crm_2026', '5fokp_qnbo1', '4P9716bzm7598A');

$stmt = $pdo->query("SELECT id, file_path, file_stored_name, file_original_name, doc_group, created_at FROM cims_documents ORDER BY id DESC LIMIT 3");
echo "LATEST DOCS:\n";
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $d) {
    echo "id={$d['id']} original={$d['file_original_name']} group={$d['doc_group']} created={$d['created_at']}\n";
    echo "  stored_name={$d['file_stored_name']}\n";
    echo "  file_path={$d['file_path']}\n";
    $fp = $d['file_path'];
    $locs = [
        '/usr/www/users/smartucbmh/application/storage/app/public/' . $fp,
        '/usr/www/users/smartucbmh/storage/' . $fp,
        '/usr/www/users/smartucbmh/public_html/storage/' . $fp,
    ];
    foreach ($locs as $l) {
        $label = str_replace('/usr/www/users/smartucbmh/', '', $l);
        echo "  $label => " . (file_exists($l) ? "EXISTS (" . filesize($l) . "b)" : "MISSING") . "\n";
    }
    echo "---\n";
}
