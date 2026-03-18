<?php
header('Content-Type: text/plain; charset=utf-8');

$envFile = __DIR__ . '/application/.env';
$env = [];
$content = file_get_contents($envFile);
foreach (explode("\n", $content) as $line) {
    $line = trim($line);
    if (empty($line) || $line[0] === '#') continue;
    $pos = strpos($line, '=');
    if ($pos === false) continue;
    $key = trim(substr($line, 0, $pos));
    $val = trim(substr($line, $pos + 1));
    if ((substr($val, 0, 1) === '"' && substr($val, -1) === '"') || (substr($val, 0, 1) === "'" && substr($val, -1) === "'")) {
        $val = substr($val, 1, -1);
    }
    $env[$key] = $val;
}

$pdo = new PDO("mysql:host={$env['DB_HOST']};port={$env['DB_PORT']};dbname={$env['DB_DATABASE']};charset=utf8mb4", $env['DB_USERNAME'], $env['DB_PASSWORD']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$fieldId = 38;

// Get all clients
$clients = $pdo->query("SELECT client_id, client_company_name FROM clients ORDER BY client_company_name")->fetchAll(PDO::FETCH_ASSOC);

// Generate codes
$prefixCount = [];
$newCodes = [];

foreach ($clients as $c) {
    $name = trim($c['client_company_name']);
    $alpha = '';
    for ($i = 0; $i < strlen($name) && strlen($alpha) < 3; $i++) {
        if (ctype_alpha($name[$i])) {
            $alpha .= strtoupper($name[$i]);
        }
    }
    while (strlen($alpha) < 3) $alpha .= 'X';

    if (!isset($prefixCount[$alpha])) $prefixCount[$alpha] = 0;
    $prefixCount[$alpha]++;
    $num = $prefixCount[$alpha] * 100;
    $code = $alpha . $num;

    $newCodes[$c['client_id']] = $code;
}

// Get existing records
$existing = $pdo->query("SELECT id, relid, value FROM tblcustomfieldsvalues WHERE fieldid=$fieldId")->fetchAll(PDO::FETCH_ASSOC);
$existingMap = [];
foreach ($existing as $e) {
    $existingMap[$e['relid']] = $e;
}

// Apply updates
$updated = 0;
$inserted = 0;

$pdo->beginTransaction();
try {
    foreach ($newCodes as $clientId => $code) {
        if (isset($existingMap[$clientId])) {
            // Update existing record
            $stmt = $pdo->prepare("UPDATE tblcustomfieldsvalues SET value = ? WHERE id = ?");
            $stmt->execute([$code, $existingMap[$clientId]['id']]);
            $updated++;
            echo "UPDATED: client_id=$clientId | OLD={$existingMap[$clientId]['value']} | NEW=$code\n";
        } else {
            // Insert new record
            $stmt = $pdo->prepare("INSERT INTO tblcustomfieldsvalues (relid, fieldid, fieldto, value) VALUES (?, ?, 'customers', ?)");
            $stmt->execute([$clientId, $fieldId, $code]);
            $inserted++;
            echo "INSERTED: client_id=$clientId | CODE=$code\n";
        }
    }
    $pdo->commit();
    echo "\n=== DONE ===\n";
    echo "Updated: $updated\n";
    echo "Inserted: $inserted\n";
    echo "Total: " . ($updated + $inserted) . "\n";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "ERROR - ROLLED BACK: " . $e->getMessage() . "\n";
}

@unlink(__FILE__);
