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

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

echo "<pre>\n=== PDF UPLOAD DIAGNOSTIC ===\n\n";

// 1. Check cims_document_types table
echo "=== cims_document_types TABLE ===\n";
if (Schema::hasTable('cims_document_types')) {
    $count = DB::table('cims_document_types')->count();
    $cols = Schema::getColumnListing('cims_document_types');
    echo "  EXISTS: $count rows\n";
    echo "  Columns: " . implode(', ', $cols) . "\n";

    // Check for the specific doc_refs needed
    $needed = ['COR 14.3', 'ITAX REG', 'PAYROLL REG', 'VAT REG', 'SARS REP', 'BANK CONFIRM'];
    foreach ($needed as $ref) {
        $found = DB::table('cims_document_types')->where('doc_ref', $ref)->first();
        if ($found) {
            echo "  OK: doc_ref='$ref' -> id={$found->id}, name={$found->name}, doc_group={$found->doc_group}\n";
        } else {
            echo "  MISSING: doc_ref='$ref'\n";
        }
    }
} else {
    echo "  TABLE MISSING!\n";
}

// 2. Check cims_documents table
echo "\n=== cims_documents TABLE ===\n";
if (Schema::hasTable('cims_documents')) {
    $count = DB::table('cims_documents')->count();
    $cols = Schema::getColumnListing('cims_documents');
    echo "  EXISTS: $count rows\n";
    echo "  Columns: " . implode(', ', $cols) . "\n";
} else {
    echo "  TABLE MISSING!\n";
}

// 3. Check client_master_documents table
echo "\n=== client_master_documents TABLE ===\n";
if (Schema::hasTable('client_master_documents')) {
    $count = DB::table('client_master_documents')->count();
    $cols = Schema::getColumnListing('client_master_documents');
    echo "  EXISTS: $count rows\n";
    echo "  Columns: " . implode(', ', $cols) . "\n";
} else {
    echo "  TABLE MISSING!\n";
}

// 4. Check storage link
echo "\n=== STORAGE SETUP ===\n";
$publicPath = public_path('storage');
echo "  public_path('storage'): $publicPath\n";
echo "  Exists: " . (file_exists($publicPath) ? 'YES' : 'NO') . "\n";
if (is_link($publicPath)) {
    echo "  Is symlink: YES -> " . readlink($publicPath) . "\n";
} else {
    echo "  Is symlink: NO\n";
}

$storagePath = storage_path('app/public');
echo "  storage_path('app/public'): $storagePath\n";
echo "  Exists: " . (file_exists($storagePath) ? 'YES' : 'NO') . "\n";
echo "  Writable: " . (is_writable($storagePath) ? 'YES' : 'NO') . "\n";

// Check if client_docs dir exists
$clientDocsPath = storage_path('app/public/client_docs');
echo "  client_docs dir: $clientDocsPath\n";
echo "  Exists: " . (file_exists($clientDocsPath) ? 'YES' : 'NO') . "\n";

// 5. Check form enctype in blade
echo "\n=== FORM CONFIGURATION ===\n";
$bladePath = base_path('Modules/ClientMasterNew/Resources/views/clientmaster/clientmaster_create.blade.php');
if (file_exists($bladePath)) {
    $content = file_get_contents($bladePath);
    if (strpos($content, 'enctype="multipart/form-data"') !== false || strpos($content, "enctype='multipart/form-data'") !== false) {
        echo "  Form enctype: multipart/form-data - OK\n";
    } else {
        echo "  Form enctype: MISSING! (required for file uploads)\n";
    }

    // Check form action
    preg_match('/<form[^>]*action=["\']([^"\']*)["\'][^>]*>/i', $content, $matches);
    if ($matches) {
        echo "  Form action: {$matches[1]}\n";
    }
    preg_match('/<form[^>]*method=["\']([^"\']*)["\'][^>]*>/i', $content, $matches);
    if ($matches) {
        echo "  Form method: {$matches[1]}\n";
    }
} else {
    echo "  Blade file NOT FOUND at: $bladePath\n";
}

// 6. Check public path vs actual public
echo "\n=== PATH CONFIG ===\n";
echo "  base_path(): " . base_path() . "\n";
echo "  public_path(): " . public_path() . "\n";
echo "  storage_path(): " . storage_path() . "\n";
echo "  app.url: " . config('app.url') . "\n";

// 7. Check filesystem config
echo "\n=== FILESYSTEM CONFIG ===\n";
echo "  Default disk: " . config('filesystems.default') . "\n";
$publicDisk = config('filesystems.disks.public');
if ($publicDisk) {
    echo "  Public disk driver: " . ($publicDisk['driver'] ?? 'not set') . "\n";
    echo "  Public disk root: " . ($publicDisk['root'] ?? 'not set') . "\n";
    echo "  Public disk url: " . ($publicDisk['url'] ?? 'not set') . "\n";
}

// 8. Check PHP upload limits
echo "\n=== PHP UPLOAD LIMITS ===\n";
echo "  upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "  post_max_size: " . ini_get('post_max_size') . "\n";
echo "  max_file_uploads: " . ini_get('max_file_uploads') . "\n";

// 9. Test storage write
echo "\n=== STORAGE WRITE TEST ===\n";
try {
    Storage::disk('public')->put('test_write.txt', 'test');
    echo "  Write test: SUCCESS\n";
    Storage::disk('public')->delete('test_write.txt');
    echo "  Delete test: SUCCESS\n";
} catch (\Exception $e) {
    echo "  Write test FAILED: " . $e->getMessage() . "\n";
}

echo "\n=== DONE ===\n</pre>";
