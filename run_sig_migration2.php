<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../application/vendor/autoload.php';
$app = require_once __DIR__ . '/../application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

try {
    $cols = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM cims_email_signatures");
    $colNames = array_map(function($c){ return $c->Field; }, $cols);
    echo "Existing columns: " . implode(', ', $colNames) . "\n";

    $added = [];

    if (!in_array('whatsapp', $colNames)) {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE cims_email_signatures ADD COLUMN whatsapp VARCHAR(50) NULL DEFAULT '' AFTER mobile");
        $added[] = 'whatsapp';
    }
    if (!in_array('direct_number', $colNames)) {
        $after = in_array('whatsapp', $colNames) || in_array('whatsapp', $added) ? 'whatsapp' : 'mobile';
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE cims_email_signatures ADD COLUMN direct_number VARCHAR(50) NULL DEFAULT '' AFTER {$after}");
        $added[] = 'direct_number';
    }
    if (!in_array('slogan', $colNames)) {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE cims_email_signatures ADD COLUMN slogan VARCHAR(500) NULL DEFAULT '' AFTER company_website");
        $added[] = 'slogan';
    }
    if (!in_array('disclaimer_html', $colNames)) {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE cims_email_signatures ADD COLUMN disclaimer_html TEXT NULL AFTER slogan");
        $added[] = 'disclaimer_html';
    }

    if (empty($added)) {
        echo "All columns already exist.\n";
    } else {
        echo "Added: " . implode(', ', $added) . "\n";
    }

    $cols2 = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM cims_email_signatures");
    echo "Final columns: " . implode(', ', array_map(function($c){ return $c->Field; }, $cols2)) . "\n";
    echo "DONE\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}

$kernel->terminate($request, $response);
