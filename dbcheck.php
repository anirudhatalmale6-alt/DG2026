<?php
header('Content-Type: text/plain; charset=utf-8');

// Parse .env file carefully
$envFile = __DIR__ . '/application/.env';
if (!file_exists($envFile)) {
    $envFile = __DIR__ . '/.env';
}
if (!file_exists($envFile)) {
    die("No .env found");
}

$env = [];
$content = file_get_contents($envFile);
foreach (explode("\n", $content) as $line) {
    $line = trim($line);
    if (empty($line) || $line[0] === '#') continue;
    $pos = strpos($line, '=');
    if ($pos === false) continue;
    $key = trim(substr($line, 0, $pos));
    $val = trim(substr($line, $pos + 1));
    // Remove surrounding quotes
    if ((substr($val, 0, 1) === '"' && substr($val, -1) === '"') || (substr($val, 0, 1) === "'" && substr($val, -1) === "'")) {
        $val = substr($val, 1, -1);
    }
    $env[$key] = $val;
}

$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? '3306';
$db = $env['DB_DATABASE'] ?? '';
$user = $env['DB_USERNAME'] ?? '';
$pass = $env['DB_PASSWORD'] ?? '';

echo "Connecting: host=$host, port=$port, db=$db, user=$user\n\n";

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected OK\n\n";
} catch (Exception $e) {
    die("DB Error: " . $e->getMessage());
}

echo "=== FIELD 38 DEF ===\n";
$stmt = $pdo->query("SELECT * FROM tblcustomfields WHERE id=38");
$f = $stmt->fetch(PDO::FETCH_ASSOC);
if ($f) { foreach ($f as $k => $v) echo "$k=$v\n"; }
else echo "Not found\n";

echo "\n=== FIELD38 VALUES (all) ===\n";
$stmt = $pdo->query("SELECT * FROM tblcustomfieldsvalues WHERE fieldid=38 ORDER BY relid");
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "id={$r['id']}|relid={$r['relid']}|fieldid={$r['fieldid']}|fieldto={$r['fieldto']}|value={$r['value']}\n";
}

echo "\n=== JOIN (first 25) ===\n";
$stmt = $pdo->query("SELECT c.client_id, c.client_company_name, v.relid, v.fieldto, v.value FROM clients c LEFT JOIN tblcustomfieldsvalues v ON c.client_id = v.relid AND v.fieldid = 38 ORDER BY c.client_company_name LIMIT 25");
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "cid={$r['client_id']}|relid=".($r['relid']??'NULL')."|ft=".($r['fieldto']??'NULL')."|code=".($r['value']??'NULL')."|name={$r['client_company_name']}\n";
}

echo "\n=== ATP SEARCH ===\n";
$stmt = $pdo->query("SELECT * FROM tblcustomfieldsvalues WHERE value LIKE '%ATP%'");
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) { echo "id={$r['id']}|relid={$r['relid']}|fid={$r['fieldid']}|ft={$r['fieldto']}|val={$r['value']}\n"; }

echo "\n=== NOM SEARCH ===\n";
$stmt = $pdo->query("SELECT * FROM tblcustomfieldsvalues WHERE value LIKE '%NOM%'");
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) { echo "id={$r['id']}|relid={$r['relid']}|fid={$r['fieldid']}|ft={$r['fieldto']}|val={$r['value']}\n"; }

@unlink(__FILE__);
