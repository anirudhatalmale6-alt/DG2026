<?php
$files = [
    'DocumentCategory.php' => __DIR__ . '/Modules/cims_pm_pro/Models/DocumentCategory.php',
    'DocumentPeriod.php' => __DIR__ . '/Modules/cims_pm_pro/Models/DocumentPeriod.php',
];
foreach ($files as $name => $path) {
    echo "$name: " . (file_exists($path) ? "EXISTS (" . filesize($path) . " bytes)" : "MISSING") . "\n";
}
// Also try to autoload it
require_once __DIR__ . '/vendor/autoload.php';
echo "\nAutoload test:\n";
echo "DocumentCategory: " . (class_exists('Modules\cims_pm_pro\Models\DocumentCategory') ? 'LOADED OK' : 'FAILED') . "\n";
echo "DocumentPeriod: " . (class_exists('Modules\cims_pm_pro\Models\DocumentPeriod') ? 'LOADED OK' : 'FAILED') . "\n";
unlink(__FILE__);
