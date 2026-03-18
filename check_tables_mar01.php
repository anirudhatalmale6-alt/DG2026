<?php
header('Content-Type: text/plain');

$conn = new mysqli('localhost', '5fokp_qnbo1', '4P9716bzm7598A', 'grow_crm_2026');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

echo "=== DESCRIBE client_master ===\n\n";
$result = $conn->query('DESCRIBE client_master');
if ($result) {
    printf("%-40s %-30s %-6s %-6s %-10s %-20s\n", 'Field', 'Type', 'Null', 'Key', 'Default', 'Extra');
    echo str_repeat('-', 120) . "\n";
    while ($row = $result->fetch_assoc()) {
        printf("%-40s %-30s %-6s %-6s %-10s %-20s\n",
            $row['Field'], $row['Type'], $row['Null'], $row['Key'],
            ($row['Default'] === null ? 'NULL' : $row['Default']), $row['Extra']);
    }
} else {
    echo "Error: " . $conn->error . "\n";
}

echo "\n\n=== DESCRIBE client_master_directors ===\n\n";
$result2 = $conn->query('DESCRIBE client_master_directors');
if ($result2) {
    printf("%-40s %-30s %-6s %-6s %-10s %-20s\n", 'Field', 'Type', 'Null', 'Key', 'Default', 'Extra');
    echo str_repeat('-', 120) . "\n";
    while ($row = $result2->fetch_assoc()) {
        printf("%-40s %-30s %-6s %-6s %-10s %-20s\n",
            $row['Field'], $row['Type'], $row['Null'], $row['Key'],
            ($row['Default'] === null ? 'NULL' : $row['Default']), $row['Extra']);
    }
} else {
    echo "Error: " . $conn->error . "\n";
}

$conn->close();
echo "\n\nDone.\n";
