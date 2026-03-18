<?php
// Check the env file directly
$envPaths = [
    __DIR__ . '/application/.env',
    '/usr/www/users/smartucbmh/public_html/application/.env',
];
foreach ($envPaths as $ep) {
    if (file_exists($ep)) {
        $content = file_get_contents($ep);
        preg_match('/^APP_URL=(.*)$/m', $content, $matches);
        echo "Found env at: $ep\n";
        echo "APP_URL=" . trim($matches[1] ?? 'NOT FOUND') . "\n";
        $url = trim($matches[1] ?? '');
        $path = parse_url($url, PHP_URL_PATH);
        echo "Parsed path: '" . ($path ?? '') . "'\n";
        echo "appBasePath: '" . rtrim($path ?? '', '/') . "'\n";
        break;
    }
}

// Check module route prefix
$moduleDirs = glob(__DIR__ . '/application/Modules/cims_pm_pro/Providers/*.php');
echo "\nModule providers:\n";
foreach ($moduleDirs as $f) {
    echo "- " . basename($f) . "\n";
}

$rsp = __DIR__ . '/application/Modules/cims_pm_pro/Providers/RouteServiceProvider.php';
if (file_exists($rsp)) {
    $content = file_get_contents($rsp);
    // Look for prefix
    preg_match('/prefix\s*\(\s*[\'"]([^\'"]+)/', $content, $m);
    echo "\nRoute prefix found: " . ($m[1] ?? 'not found') . "\n";
    // Show the relevant section
    preg_match('/mapWebRoutes.*?\}/s', $content, $m2);
    echo "\nmapWebRoutes:\n" . ($m2[0] ?? 'not found') . "\n";
}

// Also check the module.json
$mj = __DIR__ . '/application/Modules/cims_pm_pro/module.json';
if (file_exists($mj)) {
    echo "\nmodule.json:\n" . file_get_contents($mj) . "\n";
}
?>
