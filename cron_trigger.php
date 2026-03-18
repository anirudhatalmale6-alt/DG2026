<?php
/**
 * Web-based cron trigger for GrowCRM email queue
 * Protected by a secret key
 */
$secret = 'atp2026cron';
if (!isset($_GET['key']) || $_GET['key'] !== $secret) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

chdir(__DIR__ . '/application');
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Use Laravel's container to properly resolve dependencies
try {
    $app->call('App\Cronjobs\EmailBillsCron');
    echo "EmailBillsCron: OK\n";
} catch (\Exception $e) {
    echo "EmailBillsCron error: " . $e->getMessage() . "\n";
}

try {
    $app->call('App\Cronjobs\EmailCron');
    echo "EmailCron: OK\n";
} catch (\Exception $e) {
    echo "EmailCron error: " . $e->getMessage() . "\n";
}
