<?php
header('Content-Type: text/plain');

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

// 1. Check persons table columns
echo "=== cims_persons columns ===\n";
$cols = \DB::getSchemaBuilder()->getColumnListing('cims_persons');
echo implode(', ', $cols) . "\n\n";

// 2. Get first person record
echo "=== First person record ===\n";
$person = \DB::table('cims_persons')->first();
if ($person) {
    foreach (get_object_vars($person) as $k => $v) {
        echo "  {$k}: " . ($v ?? 'NULL') . "\n";
    }
}

// 3. Check CIMSPersons route for ajax.person.get
echo "\n=== Route check ===\n";
$route = \Route::getRoutes()->getByName('cimspersons.ajax.person.get');
if ($route) {
    echo "Route found: " . $route->uri() . "\n";
    echo "Action: " . $route->getActionName() . "\n";
} else {
    echo "Route 'cimspersons.ajax.person.get' NOT FOUND\n";
}

// 4. Check Person model
echo "\n=== Person model ===\n";
try {
    $model = new \Modules\cims_pm_pro\Models\Person();
    echo "Table: " . $model->getTable() . "\n";
    echo "PK: " . $model->getKeyName() . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Also check CIMSPersons model
echo "\n=== CIMSPersons model check ===\n";
try {
    $cls = 'Modules\CIMSPersons\Models\Person';
    if (class_exists($cls)) {
        $m = new $cls();
        echo "Table: " . $m->getTable() . "\n";
    } else {
        echo "Class not found: {$cls}\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
