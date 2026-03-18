<?php
header('Content-Type: application/json');

$db = new mysqli('localhost', '5fokp_qnbo1', '4P9716bzm7598A', 'grow_crm_2026');
if ($db->connect_error) die(json_encode(['error' => $db->connect_error]));

$result = [];

// Active clients
$r = $db->query("SELECT client_id, clientcode, company_name, paye_number, sdl_number, uif_number FROM client_master WHERE is_active=1 ORDER BY client_id LIMIT 20");
$result['clients'] = [];
while ($row = $r->fetch_assoc()) $result['clients'][] = $row;

// Financial years
$r = $db->query("SELECT DISTINCT financial_year FROM cims_document_periods ORDER BY financial_year");
$result['years'] = [];
while ($row = $r->fetch_assoc()) $result['years'][] = $row['financial_year'];

// Periods for 2025 and 2026
$r = $db->query("SELECT id, period_name, period_combo, financial_year FROM cims_document_periods WHERE financial_year IN (2024,2025,2026) ORDER BY financial_year, period_combo");
$result['periods'] = [];
while ($row = $r->fetch_assoc()) $result['periods'][] = $row;

// Existing EMP201 count
$r = $db->query("SELECT COUNT(*) as cnt FROM cims_emp201_declarations WHERE deleted_at IS NULL");
$row = $r->fetch_assoc();
$result['existing_count'] = $row['cnt'];

// Existing records sample
$r = $db->query("SELECT id, client_id, client_code, financial_year, period_combo FROM cims_emp201_declarations WHERE deleted_at IS NULL LIMIT 10");
$result['existing'] = [];
while ($row = $r->fetch_assoc()) $result['existing'][] = $row;

$db->close();
echo json_encode($result, JSON_PRETTY_PRINT);
