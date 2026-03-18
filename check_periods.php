<?php
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

// Table structure
$cols = DB::select("SHOW COLUMNS FROM cims_document_periods");
echo "COLUMNS:\n";
foreach ($cols as $c) { echo "  {$c->Field} ({$c->Type})\n"; }

// Sample data (latest 10)
$rows = DB::table('cims_document_periods')->orderByDesc('id')->limit(10)->get();
echo "\nSAMPLE DATA (latest 10):\n";
echo json_encode($rows, JSON_PRETTY_PRINT);

// Total count
$count = DB::table('cims_document_periods')->count();
echo "\n\nTOTAL COUNT: " . $count;
