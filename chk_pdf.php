<?php
require_once '/usr/www/users/smartucbmh/application/vendor/autoload.php';
$app = require_once '/usr/www/users/smartucbmh/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

// Check for PDF libraries
$checks = [
    'barryvdh/laravel-dompdf' => class_exists('Barryvdh\DomPDF\Facade\Pdf'),
    'dompdf/dompdf' => class_exists('Dompdf\Dompdf'),
    'mpdf/mpdf' => class_exists('Mpdf\Mpdf'),
    'tecnickcom/tcpdf' => class_exists('TCPDF'),
    'knplabs/knp-snappy' => class_exists('Knp\Snappy\Pdf'),
];

echo "=== PDF Libraries ===\n";
foreach ($checks as $pkg => $exists) {
    echo "$pkg: " . ($exists ? "AVAILABLE" : "not found") . "\n";
}

// Check composer.json for any pdf packages
$composer = json_decode(file_get_contents('/usr/www/users/smartucbmh/application/composer.json'), true);
echo "\n=== composer.json require (pdf-related) ===\n";
foreach ($composer['require'] ?? [] as $pkg => $ver) {
    if (stripos($pkg, 'pdf') !== false || stripos($pkg, 'dompdf') !== false || stripos($pkg, 'mpdf') !== false || stripos($pkg, 'tcpdf') !== false || stripos($pkg, 'snappy') !== false) {
        echo "$pkg: $ver\n";
    }
}
