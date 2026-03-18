<?php
header('Content-Type: application/json');

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

$result = [];

// Discover table columns
$result['client_master_cols'] = collect(DB::select("SHOW COLUMNS FROM client_master"))
    ->pluck('Field')->toArray();

$result['periods_cols'] = collect(DB::select("SHOW COLUMNS FROM cims_document_periods"))
    ->pluck('Field')->toArray();

$result['emp201_cols'] = collect(DB::select("SHOW COLUMNS FROM cims_emp201_declarations"))
    ->pluck('Field')->toArray();

echo json_encode($result, JSON_PRETTY_PRINT);
