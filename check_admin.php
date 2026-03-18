<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$appBase = __DIR__ . '/application';
require $appBase . '/vendor/autoload.php';
$app = require $appBase . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "<pre>\n";

// Check admin users
$users = DB::table('users')->select('id', 'email', 'first_name', 'last_name', 'role_id', 'status')->limit(5)->get();
echo "--- Users ---\n";
foreach ($users as $u) {
    echo "ID:{$u->id} | {$u->email} | {$u->first_name} {$u->last_name} | Role:{$u->role_id} | Status:{$u->status}\n";
}

// Check routes count
echo "\n--- Route Count ---\n";
$allRoutes = Route::getRoutes();
$apptRoutes = 0;
foreach ($allRoutes as $route) {
    $name = $route->getName();
    if ($name && strpos($name, 'cimsappointments') !== false) {
        $apptRoutes++;
        echo "  $name => " . $route->uri() . "\n";
    }
}
echo "Total appointment routes: $apptRoutes\n";

echo "\n--- Module Path ---\n";
echo "module_path: " . module_path('CIMSAppointments') . "\n";
echo "Routes file exists: " . (file_exists(module_path('CIMSAppointments', '/Routes/web.php')) ? 'YES' : 'NO') . "\n";

echo "</pre>";

// Self-delete
unlink(__FILE__);
