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

echo "<pre>\n=== LARAVEL LOG SEARCH ===\n\n";

// Check all possible log locations
$logPaths = [
    storage_path('logs/laravel.log'),
    base_path('../storage/logs/laravel.log'),
    '/usr/www/users/smartucbmh/storage/logs/laravel.log',
    '/usr/www/users/smartucbmh/application/storage/logs/laravel.log',
];

foreach ($logPaths as $logPath) {
    echo "Checking: $logPath\n";
    if (file_exists($logPath)) {
        echo "  FOUND! Size: " . filesize($logPath) . " bytes\n";
        $log = file_get_contents($logPath);
        // Get last 10000 chars
        $tail = substr($log, -10000);
        echo "\n--- LAST 10000 CHARS ---\n";
        echo htmlspecialchars($tail);
        echo "\n--- END ---\n\n";
    } else {
        echo "  Not found\n";
    }
}

// Also check for daily logs
$dailyLogDir = storage_path('logs');
echo "\nLog directory: $dailyLogDir\n";
if (is_dir($dailyLogDir)) {
    $files = glob($dailyLogDir . '/*.log');
    echo "Log files found: " . count($files) . "\n";
    foreach ($files as $f) {
        echo "  " . basename($f) . " (" . filesize($f) . " bytes)\n";
    }
    // Read the most recent log
    if (!empty($files)) {
        usort($files, function($a, $b) { return filemtime($b) - filemtime($a); });
        $mostRecent = $files[0];
        echo "\nMost recent: " . basename($mostRecent) . "\n";
        $content = file_get_contents($mostRecent);
        $tail = substr($content, -10000);
        echo "\n--- LAST 10000 CHARS ---\n";
        echo htmlspecialchars($tail);
        echo "\n--- END ---\n";
    }
}

echo "\n=== DONE ===\n</pre>";
