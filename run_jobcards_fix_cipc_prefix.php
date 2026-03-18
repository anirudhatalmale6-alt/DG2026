<?php
/**
 * Fix: Add "CIPC - " prefix to all CIPC job types that don't already have it.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

echo "<h2>CIPC Prefix Fix</h2>";

// Get all CIPC types that don't start with "CIPC"
$types = DB::table('job_card_types')
    ->where('submission_to', 'CIPC')
    ->where('name', 'NOT LIKE', 'CIPC%')
    ->get();

if ($types->isEmpty()) {
    echo "<p style='color:green;'>All CIPC job types already have the CIPC prefix. Nothing to fix.</p>";
} else {
    echo "<p>Found <strong>" . $types->count() . "</strong> CIPC types without prefix. Fixing...</p><ul>";

    foreach ($types as $t) {
        $newName = 'CIPC - ' . $t->name;
        DB::table('job_card_types')
            ->where('id', $t->id)
            ->update(['name' => $newName, 'updated_at' => now()]);

        echo "<li><span style='color:#999;text-decoration:line-through;'>" . htmlspecialchars($t->name)
            . "</span> &rarr; <strong style='color:green;'>" . htmlspecialchars($newName) . "</strong></li>";
    }

    echo "</ul><p style='color:green;font-weight:bold;'>Done! All CIPC types now have the prefix.</p>";
}

// Show final list of all CIPC types
echo "<h3>All CIPC Job Types:</h3><ol>";
$allCipc = DB::table('job_card_types')
    ->where('submission_to', 'CIPC')
    ->orderBy('display_order')
    ->get();
foreach ($allCipc as $t) {
    echo "<li>" . htmlspecialchars($t->name) . "</li>";
}
echo "</ol>";
echo "<p>Total CIPC types: <strong>" . $allCipc->count() . "</strong></p>";
