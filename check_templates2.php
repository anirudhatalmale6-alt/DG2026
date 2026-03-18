<?php
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

$templates = DB::table('cims_email_templates')->select('id','name','category')->orderBy('category')->orderBy('name')->get();
echo json_encode($templates, JSON_PRETTY_PRINT);
