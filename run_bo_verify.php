<?php
/**
 * Verify BO service methods work without errors. DELETE after use.
 */
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>BO Verification</h2><pre>\n";

try {
    $service = new \Modules\JobCards\Services\JobCardService();
    echo "1. JobCardService instantiated OK\n";

    // Test getDirectorsForClient
    $result = $service->getDirectorsForClient(16);
    echo "2. getDirectorsForClient(16) returned: " . count($result['directors']) . " directors\n";
    echo "   Total shares: " . $result['totalShares'] . "\n";
    echo "   Share type: " . $result['shareType'] . "\n";

    if (!empty($result['directors'])) {
        $d = $result['directors'][0];
        echo "   First director: {$d->firstname} {$d->surname}\n";
        echo "   has_id_document: " . ($d->has_id_document ? 'true' : 'false') . "\n";
        echo "   has_poa: " . ($d->has_poa ? 'true' : 'false') . "\n";
        echo "   tax_number: " . ($d->tax_number ?: 'NULL') . "\n";
        echo "   calculated_percentage: {$d->calculated_percentage}%\n";
    }

    // Test getDirectorDocuments
    $docs = $service->getDirectorDocuments(1);
    echo "3. getDirectorDocuments(1): " . count($docs) . " director groups\n";

    echo "\n=== ALL OK ===\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "</pre>";
