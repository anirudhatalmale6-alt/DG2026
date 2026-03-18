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
use Illuminate\Database\Schema\Blueprint;

echo "<pre>\n=== FIX DOCS TABLE + CHECK LOGS ===\n\n";

// 1. Add file_size column to cims_documents
echo "=== Adding file_size to cims_documents ===\n";
if (!Schema::hasColumn('cims_documents', 'file_size')) {
    Schema::table('cims_documents', function (Blueprint $t) {
        $t->bigInteger('file_size')->nullable()->after('file_path');
    });
    echo "  Added file_size column\n";
} else {
    echo "  file_size column already exists\n";
}

// 2. Also add bank_id and bank_name columns if missing (for bank document uploads)
if (!Schema::hasColumn('cims_documents', 'bank_id')) {
    Schema::table('cims_documents', function (Blueprint $t) {
        $t->integer('bank_id')->nullable();
        $t->string('bank_name', 255)->nullable();
    });
    echo "  Added bank_id and bank_name columns\n";
} else {
    echo "  bank_id column already exists\n";
}

// 3. Check the CGI log for recent upload errors
echo "\n=== Recent CGI Log Entries ===\n";
$logPath = storage_path('logs/laravel-cgi-fcgi.log');
if (file_exists($logPath)) {
    $content = file_get_contents($logPath);
    $tail = substr($content, -8000);
    echo htmlspecialchars($tail);
} else {
    echo "  Not found: $logPath\n";
}

// 4. Check the FPM log for recent upload-related errors
echo "\n\n=== Recent FPM Log (upload-related) ===\n";
$logPath = storage_path('logs/laravel-fpm-fcgi.log');
if (file_exists($logPath)) {
    $content = file_get_contents($logPath);
    $tail = substr($content, -15000);
    // Find lines with upload or income_tax or document
    $lines = explode("\n", $tail);
    foreach ($lines as $line) {
        if (preg_match('/upload|income_tax|document.*fail|ITAX|file_size/i', $line)) {
            echo htmlspecialchars(substr($line, 0, 500)) . "\n";
        }
    }
}

echo "\n=== DONE ===\n</pre>";
