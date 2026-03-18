<?php
// Use Laravel's bootstrap
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

header('Content-Type: text/plain; charset=utf-8');

try {
    echo "=== 1. Custom Field Definitions ===\n";
    $fields = DB::table('tblcustomfields')->get();
    foreach ($fields as $f) {
        $arr = (array)$f;
        $cols = implode(' | ', array_map(function($k,$v){ return "$k=$v"; }, array_keys($arr), $arr));
        echo $cols . "\n";
    }

    echo "\n=== 2. tblcustomfieldsvalues structure ===\n";
    $cols = DB::select("SHOW COLUMNS FROM tblcustomfieldsvalues");
    foreach ($cols as $c) {
        echo "Field: {$c->Field} | Type: {$c->Type} | Key: {$c->Key}\n";
    }

    echo "\n=== 3. Values for fieldid=38 (first 20) ===\n";
    $rows = DB::table('tblcustomfieldsvalues')->where('fieldid', 38)->limit(20)->get();
    foreach ($rows as $r) {
        $arr = (array)$r;
        $cols = implode(' | ', array_map(function($k,$v){ return "$k=$v"; }, array_keys($arr), $arr));
        echo $cols . "\n";
    }

    echo "\n=== 4. JOIN client_id to relid (first 20) ===\n";
    $rows = DB::select("
        SELECT c.client_id, c.client_company_name, v.relid, v.value as client_code
        FROM clients c
        LEFT JOIN tblcustomfieldsvalues v ON c.client_id = v.relid AND v.fieldid = 38
        ORDER BY c.client_company_name
        LIMIT 20
    ");
    foreach ($rows as $r) {
        echo "client_id={$r->client_id} | relid=" . ($r->relid ?? 'NULL') . " | code=" . ($r->client_code ?? 'NULL') . " | name={$r->client_company_name}\n";
    }

    echo "\n=== 5. ATP search ===\n";
    $rows = DB::table('tblcustomfieldsvalues')->where('value', 'like', '%ATP%')->get();
    foreach ($rows as $r) {
        $arr = (array)$r;
        echo implode(' | ', array_map(function($k,$v){ return "$k=$v"; }, array_keys($arr), $arr)) . "\n";
    }

    echo "\n=== 6. NOM search ===\n";
    $rows = DB::table('tblcustomfieldsvalues')->where('value', 'like', '%NOM%')->get();
    foreach ($rows as $r) {
        $arr = (array)$r;
        echo implode(' | ', array_map(function($k,$v){ return "$k=$v"; }, array_keys($arr), $arr)) . "\n";
    }

    echo "\n=== 7. Clients with 'Accounting' in name ===\n";
    $rows = DB::table('clients')->where('client_company_name', 'like', '%Accounting%')->limit(5)->get();
    foreach ($rows as $r) {
        echo "client_id={$r->client_id} | name={$r->client_company_name}\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

@unlink(__FILE__);
