<?php
header('Content-Type: text/plain');

echo "=== Diagnostic v3 ===\n\n";

try {
    require __DIR__ . '/application/vendor/autoload.php';
    $app = require_once __DIR__ . '/application/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->handle(Illuminate\Http\Request::capture());

    // 1. Find the correct client table name
    echo "=== All tables containing 'client' ===\n";
    $tables = \DB::select("SHOW TABLES LIKE '%client%'");
    foreach ($tables as $t) {
        $vals = get_object_vars($t);
        echo "  " . array_values($vals)[0] . "\n";
    }

    // 2. Find tables containing 'document'
    echo "\n=== All tables containing 'document' ===\n";
    $tables = \DB::select("SHOW TABLES LIKE '%document%'");
    foreach ($tables as $t) {
        $vals = get_object_vars($t);
        echo "  " . array_values($vals)[0] . "\n";
    }

    // 3. Check what the ClientMaster model's table is
    echo "\n=== ClientMaster model table ===\n";
    $model = new \Modules\cims_pm_pro\Models\ClientMaster();
    echo "Table: " . $model->getTable() . "\n";
    echo "Primary Key: " . $model->getKeyName() . "\n";

    // 4. Check what the Document model's table is
    echo "\n=== Document model table ===\n";
    $docModel = new \Modules\cims_pm_pro\Models\Document();
    echo "Table: " . $docModel->getTable() . "\n";
    echo "Primary Key: " . $docModel->getKeyName() . "\n";

    // 5. Try to get ALL documents with their full details
    echo "\n=== ALL documents in cims_documents ===\n";
    $docs = \DB::table('cims_documents')->get();
    if ($docs->isEmpty()) {
        echo "  NO documents found!\n";
    } else {
        foreach ($docs as $doc) {
            echo "  ---\n";
            foreach (get_object_vars($doc) as $key => $val) {
                echo "  {$key}: " . ($val ?? 'NULL') . "\n";
            }
        }
    }

    // 6. Try to get client records from whichever table exists
    echo "\n=== Trying client table queries ===\n";
    $possibleTables = ['cims_client_master', 'clients', 'client_master', 'cims_clients'];
    foreach ($possibleTables as $tbl) {
        try {
            $count = \DB::table($tbl)->count();
            echo "  {$tbl}: {$count} records\n";

            // Show first few with certificate columns
            $cols = \DB::getSchemaBuilder()->getColumnListing($tbl);
            $certCols = array_filter($cols, fn($c) => str_contains($c, 'cert') || str_contains($c, 'document') || str_contains($c, 'file') || str_contains($c, 'cor_'));
            echo "  Certificate-related columns: " . implode(', ', $certCols) . "\n";

            $first = \DB::table($tbl)->first();
            if ($first) {
                echo "  First record keys: " . implode(', ', array_keys(get_object_vars($first))) . "\n";
            }
        } catch (\Exception $e) {
            echo "  {$tbl}: " . $e->getMessage() . "\n";
        }
    }

    // 7. Check the view_client route
    echo "\n=== Route check ===\n";
    $route = \Route::getRoutes()->getByName('cimsdocmanager.view.client');
    if ($route) {
        echo "Route found: " . $route->uri() . "\n";
        echo "Action: " . $route->getActionName() . "\n";
    } else {
        echo "Route 'cimsdocmanager.view.client' NOT FOUND\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
