<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

echo "<h2>Job Cards Diagnostic</h2>";

// Check which tables exist
$tables = [
    'job_card_types', 'cims_job_card_types',
    'job_cards', 'cims_job_cards',
    'job_card_type_steps', 'cims_job_card_type_steps',
    'job_card_type_fields', 'cims_job_card_type_fields',
    'job_card_type_documents', 'cims_job_card_type_documents',
    'job_card_progress', 'cims_job_card_progress',
    'job_card_attachments', 'cims_job_card_attachments',
    'client_master',
];

echo "<h3>Table Status:</h3><table border='1' cellpadding='6' cellspacing='0' style='border-collapse:collapse;font-family:sans-serif;'>";
echo "<tr style='background:#f0f0f0;'><th>Table</th><th>Exists?</th><th>Rows</th></tr>";

foreach ($tables as $t) {
    $exists = !empty(DB::select("SHOW TABLES LIKE '{$t}'"));
    $count = $exists ? DB::table($t)->count() : '-';
    $color = $exists ? 'green' : 'red';
    echo "<tr><td>{$t}</td><td style='color:{$color};font-weight:bold;'>" . ($exists ? 'YES' : 'NO') . "</td><td>{$count}</td></tr>";
}
echo "</table>";

// Test client search
echo "<h3>Client Search Test:</h3>";
try {
    $clients = DB::table('client_master')
        ->select(['client_id', 'client_code', 'company_name'])
        ->where('is_active', 1)
        ->whereNull('deleted_at')
        ->orderBy('company_name')
        ->limit(5)
        ->get();
    echo "<p style='color:green;'>Client search works! Found " . $clients->count() . " clients.</p>";
    echo "<ul>";
    foreach ($clients as $c) {
        echo "<li>" . htmlspecialchars($c->company_name) . " (" . htmlspecialchars($c->client_code ?? '') . ")</li>";
    }
    echo "</ul>";
} catch (\Exception $e) {
    echo "<p style='color:red;'>Client search FAILED: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Test job types
echo "<h3>Job Types Test:</h3>";
try {
    $types = DB::table('cims_job_card_types')->where('is_active', 1)->limit(5)->get();
    echo "<p style='color:green;'>Job types query works (cims_ prefix)! Found " . $types->count() . " types.</p>";
} catch (\Exception $e) {
    echo "<p style='color:red;'>Job types query FAILED with cims_ prefix: " . htmlspecialchars($e->getMessage()) . "</p>";
    try {
        $types = DB::table('job_card_types')->where('is_active', 1)->limit(5)->get();
        echo "<p style='color:orange;'>OLD table name still works! Tables have NOT been renamed yet.</p>";
        echo "<p><strong>Please run the rename script first:</strong> <a href='/run_jobcards_rename_tables.php'>run_jobcards_rename_tables.php</a></p>";
    } catch (\Exception $e2) {
        echo "<p style='color:red;'>Neither old nor new table names work!</p>";
    }
}
