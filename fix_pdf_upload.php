<?php
$basePaths = [__DIR__ . '/../application', __DIR__ . '/../../application', __DIR__ . '/..'];
$bootstrapped = false;
foreach ($basePaths as $base) {
    if (file_exists($base . '/bootstrap/app.php')) {
        if (file_exists($base . '/bootstrap/autoload.php')) require $base . '/bootstrap/autoload.php';
        elseif (file_exists($base . '/vendor/autoload.php')) require $base . '/vendor/autoload.php';
        $app = require_once $base . '/bootstrap/app.php';
        $bootstrapped = true;
        break;
    }
}
if (!$bootstrapped) die("Could not find Laravel bootstrap.");
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "<pre>\n=== FIX PDF UPLOAD ===\n\n";

// -------------------------------------------------------
// 1. Add missing document types
// -------------------------------------------------------
echo "=== ADDING MISSING DOCUMENT TYPES ===\n";

$missingDocTypes = [
    [
        'name' => 'Payroll Notice of Registration',
        'is_active' => 1,
        'category_id' => 1,
        'doc_ref' => 'PAYROLL REG',
        'doc_group' => 'SARS',
        'description' => 'SARS Payroll Notice of Registration',
        'lead_time_days' => 0,
        'has_expiry' => 0,
        'days_to_expire' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'VAT Notice of Registration',
        'is_active' => 1,
        'category_id' => 1,
        'doc_ref' => 'VAT REG',
        'doc_group' => 'SARS',
        'description' => 'SARS VAT Notice of Registration',
        'lead_time_days' => 0,
        'has_expiry' => 0,
        'days_to_expire' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'SARS Representative',
        'is_active' => 1,
        'category_id' => 1,
        'doc_ref' => 'SARS REP',
        'doc_group' => 'SARS',
        'description' => 'SARS Representative Registration',
        'lead_time_days' => 0,
        'has_expiry' => 0,
        'days_to_expire' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'Confirmation of Banking',
        'is_active' => 1,
        'category_id' => 1,
        'doc_ref' => 'BANK CONFIRM',
        'doc_group' => 'BANKING',
        'description' => 'Bank Confirmation Letter / Statement',
        'lead_time_days' => 0,
        'has_expiry' => 0,
        'days_to_expire' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

foreach ($missingDocTypes as $docType) {
    $exists = DB::table('cims_document_types')->where('doc_ref', $docType['doc_ref'])->exists();
    if ($exists) {
        echo "  [SKIP] doc_ref='{$docType['doc_ref']}' already exists\n";
    } else {
        DB::table('cims_document_types')->insert($docType);
        echo "  [INSERT] doc_ref='{$docType['doc_ref']}' - {$docType['name']}\n";
    }
}

// Verify all needed doc_refs now exist
echo "\n=== VERIFY DOCUMENT TYPES ===\n";
$needed = ['COR 14.3', 'ITAX REG', 'PAYROLL REG', 'VAT REG', 'SARS REP', 'BANK CONFIRM'];
foreach ($needed as $ref) {
    $found = DB::table('cims_document_types')->where('doc_ref', $ref)->first();
    echo "  " . ($found ? "OK" : "MISSING") . ": doc_ref='$ref'" . ($found ? " (id={$found->id})" : "") . "\n";
}

// -------------------------------------------------------
// 2. Fix storage symlink
// -------------------------------------------------------
echo "\n=== STORAGE SYMLINK ===\n";

// The actual storage path used by the 'public' disk
$storageDiskRoot = config('filesystems.disks.public.root');
echo "  Public disk root from config: $storageDiskRoot\n";

// The actual app storage path
$appStoragePath = storage_path('app/public');
echo "  App storage path: $appStoragePath\n";

// Create the symlink for public access
$publicStoragePath = public_path('storage');
echo "  public_path('storage'): $publicStoragePath\n";

if (is_link($publicStoragePath)) {
    echo "  Symlink already exists -> " . readlink($publicStoragePath) . "\n";
} elseif (file_exists($publicStoragePath)) {
    echo "  Path exists but is NOT a symlink\n";
} else {
    // Create symlink: public/storage -> ../storage/app/public
    // But the public disk root is /usr/www/users/smartucbmh/storage, not inside application
    // Let's create the symlink pointing to the actual disk root
    $target = $storageDiskRoot;
    if (!file_exists($target)) {
        mkdir($target, 0755, true);
        echo "  Created storage disk root: $target\n";
    }

    // Also ensure the app storage exists
    if (!file_exists($appStoragePath)) {
        mkdir($appStoragePath, 0755, true);
        echo "  Created app storage path: $appStoragePath\n";
    }

    // Create symlink
    $result = @symlink($target, $publicStoragePath);
    if ($result) {
        echo "  Created symlink: $publicStoragePath -> $target\n";
    } else {
        echo "  FAILED to create symlink. Trying alternative...\n";
        // Try to create the directory as a regular dir instead
        // and set the public disk root to use app storage
    }
}

// Also need symlink in the actual public_html/public/ directory
// since public_path() points to application/public but actual web root is public_html/public
$webPublicStorage = dirname(dirname(base_path())) . '/public/storage';
echo "\n  Web public storage path: $webPublicStorage\n";

if (is_link($webPublicStorage)) {
    echo "  Web symlink already exists -> " . readlink($webPublicStorage) . "\n";
} elseif (file_exists($webPublicStorage)) {
    echo "  Web path exists but is NOT a symlink\n";
} else {
    // Create symlink to the storage disk root
    $result = @symlink($storageDiskRoot, $webPublicStorage);
    if ($result) {
        echo "  Created web symlink: $webPublicStorage -> $storageDiskRoot\n";
    } else {
        echo "  FAILED to create web symlink\n";
    }
}

// -------------------------------------------------------
// 3. Ensure client_docs directory exists in storage
// -------------------------------------------------------
echo "\n=== CLIENT DOCS DIRECTORY ===\n";

$clientDocsPath = $storageDiskRoot . '/client_docs';
if (!file_exists($clientDocsPath)) {
    mkdir($clientDocsPath, 0755, true);
    echo "  Created: $clientDocsPath\n";
} else {
    echo "  Exists: $clientDocsPath\n";
}

// Also in app storage
$appClientDocs = $appStoragePath . '/client_docs';
if (!file_exists($appClientDocs)) {
    mkdir($appClientDocs, 0755, true);
    echo "  Created: $appClientDocs\n";
} else {
    echo "  Exists: $appClientDocs\n";
}

// -------------------------------------------------------
// 4. Verify final state
// -------------------------------------------------------
echo "\n=== FINAL VERIFICATION ===\n";

// Test file upload to public disk
try {
    \Illuminate\Support\Facades\Storage::disk('public')->put('client_docs/test_upload.txt', 'PDF upload test');
    $path = \Illuminate\Support\Facades\Storage::disk('public')->path('client_docs/test_upload.txt');
    echo "  Storage write test: SUCCESS at $path\n";
    echo "  File exists: " . (file_exists($path) ? 'YES' : 'NO') . "\n";
    \Illuminate\Support\Facades\Storage::disk('public')->delete('client_docs/test_upload.txt');
    echo "  Cleanup: done\n";
} catch (\Exception $e) {
    echo "  Storage write test FAILED: " . $e->getMessage() . "\n";
}

echo "\n=== DONE ===\n</pre>";
