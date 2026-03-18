<?php
/**
 * Rename all Job Cards tables to use the cims_ prefix for consistency.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

echo "<h2>Renaming Job Card Tables to CIMS Convention</h2>";

$renames = [
    'job_card_types'          => 'cims_job_card_types',
    'job_card_type_steps'     => 'cims_job_card_type_steps',
    'job_card_type_fields'    => 'cims_job_card_type_fields',
    'job_card_type_documents' => 'cims_job_card_type_documents',
    'job_cards'               => 'cims_job_cards',
    'job_card_progress'       => 'cims_job_card_progress',
    'job_card_attachments'    => 'cims_job_card_attachments',
];

$success = 0;
$skipped = 0;
$errors = 0;

echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse:collapse;font-family:sans-serif;'>";
echo "<tr style='background:#f0f0f0;'><th>Old Name</th><th>New Name</th><th>Status</th></tr>";

foreach ($renames as $oldName => $newName) {
    // Check if old table exists
    $oldExists = DB::select("SHOW TABLES LIKE '{$oldName}'");
    $newExists = DB::select("SHOW TABLES LIKE '{$newName}'");

    if (!empty($newExists)) {
        echo "<tr><td>{$oldName}</td><td>{$newName}</td><td style='color:blue;'>Already renamed (skipped)</td></tr>";
        $skipped++;
        continue;
    }

    if (empty($oldExists)) {
        echo "<tr><td>{$oldName}</td><td>{$newName}</td><td style='color:orange;'>Old table not found (skipped)</td></tr>";
        $skipped++;
        continue;
    }

    try {
        DB::statement("RENAME TABLE `{$oldName}` TO `{$newName}`");
        echo "<tr><td>{$oldName}</td><td style='color:green;font-weight:bold;'>{$newName}</td><td style='color:green;'>Renamed OK</td></tr>";
        $success++;
    } catch (\Exception $e) {
        echo "<tr><td>{$oldName}</td><td>{$newName}</td><td style='color:red;'>ERROR: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
        $errors++;
    }
}

echo "</table>";
echo "<br><p><strong>Results:</strong> {$success} renamed, {$skipped} skipped, {$errors} errors</p>";

if ($errors === 0) {
    echo "<p style='color:green;font-weight:bold;font-size:16px;'>All tables renamed successfully!</p>";
} else {
    echo "<p style='color:red;font-weight:bold;'>Some errors occurred. Please check above.</p>";
}
