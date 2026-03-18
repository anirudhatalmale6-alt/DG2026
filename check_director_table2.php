<?php
// Try multiple config paths
$paths = [
    '/usr/www/users/smartucbmh/public_html/application/config/database.php',
    '/usr/www/users/smartucbmh/application/config/database.php',
    __DIR__ . '/application/config/database.php',
    __DIR__ . '/../application/config/database.php',
];
$config = null;
$foundPath = '';
foreach ($paths as $p) {
    if (file_exists($p)) {
        $config = include $p;
        $foundPath = $p;
        break;
    }
}
if (!$config) {
    echo "Config not found. Tried:\n";
    foreach ($paths as $p) echo "- $p\n";
    echo "\n__DIR__ = " . __DIR__ . "\n";
    echo "getcwd = " . getcwd() . "\n";
    exit;
}
echo "Config found at: $foundPath\n\n";
$db = $config['connections']['mysql'];
$pdo = new PDO("mysql:host={$db['host']};dbname={$db['database']};port={$db['port']}", $db['username'], $db['password']);
$stmt = $pdo->query("DESCRIBE client_master_directors");
echo "=== client_master_directors table columns ===\n";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " | " . $row['Type'] . " | " . $row['Null'] . " | " . $row['Key'] . " | " . ($row['Default'] ?? 'NULL') . "\n";
}
echo "\n=== INDEXES ===\n";
$stmt2 = $pdo->query("SHOW INDEX FROM client_master_directors");
while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    echo $row2['Key_name'] . " | " . $row2['Column_name'] . " | " . $row2['Non_unique'] . "\n";
}

// Also check cims_persons for profile photo
echo "\n=== cims_persons profile_photo sample ===\n";
$stmt3 = $pdo->query("SELECT id, firstname, surname, profile_photo FROM cims_persons LIMIT 10");
while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
    echo $row3['id'] . " | " . $row3['firstname'] . " " . $row3['surname'] . " | photo: " . ($row3['profile_photo'] ?? 'NULL') . "\n";
}
?>
