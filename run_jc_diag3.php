<?php
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

echo "<h2>Client Master - Share/Director Columns</h2><pre>\n";

// Find share/director related columns in client_master
echo "=== CLIENT_MASTER COLUMNS WITH 'share' OR 'director' OR 'number' ===\n";
try {
    $cols = DB::select("SHOW COLUMNS FROM client_master");
    foreach ($cols as $c) {
        $f = strtolower($c->Field);
        if (strpos($f, 'share') !== false || strpos($f, 'director') !== false || strpos($f, 'number') !== false) {
            echo "  {$c->Field} ({$c->Type})\n";
        }
    }
} catch (\Exception $e) { echo "  ERROR: " . $e->getMessage() . "\n"; }

// Sample data for a client with shares
echo "\n=== SAMPLE CLIENT DATA (shares/directors fields) ===\n";
try {
    $rows = DB::select("SELECT client_id, company_name, client_code,
        COALESCE(number_of_directors, 'NULL') as num_dirs,
        COALESCE(number_of_shares, 'NULL') as num_shares,
        COALESCE(share_type, 'NULL') as share_type
        FROM client_master
        WHERE number_of_shares IS NOT NULL AND number_of_shares > 0
        LIMIT 10");
    foreach ($rows as $r) {
        echo "  client_id={$r->client_id} | {$r->company_name} ({$r->client_code}) | Directors: {$r->num_dirs} | Shares: {$r->num_shares} | Type: {$r->share_type}\n";
    }
    if (empty($rows)) echo "  No clients with shares found. Trying without filter...\n";
} catch (\Exception $e) {
    echo "  ERROR: " . $e->getMessage() . "\n";
    // Try alternate column names
    echo "\n  Trying alternate column search...\n";
    try {
        $cols = DB::select("SHOW COLUMNS FROM client_master");
        foreach ($cols as $c) echo "  {$c->Field} ({$c->Type})\n";
    } catch (\Exception $e2) { echo "  " . $e2->getMessage() . "\n"; }
}

// Check directors for the job card's client (client_id from recent job card)
echo "\n=== DIRECTORS FOR RECENT JOB CARD CLIENT ===\n";
try {
    $jc = DB::table('cims_job_cards')->orderBy('id', 'desc')->first();
    if ($jc) {
        echo "Job Card: {$jc->job_code}, client_id: {$jc->client_id}\n";
        $dirs = DB::select("SELECT id, client_id, person_id, firstname, surname, identity_number, number_of_director_shares, share_percentage, director_type_name, director_status_name, is_active, id_front_image, id_back_image FROM client_master_directors WHERE client_id = ?", [$jc->client_id]);
        foreach ($dirs as $d) {
            echo "  Dir ID={$d->id} | person_id={$d->person_id} | {$d->firstname} {$d->surname} | ID: {$d->identity_number} | Shares: {$d->number_of_director_shares} | %: {$d->share_percentage} | Type: {$d->director_type_name} | Status: {$d->director_status_name} | id_front: {$d->id_front_image} | id_back: {$d->id_back_image}\n";
        }
        if (empty($dirs)) echo "  No directors found for this client\n";

        // Get client share info
        $client = DB::table('client_master')->where('client_id', $jc->client_id)->first();
        if ($client) {
            $arr = (array)$client;
            $shareFields = array_filter($arr, function($v, $k) {
                return strpos(strtolower($k), 'share') !== false || strpos(strtolower($k), 'director') !== false;
            }, ARRAY_FILTER_USE_BOTH);
            echo "\n  Client share fields: " . json_encode($shareFields) . "\n";
        }
    }
} catch (\Exception $e) { echo "  ERROR: " . $e->getMessage() . "\n"; }

// Check person's ID document files
echo "\n=== PERSON ID DOCUMENTS ===\n";
try {
    $persons = DB::select("SELECT id, firstname, surname, identity_number, id_front_image, id_back_image, green_book_image, passport_image FROM cims_persons LIMIT 5");
    foreach ($persons as $p) {
        echo "  Person ID={$p->id} | {$p->firstname} {$p->surname} | ID: {$p->identity_number}\n";
        echo "    id_front: " . ($p->id_front_image ?: 'NULL') . "\n";
        echo "    id_back: " . ($p->id_back_image ?: 'NULL') . "\n";
        echo "    green_book: " . ($p->green_book_image ?: 'NULL') . "\n";
        echo "    passport: " . ($p->passport_image ?: 'NULL') . "\n";
    }
} catch (\Exception $e) { echo "  ERROR: " . $e->getMessage() . "\n"; }

echo "\n</pre>";
