<?php
// Diagnostic: show client_master_directors table columns
define('BASE_DIR', '/usr/www/users/smartucbmh');
$configPath = BASE_DIR . '/public_html/application/config/database.php';
if (!file_exists($configPath)) {
    die("Config not found");
}
$config = include $configPath;
$db = $config['connections']['mysql'];
$pdo = new PDO("mysql:host={$db['host']};dbname={$db['database']};port={$db['port']}", $db['username'], $db['password']);
$stmt = $pdo->query("DESCRIBE client_master_directors");
echo "=== client_master_directors table columns ===\n";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " | " . $row['Type'] . " | " . $row['Null'] . " | " . $row['Key'] . " | " . $row['Default'] . "\n";
}
echo "\n=== INDEXES ===\n";
$stmt2 = $pdo->query("SHOW INDEX FROM client_master_directors");
while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    echo $row2['Key_name'] . " | " . $row2['Column_name'] . " | " . $row2['Non_unique'] . "\n";
}
?>
