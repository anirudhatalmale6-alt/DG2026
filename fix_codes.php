<?php
header('Content-Type: text/plain; charset=utf-8');

// Parse .env
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

// Generate codes: first 3 chars uppercase + 100, increment for duplicates
$prefixCount = [];
$newCodes = [];

foreach ($clients as $c) {
    $name = trim($c['client_company_name']);
    // Get first 3 alpha characters from the company name
    $alpha = '';
    for ($i = 0; $i < strlen($name) && strlen($alpha) < 3; $i++) {
        if (ctype_alpha($name[$i])) {
            $alpha .= strtoupper($name[$i]);
        }
    }
    // Pad if less than 3 chars
    while (strlen($alpha) < 3) {
        $alpha .= 'X';
    }

    if (!isset($prefixCount[$alpha])) {
        $prefixCount[$alpha] = 0;
    }
    $prefixCount[$alpha]++;
    $num = $prefixCount[$alpha] * 100;
    $code = $alpha . $num;

    $newCodes[$c['client_id']] = [
        'code' => $code,
        'name' => $c['client_company_name'],
    ];
}

// DRY RUN - show what will change
echo "=== PROPOSED CLIENT CODE UPDATES ===\n";
echo str_pad("CLIENT_ID", 10) . str_pad("NEW_CODE", 12) . "COMPANY_NAME\n";
echo str_repeat('-', 80) . "\n";

foreach ($newCodes as $cid => $data) {
    echo str_pad($cid, 10) . str_pad($data['code'], 12) . $data['name'] . "\n";
}

echo "\n=== CURRENT vs NEW ===\n";
$existing = $pdo->query("SELECT relid, value FROM tblcustomfieldsvalues WHERE fieldid=$fieldId")->fetchAll(PDO::FETCH_KEY_PAIR);

$changes = 0;
$inserts = 0;
foreach ($newCodes as $cid => $data) {
    $oldCode = $existing[$cid] ?? 'NONE';
    $marker = ($oldCode !== $data['code']) ? ' << CHANGED' : '';
    if ($oldCode === 'NONE') $marker = ' << NEW INSERT';
    if ($marker) {
        echo "client_id=$cid | OLD: $oldCode | NEW: {$data['code']} | {$data['name']}$marker\n";
        if ($oldCode === 'NONE') $inserts++;
        else $changes++;
    }
}

echo "\nTotal clients: " . count($newCodes) . "\n";
echo "Changes: $changes\n";
echo "New inserts: $inserts\n";
echo "Unchanged: " . (count($newCodes) - $changes - $inserts) . "\n";

echo "\n=== DRY RUN ONLY - NO CHANGES MADE ===\n";

@unlink(__FILE__);
