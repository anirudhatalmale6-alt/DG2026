<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());
use Illuminate\Support\Facades\DB;
header('Content-Type: text/plain; charset=utf-8');

// Field 38 definition
$f38 = DB::table('tblcustomfields')->where('id', 38)->first();
echo "FIELD38: " . json_encode($f38) . "\n\n";

// All fieldid=38 values with fieldto
echo "FIELD38_VALUES:\n";
$vals = DB::table('tblcustomfieldsvalues')->where('fieldid', 38)->get();
foreach ($vals as $v) {
    echo "id={$v->id}|relid={$v->relid}|fieldid={$v->fieldid}|fieldto={$v->fieldto}|value={$v->value}\n";
}

echo "\nCLIENTS_WITH_ACCOUNTING:\n";
$clients = DB::table('clients')->where('client_company_name', 'like', '%Accounting%')->get(['client_id','client_company_name']);
foreach ($clients as $c) {
    echo "client_id={$c->client_id}|name={$c->client_company_name}\n";
}

echo "\nJOIN_RESULT:\n";
$rows = DB::select("
    SELECT c.client_id, c.client_company_name, v.relid, v.fieldto, v.value
    FROM clients c
    LEFT JOIN tblcustomfieldsvalues v ON c.client_id = v.relid AND v.fieldid = 38
    ORDER BY c.client_company_name LIMIT 25
");
foreach ($rows as $r) {
    echo "cid={$r->client_id}|relid=".($r->relid??'NULL')."|fieldto=".($r->fieldto??'NULL')."|code=".($r->value??'NULL')."|name={$r->client_company_name}\n";
}

echo "\nATP_SEARCH:\n";
$atp = DB::table('tblcustomfieldsvalues')->where('value','like','%ATP%')->get();
foreach ($atp as $a) { echo "id={$a->id}|relid={$a->relid}|fieldid={$a->fieldid}|fieldto={$a->fieldto}|value={$a->value}\n"; }

echo "\nNOM_SEARCH:\n";
$nom = DB::table('tblcustomfieldsvalues')->where('value','like','%NOM%')->get();
foreach ($nom as $n) { echo "id={$n->id}|relid={$n->relid}|fieldid={$n->fieldid}|fieldto={$n->fieldto}|value={$n->value}\n"; }

@unlink(__FILE__);
