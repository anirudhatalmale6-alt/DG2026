<?php
/** Verify BODocumentService works. DELETE after use. */
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>BO Service Verification</h2><pre>\n";

try {
    $jcService = new \Modules\JobCards\Services\JobCardService();
    echo "1. JobCardService OK\n";

    $boService = new \Modules\JobCards\Services\BODocumentService($jcService);
    echo "2. BODocumentService OK\n";

    // Test getBOData
    $data = $boService->getBOData(16);
    echo "3. getBOData(16) returned client: " . ($data['client']->company_name ?? 'NULL') . "\n";
    echo "   Directors: " . count($data['directors']) . "\n";
    echo "   Agent code: " . $data['agentCode'] . "\n";
    echo "   Total shares: " . $data['totalShares'] . "\n";

    if (count($data['directors'])) {
        $d = $data['directors'][0];
        echo "   Dir 1: {$d->firstname} {$d->surname}\n";
        echo "     Address: " . ($d->person_address_line ?: $d->address_line ?: 'NULL') . "\n";
        echo "     City: " . ($d->person_city ?: $d->city ?: 'NULL') . "\n";
        echo "     Province: " . ($d->person_province ?: $d->province ?: 'NULL') . "\n";
        echo "     Signature: " . ($d->signature_image ?: 'NULL') . "\n";
    }

    echo "\n=== ALL OK ===\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo substr($e->getTraceAsString(), 0, 1000) . "\n";
}
echo "</pre>";
