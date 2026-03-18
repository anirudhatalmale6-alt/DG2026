<?php
header('Content-Type: text/plain');

try {
    require __DIR__ . '/application/vendor/autoload.php';
    $app = require_once __DIR__ . '/application/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->handle(Illuminate\Http\Request::capture());

    // 1. Get cims_documents column names
    echo "=== cims_documents columns ===\n";
    $cols = \DB::getSchemaBuilder()->getColumnListing('cims_documents');
    echo implode(', ', $cols) . "\n\n";

    // 2. ALL documents - just dump all columns
    echo "=== ALL documents (raw) ===\n";
    $docs = \DB::table('cims_documents')->get();
    foreach ($docs as $d) {
        echo "---\n";
        foreach (get_object_vars($d) as $k => $v) {
            echo "  {$k}: " . ($v ?? 'NULL') . "\n";
        }
        // Check file
        $fp = $d->file_path ?? '';
        if ($fp) {
            $full = storage_path('app/public/' . $fp);
            echo "  FULL_PATH: {$full}\n";
            echo "  FILE_EXISTS: " . (file_exists($full) ? 'YES' : 'NO') . "\n";
        }
    }

    // 3. Client 13 detail
    echo "\n=== Client 13 (ATP100) detail ===\n";
    $c = \DB::table('client_master')->where('client_id', 13)->first();
    if ($c) {
        echo "  cor_14_3_certificate: '" . ($c->cor_14_3_certificate ?? 'NULL') . "'\n";
        echo "  income_tax_registration: '" . ($c->income_tax_registration ?? 'NULL') . "'\n";

        // Now search document
        if ($c->cor_14_3_certificate) {
            echo "\n  Searching: client_id=13, file_stored_name='{$c->cor_14_3_certificate}'\n";
            $doc = \DB::table('cims_documents')
                ->where('client_id', 13)
                ->where('file_stored_name', $c->cor_14_3_certificate)
                ->first();
            if ($doc) {
                echo "  FOUND doc id={$doc->id}, file_path={$doc->file_path}\n";
                $path = storage_path('app/public/' . $doc->file_path);
                echo "  Full: {$path}\n";
                echo "  Exists: " . (file_exists($path) ? 'YES' : 'NO') . "\n";
            } else {
                echo "  NOT FOUND in cims_documents!\n";
                // Try looser search
                echo "  Loose search by client_id=13:\n";
                $allDocs = \DB::table('cims_documents')->where('client_id', 13)->get();
                foreach ($allDocs as $ad) {
                    echo "    id={$ad->id}, stored_name={$ad->file_stored_name}\n";
                }
            }
        }
    }

    echo "\nstorage_path: " . storage_path() . "\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getFile() . ":" . $e->getLine() . "\n";
}
