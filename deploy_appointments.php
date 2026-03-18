<?php
/**
 * Deploy CIMSAppointments - Run migrations and clear caches
 * Upload to public_html/ and access via browser, then delete.
 */

// Bootstrap Laravel
require __DIR__ . '/application/bootstrap/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "<pre>\n";
echo "=== CIMSAppointments Deployment ===\n\n";

// 1. Clear bootstrap cache files for the module
echo "--- Clearing Bootstrap Cache ---\n";
$cacheFiles = glob(base_path('bootstrap/cache/*.php'));
$cleared = 0;
foreach ($cacheFiles as $file) {
    $basename = basename($file);
    if (in_array($basename, ['packages.php', 'services.php']) || stripos($basename, 'appointment') !== false) {
        unlink($file);
        echo "Deleted: $basename\n";
        $cleared++;
    }
}
echo "Cleared $cleared cache files\n\n";

// 2. Clear compiled views
echo "--- Clearing Compiled Views ---\n";
$viewFiles = glob(base_path('storage/framework/views/*.php'));
$viewCount = count($viewFiles);
foreach ($viewFiles as $file) {
    unlink($file);
}
echo "Cleared $viewCount compiled views\n\n";

// 3. Clear config cache
echo "--- Clearing Config Cache ---\n";
$configCache = base_path('bootstrap/cache/config.php');
if (file_exists($configCache)) {
    unlink($configCache);
    echo "Deleted config cache\n";
} else {
    echo "No config cache to clear\n";
}
echo "\n";

// 4. Clear route cache
echo "--- Clearing Route Cache ---\n";
$routeCache = base_path('bootstrap/cache/routes-v7.php');
if (file_exists($routeCache)) {
    unlink($routeCache);
    echo "Deleted route cache\n";
} else {
    echo "No route cache to clear\n";
}
$routeCache2 = base_path('bootstrap/cache/routes.php');
if (file_exists($routeCache2)) {
    unlink($routeCache2);
    echo "Deleted routes.php cache\n";
}
echo "\n";

// 5. Run migrations
echo "--- Running Migrations ---\n";
try {
    $exitCode = Artisan::call('migrate', ['--force' => true]);
    echo Artisan::output();
    echo "Migration exit code: $exitCode\n";
} catch (Exception $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
echo "\n";

// 6. Verify tables exist
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
    try {
        $exists = \Illuminate\Support\Facades\Schema::hasTable($table);
        echo "$table: " . ($exists ? "OK" : "MISSING") . "\n";
    } catch (Exception $e) {
        echo "$table: ERROR - " . $e->getMessage() . "\n";
    }
}
echo "\n";

// 7. Check module is loaded
echo "--- Checking Module Status ---\n";
try {
    $moduleStatuses = json_decode(file_get_contents(base_path('modules_statuses.json')), true);
    echo "CIMSAppointments in modules_statuses.json: " . (isset($moduleStatuses['CIMSAppointments']) ? ($moduleStatuses['CIMSAppointments'] ? 'true' : 'false') : 'NOT FOUND') . "\n";
} catch (Exception $e) {
    echo "Error reading modules_statuses.json: " . $e->getMessage() . "\n";
}
echo "\n";

// 8. Check if routes are registered
echo "--- Checking Routes ---\n";
$routeNames = ['cimsappointments.dashboard', 'cimsappointments.appointments.index', 'cimsappointments.appointments.create'];
foreach ($routeNames as $name) {
    $exists = \Illuminate\Support\Facades\Route::has($name);
    echo "$name: " . ($exists ? "REGISTERED" : "NOT FOUND") . "\n";
}
echo "\n";

// 9. Check settings were seeded
echo "--- Checking Settings ---\n";
try {
    $settingsCount = \Illuminate\Support\Facades\DB::table('cims_appointments_settings')->count();
    echo "Settings records: $settingsCount\n";
} catch (Exception $e) {
    echo "Settings check failed: " . $e->getMessage() . "\n";
}

echo "\n=== Deployment Complete ===\n";
echo "</pre>";
