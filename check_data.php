<?php
// Check available clients and periods

$db = new mysqli('localhost', '5fokp_qnbo1', '4P9716bzm7598A', 'grow_crm_2026');
if ($db->connect_error) die("Connection failed: " . $db->connect_error);

echo "=== ACTIVE CLIENTS (first 20) ===\n";
$r = $db->query("SELECT client_id, clientcode, company_name FROM client_master WHERE is_active=1 ORDER BY client_id LIMIT 20");
while ($row = $r->fetch_assoc()) {
    echo "ID: {$row['client_id']} | Code: {$row['clientcode']} | Company: {$row['company_name']}\n";
}

echo "\n=== PERIODS (unique financial years) ===\n";
$r = $db->query("SELECT DISTINCT financial_year FROM cims_document_periods ORDER BY financial_year");
while ($row = $r->fetch_assoc()) {
    echo "Year: {$row['financial_year']}\n";
}

echo "\n=== PERIOD SAMPLES ===\n";
$r = $db->query("SELECT id, period_name, period_combo, financial_year FROM cims_document_periods WHERE financial_year IN (2025, 2026) ORDER BY financial_year, period_combo LIMIT 30");
while ($row = $r->fetch_assoc()) {
    echo "ID: {$row['id']} | Name: {$row['period_name']} | Combo: {$row['period_combo']} | Year: {$row['financial_year']}\n";
}

echo "\n=== EXISTING EMP201 COUNT ===\n";
$r = $db->query("SELECT COUNT(*) as cnt FROM cims_emp201_declarations WHERE deleted_at IS NULL");
$row = $r->fetch_assoc();
echo "Existing records: {$row['cnt']}\n";

echo "\n=== PAYE NUMBERS SAMPLE ===\n";
$r = $db->query("SELECT client_id, clientcode, company_name, paye_number, sdl_number, uif_number FROM client_master WHERE is_active=1 AND paye_number IS NOT NULL AND paye_number != '' LIMIT 10");
while ($row = $r->fetch_assoc()) {
    echo "ID: {$row['client_id']} | Code: {$row['clientcode']} | PAYE: {$row['paye_number']} | SDL: {$row['sdl_number']} | UIF: {$row['uif_number']}\n";
}

$db->close();
