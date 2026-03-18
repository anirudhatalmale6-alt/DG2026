<?php
// Find ALL income-types blade files on the server
$basePath = '/usr/www/users/smartucbmh';
$results = [];

function searchDir($dir, $pattern, &$results) {
    if (!is_dir($dir)) return;
    $items = @scandir($dir);
    if (!$items) return;
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            // Skip vendor, node_modules, storage
            if (in_array($item, ['vendor','node_modules','.git'])) continue;
            searchDir($path, $pattern, $results);
        } elseif (preg_match($pattern, $item)) {
            $results[] = $path . ' [' . date('Y-m-d H:i:s', filemtime($path)) . '] [' . filesize($path) . ' bytes]';
        }
    }
}

// Search for any blade file with "income" in the name
searchDir($basePath . '/public_html/application/Modules', '/income.*blade/i', $results);

echo "Files found with 'income' in name:\n";
foreach ($results as $r) echo "  $r\n";

// Also check if there's a route cache
echo "\nRoute cache exists: " . (file_exists($basePath . '/public_html/application/bootstrap/cache/routes-v7.php') ? 'YES' : 'NO') . "\n";

// Check config cache
echo "Config cache exists: " . (file_exists($basePath . '/public_html/application/bootstrap/cache/config.php') ? 'YES' : 'NO') . "\n";

// Check the specific file we expect
$expected = $basePath . '/public_html/application/Modules/CIMS_PAYROLL/Resources/views/payroll/income-types/index.blade.php';
echo "\nExpected file exists: " . (file_exists($expected) ? 'YES' : 'NO') . "\n";
if (file_exists($expected)) {
    echo "Last modified: " . date('Y-m-d H:i:s', filemtime($expected)) . "\n";
    echo "Size: " . filesize($expected) . " bytes\n";
    // Check if it contains button_master_yes
    $content = file_get_contents($expected);
    echo "Contains 'button_master_yes': " . (strpos($content, 'button_master_yes') !== false ? 'YES' : 'NO') . "\n";
    echo "Contains 'tag_master': " . (strpos($content, 'tag_master') !== false ? 'YES' : 'NO') . "\n";
    echo "Contains 'income-tag': " . (strpos($content, 'income-tag') !== false ? 'YES' : 'NO') . "\n";
}
