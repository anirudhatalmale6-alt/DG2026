<?php
$modelsDir = __DIR__ . '/Modules/cims_pm_pro/Models';
$needed = ['DocumentCategory.php', 'DocumentType.php', 'DocumentPeriod.php'];
echo "=== Models in cims_pm_pro/Models ===\n";
foreach (scandir($modelsDir) as $f) {
    if ($f === '.' || $f === '..') continue;
    echo "  $f\n";
}
echo "\n=== Missing models ===\n";
foreach ($needed as $n) {
    if (!file_exists($modelsDir . '/' . $n)) echo "  MISSING: $n\n";
    else echo "  EXISTS: $n\n";
}

// Check other modules for these models
echo "\n=== Checking other modules for DocumentCategory ===\n";
$modulesDir = __DIR__ . '/Modules';
foreach (scandir($modulesDir) as $mod) {
    if ($mod === '.' || $mod === '..') continue;
    $mPath = $modulesDir . '/' . $mod . '/Models';
    if (is_dir($mPath)) {
        foreach (scandir($mPath) as $f) {
            if (stripos($f, 'document') !== false || stripos($f, 'category') !== false) {
                echo "  $mod/Models/$f\n";
            }
        }
    }
}

// Check DB tables
echo "\n=== DB tables with 'category' or 'document' ===\n";
try {
    $env = file_get_contents(__DIR__ . '/.env');
    preg_match('/DB_HOST=(.*)/', $env, $h);
    preg_match('/DB_DATABASE=(.*)/', $env, $d);
    preg_match('/DB_USERNAME=(.*)/', $env, $u);
    preg_match('/DB_PASSWORD=(.*)/', $env, $p);
    $pdo = new PDO('mysql:host='.trim($h[1]).';dbname='.trim($d[1]), trim($u[1]), trim($p[1]));
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $t) {
        if (stripos($t, 'document') !== false || stripos($t, 'category') !== false || stripos($t, 'doc_') !== false) {
            // Get row count
            $cnt = $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
            echo "  $t ($cnt rows)\n";
        }
    }
    
    // Check if cims_document_categories table exists and show structure
    echo "\n=== Structure of document-related tables ===\n";
    foreach ($tables as $t) {
        if (stripos($t, 'document_categor') !== false || stripos($t, 'doc_categor') !== false || stripos($t, 'cims_document_c') !== false) {
            echo "\nTABLE: $t\n";
            $cols = $pdo->query("DESCRIBE `$t`")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($cols as $c) {
                echo "  {$c['Field']} - {$c['Type']} {$c['Null']} {$c['Key']}\n";
            }
            $rows = $pdo->query("SELECT * FROM `$t` LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            echo "  Sample data:\n";
            foreach ($rows as $r) echo "    " . json_encode($r) . "\n";
        }
    }

    // Also check for cims_document_types and cims_document_periods
    foreach (['cims_document_types', 'cims_document_periods'] as $checkTable) {
        $exists = false;
        foreach ($tables as $t) { if ($t === $checkTable) { $exists = true; break; } }
        if ($exists) {
            echo "\nTABLE: $checkTable\n";
            $cols = $pdo->query("DESCRIBE `$checkTable`")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($cols as $c) echo "  {$c['Field']} - {$c['Type']} {$c['Null']} {$c['Key']}\n";
        } else {
            echo "\nTABLE $checkTable: NOT FOUND\n";
        }
    }
} catch (Exception $e) {
    echo "  DB Error: " . $e->getMessage() . "\n";
}
