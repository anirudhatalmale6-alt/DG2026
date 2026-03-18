<?php
require __DIR__ . '/../application/vendor/autoload.php';
$app = require_once __DIR__ . '/../application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Modules\cims_pm_pro\Models\ClientMaster;
use Modules\cims_pm_pro\Models\Document;

// Check client 13 (ATP200 based on the edit URL)
$client = ClientMaster::find(13);
if (!$client) {
    echo "Client 13 not found\n";
    exit;
}

echo "Client: {$client->client_code} - {$client->company_name}\n";
echo "cor_14_3_certificate: " . ($client->cor_14_3_certificate ?? 'NULL') . "\n";
echo "income_tax_registration: " . ($client->income_tax_registration ?? 'NULL') . "\n";
echo "cor_certificate_uploaded: " . ($client->cor_certificate_uploaded ?? 'NULL') . "\n\n";

// Try to find the document
if ($client->cor_14_3_certificate) {
    $doc = Document::where(['client_id' => $client->client_id, 'file_stored_name' => $client->cor_14_3_certificate])->first();
    if ($doc) {
        echo "Document found: ID={$doc->id}\n";
        echo "file_path: {$doc->file_path}\n";
        echo "file_stored_name: {$doc->file_stored_name}\n";
        $fullPath = storage_path('app/public/' . $doc->file_path);
        echo "Full path: {$fullPath}\n";
        echo "File exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
    } else {
        echo "No document found matching file_stored_name: {$client->cor_14_3_certificate}\n";
    }
} else {
    echo "cor_14_3_certificate field is empty\n";
}

echo "\nstorage_path(): " . storage_path() . "\n";
echo "storage_path('app/public/'): " . storage_path('app/public/') . "\n";

// List what's in storage
$dir = storage_path('app/public/client_docs');
echo "\nContents of {$dir}:\n";
if (is_dir($dir)) {
    $dirs = scandir($dir);
    foreach ($dirs as $d) {
        if ($d != '.' && $d != '..') echo "  - $d\n";
    }
} else {
    echo "  Directory does not exist!\n";
}
