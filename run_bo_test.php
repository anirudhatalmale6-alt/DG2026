<?php
/**
 * Test Beneficial Ownership directors endpoint logic.
 * DELETE after use.
 */
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<h2>BO Directors Test</h2><pre>\n";

// Find a job card with directors
$jc = DB::table('cims_job_cards')->orderBy('id', 'desc')->first();
if (!$jc) { echo "No job cards found.\n</pre>"; exit; }
echo "Job Card: {$jc->job_code} (ID: {$jc->id}), client_id: {$jc->client_id}\n\n";

// Get client info
$client = DB::table('client_master')
    ->where('client_id', $jc->client_id)
    ->first();

if ($client) {
    echo "Company: {$client->company_name} ({$client->client_code})\n";

    // Check for share-related columns
    $arr = (array)$client;
    foreach ($arr as $k => $v) {
        if (stripos($k, 'share') !== false || stripos($k, 'number_of') !== false || stripos($k, 'registration') !== false) {
            echo "  {$k} = " . ($v ?: 'NULL') . "\n";
        }
    }
}

echo "\n=== Directors for this client ===\n";
$dirs = DB::table('client_master_directors as d')
    ->leftJoin('cims_persons as p', 'd.person_id', '=', 'p.id')
    ->where('d.client_id', $jc->client_id)
    ->select([
        'd.id', 'd.person_id', 'd.firstname', 'd.surname', 'd.identity_number',
        'd.number_of_director_shares', 'd.share_percentage',
        'd.director_type_name', 'd.director_status_name', 'd.is_active',
        'd.id_front_image as dir_id_front', 'd.id_back_image as dir_id_back',
        'p.id as p_id', 'p.id_front_image as p_id_front', 'p.id_back_image as p_id_back',
        'p.passport_image', 'p.signature_image', 'p.poa_image', 'p.tax_number',
    ])
    ->get();

echo "Found: " . count($dirs) . " directors\n\n";
foreach ($dirs as $d) {
    echo "  Dir ID={$d->id} | person_id={$d->person_id} | {$d->firstname} {$d->surname}\n";
    echo "    ID#: {$d->identity_number} | Tax#: " . ($d->tax_number ?: 'NULL') . "\n";
    echo "    Type: {$d->director_type_name} | Status: {$d->director_status_name}\n";
    echo "    Shares: {$d->number_of_director_shares} | %: {$d->share_percentage}\n";
    echo "    ID Docs: dir_front=" . ($d->dir_id_front ?: 'NULL') . " | p_front=" . ($d->p_id_front ?: 'NULL') . "\n";
    echo "    Signature: " . ($d->signature_image ?: 'NULL') . " | POA: " . ($d->poa_image ?: 'NULL') . "\n\n";
}

// Check cims_share_certificates table
echo "=== Share Certificates Table ===\n";
$count = DB::table('cims_share_certificates')->count();
echo "Rows: {$count}\n";

// Check attachments table columns
echo "\n=== Attachments Table Columns (new ones) ===\n";
$cols = DB::select("SHOW COLUMNS FROM cims_job_card_attachments");
foreach ($cols as $c) {
    if (in_array($c->Field, ['director_id', 'document_category'])) {
        echo "  {$c->Field} ({$c->Type})\n";
    }
}

echo "\n</pre>";
