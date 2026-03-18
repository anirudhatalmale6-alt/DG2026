<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// 1. Check existing document types
echo "<h3>Existing Document Types:</h3><pre>";
$types = DB::table('cims_document_types')->get();
foreach ($types as $t) {
    echo "id={$t->id} | ref={$t->doc_ref} | group={$t->doc_group} | name={$t->name} | active={$t->is_active}\n";
}
echo "</pre>";

// 2. Check cims_document_types columns
echo "<h3>Document Types Table Structure:</h3><pre>";
$cols = DB::select("SHOW COLUMNS FROM cims_document_types");
foreach ($cols as $c) {
    echo $c->Field . " | " . $c->Type . "\n";
}
echo "</pre>";

// 3. Check cims_documents columns
echo "<h3>Documents Table Structure:</h3><pre>";
$cols = DB::select("SHOW COLUMNS FROM cims_documents");
foreach ($cols as $c) {
    echo $c->Field . " | " . $c->Type . "\n";
}
echo "</pre>";

// 4. Check existing flag columns on emp201 declarations
echo "<h3>EMP201 file-related columns:</h3><pre>";
$cols = DB::select("SHOW COLUMNS FROM cims_emp201_declarations LIKE '%file%'");
foreach ($cols as $c) {
    echo $c->Field . " | " . $c->Type . "\n";
}
$cols2 = DB::select("SHOW COLUMNS FROM cims_emp201_declarations LIKE '%uploaded%'");
foreach ($cols2 as $c) {
    echo $c->Field . " | " . $c->Type . "\n";
}
echo "</pre>";

// 5. Check the Document model namespace/path
echo "<h3>Document model check:</h3><pre>";
try {
    $doc = new \Modules\cims_pm_pro\Models\Document();
    echo "Model loaded OK: " . get_class($doc) . "\n";
    echo "Table: " . $doc->getTable() . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "</pre>";

// 6. Check the view_client route exists
echo "<h3>Route check:</h3><pre>";
try {
    $url = route('cimsdocmanager.view.client', ['client_id' => 1, 'document' => 'test']);
    echo "cimsdocmanager.view.client route: $url\n";
} catch (\Exception $e) {
    echo "Route error: " . $e->getMessage() . "\n";
}
try {
    $url = route('cimsdocmanager.download', ['id' => 1]);
    echo "cimsdocmanager.download route: $url\n";
} catch (\Exception $e) {
    echo "Download route error: " . $e->getMessage() . "\n";
}
echo "</pre>";
