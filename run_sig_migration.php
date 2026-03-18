<?php
require __DIR__ . '/../application/vendor/autoload.php';
$app = require_once __DIR__ . '/../application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

try {
    // Check if columns already exist
    $cols = DB::select("SHOW COLUMNS FROM cims_email_signatures");
    $colNames = array_map(function($c){ return $c->Field; }, $cols);

    $added = [];

    if (!in_array('whatsapp', $colNames)) {
        DB::statement("ALTER TABLE cims_email_signatures ADD COLUMN whatsapp VARCHAR(50) NULL DEFAULT '' AFTER mobile");
        $added[] = 'whatsapp';
    }
    if (!in_array('direct_number', $colNames)) {
        DB::statement("ALTER TABLE cims_email_signatures ADD COLUMN direct_number VARCHAR(50) NULL DEFAULT '' AFTER " . (in_array('whatsapp', $colNames) || in_array('whatsapp', $added) ? 'whatsapp' : 'mobile'));
        $added[] = 'direct_number';
    }
    if (!in_array('slogan', $colNames)) {
        DB::statement("ALTER TABLE cims_email_signatures ADD COLUMN slogan VARCHAR(500) NULL DEFAULT '' AFTER company_website");
        $added[] = 'slogan';
    }
    if (!in_array('disclaimer_html', $colNames)) {
        DB::statement("ALTER TABLE cims_email_signatures ADD COLUMN disclaimer_html TEXT NULL AFTER slogan");
        $added[] = 'disclaimer_html';
    }

    if (empty($added)) {
        echo "All columns already exist. No changes needed.\n";
    } else {
        echo "Added columns: " . implode(', ', $added) . "\n";
    }

    // Verify
    $cols2 = DB::select("SHOW COLUMNS FROM cims_email_signatures");
    echo "Current columns: " . implode(', ', array_map(function($c){ return $c->Field; }, $cols2)) . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
