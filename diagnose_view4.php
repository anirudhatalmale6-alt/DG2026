<?php
header('Content-Type: text/plain');

try {
    require __DIR__ . '/application/vendor/autoload.php';
    $app = require_once __DIR__ . '/application/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->handle(Illuminate\Http\Request::capture());

    // 1. What table does ClientMaster model use?
    $cm = new \Modules\cims_pm_pro\Models\ClientMaster();
    echo "ClientMaster table: " . $cm->getTable() . "\n";
    echo "ClientMaster PK: " . $cm->getKeyName() . "\n\n";

    // 2. What table does Document model use?
    $dm = new \Modules\cims_pm_pro\Models\Document();
    echo "Document table: " . $dm->getTable() . "\n";
    echo "Document PK: " . $dm->getKeyName() . "\n\n";

    // 3. Get first client from client_master with non-null certificate
    echo "=== Clients with cor_14_3_certificate set ===\n";
    $clients = \DB::table('client_master')->whereNotNull('cor_14_3_certificate')->where('cor_14_3_certificate', '!=', '')->get(['client_id', 'client_code', 'company_name', 'cor_14_3_certificate', 'income_tax_registration']);
    if ($clients->isEmpty()) {
        echo "  NONE - no clients have cor_14_3_certificate set!\n";
    } else {
        foreach ($clients as $c) {
            echo "  client_id={$c->client_id}, code={$c->client_code}, cor_14_3={$c->cor_14_3_certificate}\n";
        }
    }

    // 4. Check ALL client_master certificate fields
    echo "\n=== ALL clients certificate fields ===\n";
    $allClients = \DB::table('client_master')->get(['client_id', 'client_code', 'cor_14_3_certificate', 'income_tax_registration', 'cor_certificate_uploaded', 'income_tax_notice_registration_uploaded']);
    foreach ($allClients as $c) {
        echo "  ID={$c->client_id} ({$c->client_code}): cor_cert='" . ($c->cor_14_3_certificate ?? 'NULL') . "', income_tax='" . ($c->income_tax_registration ?? 'NULL') . "', cor_uploaded=" . ($c->cor_certificate_uploaded ?? 'NULL') . ", tax_uploaded=" . ($c->income_tax_notice_registration_uploaded ?? 'NULL') . "\n";
    }

    // 5. ALL documents with paths
    echo "\n=== ALL documents ===\n";
    $docs = \DB::table('cims_documents')->get(['id', 'client_id', 'client_code', 'document_type', 'file_path', 'file_stored_name', 'file_original_name']);
    foreach ($docs as $d) {
        $fullPath = storage_path('app/public/' . $d->file_path);
        $exists = file_exists($fullPath) ? 'EXISTS' : 'MISSING';
        echo "  ID={$d->id}, client_id={$d->client_id}, code={$d->client_code}, type=" . ($d->document_type ?? 'NULL') . "\n";
        echo "    file_path: {$d->file_path}\n";
        echo "    file_stored_name: {$d->file_stored_name}\n";
        echo "    full_path: {$fullPath}\n";
        echo "    status: {$exists}\n";
    }

    // 6. Simulate what view_client does for a test case
    echo "\n=== Simulating view_client for client 13 ===\n";
    $client = \DB::table('client_master')->where('client_id', 13)->first();
    if (!$client) {
        echo "  Client 13 not found in client_master!\n";
        // Try with id
        $client = \DB::table('client_master')->where('id', 13)->first();
        if ($client) {
            echo "  Found with id=13: client_id={$client->client_id}\n";
        }
    } else {
        echo "  Client found: {$client->client_code}\n";
        echo "  cor_14_3_certificate: '" . ($client->cor_14_3_certificate ?? 'NULL') . "'\n";

        if ($client->cor_14_3_certificate) {
            echo "  Searching documents: client_id={$client->client_id}, file_stored_name={$client->cor_14_3_certificate}\n";
            $doc = \DB::table('cims_documents')
                ->where('client_id', $client->client_id)
                ->where('file_stored_name', $client->cor_14_3_certificate)
                ->first();
            if ($doc) {
                echo "  Document found! ID={$doc->id}\n";
                echo "  file_path: {$doc->file_path}\n";
                $path = storage_path('app/public/' . $doc->file_path);
                echo "  Full path: {$path}\n";
                echo "  File exists: " . (file_exists($path) ? 'YES' : 'NO') . "\n";
            } else {
                echo "  No matching document found!\n";
            }
        }
    }

    echo "\nstorage_path(): " . storage_path() . "\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getFile() . ":" . $e->getLine() . "\n";
}
