<?php
error_reporting(0);
define('LARAVEL_START', microtime(true));
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());
\Artisan::call('view:clear');
\Artisan::call('cache:clear');
\Artisan::call('config:clear');
\Artisan::call('route:clear');
echo "All caches cleared: view, cache, config, route";
