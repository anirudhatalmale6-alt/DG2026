<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// 1. Add EMP201-specific document types if they don't exist
$emp201Types = [
    ['doc_ref' => 'EMP201_RETURN', 'doc_group' => 'EMP201', 'name' => 'SARS EMP201 Return', 'is_active' => 1],
    ['doc_ref' => 'EMP201_STATEMENT', 'doc_group' => 'EMP201', 'name' => 'SARS PAYE Statement', 'is_active' => 1],
    ['doc_ref' => 'EMP201_WORKING', 'doc_group' => 'EMP201', 'name' => 'EMP201 Working Papers', 'is_active' => 1],
    ['doc_ref' => 'EMP201_PACK', 'doc_group' => 'EMP201', 'name' => 'EMP201 Pack', 'is_active' => 1],
    ['doc_ref' => 'EMP201_POP', 'doc_group' => 'EMP201', 'name' => 'Proof of Payment', 'is_active' => 1],
];

foreach ($emp201Types as $type) {
    $exists = DB::table('cims_document_types')->where('doc_ref', $type['doc_ref'])->exists();
    if (!$exists) {
        DB::table('cims_document_types')->insert(array_merge($type, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));
        echo "Added: {$type['doc_ref']} - {$type['name']}\n";
    } else {
        echo "Exists: {$type['doc_ref']}\n";
    }
}

// 2. Add uploaded flag and stored filename columns to cims_emp201_declarations
$flagCols = [
    'file_emp201_return_uploaded' => "TINYINT(1) DEFAULT 0",
    'file_emp201_statement_uploaded' => "TINYINT(1) DEFAULT 0",
    'file_working_papers_uploaded' => "TINYINT(1) DEFAULT 0",
    'file_emp201_pack_uploaded' => "TINYINT(1) DEFAULT 0",
    'file_proof_of_payment_uploaded' => "TINYINT(1) DEFAULT 0",
];

foreach ($flagCols as $col => $type) {
    if (!Schema::hasColumn('cims_emp201_declarations', $col)) {
        DB::statement("ALTER TABLE cims_emp201_declarations ADD COLUMN $col $type");
        echo "Added column: $col\n";
    } else {
        echo "Column exists: $col\n";
    }
}

// 3. Get the IDs of the new doc types
echo "\n<h3>EMP201 Document Type IDs:</h3><pre>";
$types = DB::table('cims_document_types')->where('doc_group', 'EMP201')->get();
foreach ($types as $t) {
    echo "id={$t->id} | ref={$t->doc_ref} | name={$t->name}\n";
}
echo "</pre>";

// 4. Check the download route format
echo "<h3>Route URLs:</h3><pre>";
try {
    echo "view_client: " . route('cimsdocmanager.view.client', ['client_id' => 1, 'document' => 'cor_14_3_certificate']) . "\n";
} catch (\Exception $e) { echo "view_client: " . $e->getMessage() . "\n"; }
try {
    echo "download: " . route('cimsdocmanager.download', ['id' => 1]) . "\n";
} catch (\Exception $e) { echo "download: " . $e->getMessage() . "\n"; }
echo "</pre>";

echo "\nDone!\n";
