<?php
header('Content-Type: application/json');

require __DIR__ . '/application/vendor/autoload.php';

$result = [];

// Check PhpSpreadsheet
$result['phpspreadsheet'] = class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet');

// Check Maatwebsite Excel
$result['maatwebsite'] = class_exists('\Maatwebsite\Excel\Excel');

// Check if PhpSpreadsheet is in composer
$composerLock = __DIR__ . '/application/composer.lock';
if (file_exists($composerLock)) {
    $lock = json_decode(file_get_contents($composerLock), true);
    $packages = array_merge($lock['packages'] ?? [], $lock['packages-dev'] ?? []);
    foreach ($packages as $pkg) {
        if (strpos($pkg['name'], 'spreadsheet') !== false || strpos($pkg['name'], 'excel') !== false || strpos($pkg['name'], 'phpoffice') !== false) {
            $result['found_packages'][] = $pkg['name'] . ' v' . $pkg['version'];
        }
    }
}

echo json_encode($result, JSON_PRETTY_PRINT);
