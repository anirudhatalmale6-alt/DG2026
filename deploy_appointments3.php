<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre>\n";
echo "=== CIMSAppointments Deployment v3 ===\n\n";

// The app uses public_html as webroot, application/ is the Laravel base
// public_html/index.php should bootstrap properly
$appBase = dirname(__DIR__) . '/application';
if (!is_dir($appBase)) {
    $appBase = __DIR__ . '/application';
}
echo "App base: $appBase\n";
echo "Script dir: " . __DIR__ . "\n";

// Check for vendor autoload
$autoload = $appBase . '/vendor/autoload.php';
$bootstrap = $appBase . '/bootstrap/app.php';

echo "Autoload: " . (file_exists($autoload) ? "EXISTS" : "MISSING") . " ($autoload)\n";
echo "Bootstrap: " . (file_exists($bootstrap) ? "EXISTS" : "MISSING") . " ($bootstrap)\n\n";

if (!file_exists($autoload) || !file_exists($bootstrap)) {
    // Try to find it
    echo "Searching...\n";
    $search = glob('/usr/www/users/smartucbmh/*/vendor/autoload.php');
    foreach ($search as $f) echo "Found: $f\n";
    $search2 = glob('/usr/www/users/smartucbmh/*/*/vendor/autoload.php');
    foreach ($search2 as $f) echo "Found: $f\n";
    exit;
}

try {
    echo "--- Loading Laravel ---\n";
    require $autoload;
    $app = require $bootstrap;
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->handle(Illuminate\Http\Request::capture());
    echo "Laravel loaded OK\n\n";

    // Run migrations
    echo "--- Running Migrations ---\n";
    $exitCode = Artisan::call('migrate', ['--force' => true]);
    echo Artisan::output();
    echo "Exit code: $exitCode\n\n";

    // Verify tables
    echo "--- Verifying Tables ---\n";
    $tables = [
        'cims_appointments_services',
        'cims_appointments_staff',
        'cims_appointments_staff_services',
        'cims_appointments_availability',
        'cims_appointments_blocked_dates',
        'cims_appointments',
        'cims_appointments_settings',
    ];
    foreach ($tables as $table) {
        $exists = \Illuminate\Support\Facades\Schema::hasTable($table);
        echo "$table: " . ($exists ? "OK" : "MISSING") . "\n";
    }
    echo "\n";

    // Check routes
    echo "--- Checking Routes ---\n";
    $routes = ['cimsappointments.dashboard', 'cimsappointments.appointments.index', 'cimsappointments.appointments.create'];
    foreach ($routes as $name) {
        echo "$name: " . (\Illuminate\Support\Facades\Route::has($name) ? "REGISTERED" : "NOT FOUND") . "\n";
    }
    echo "\n";

    // Check settings seeded
    echo "--- Settings ---\n";
    $count = \Illuminate\Support\Facades\DB::table('cims_appointments_settings')->count();
    echo "Settings records: $count\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "FATAL: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Done ===\n";
echo "</pre>";
