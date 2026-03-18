<?php
header('Content-Type: text/plain');

// Find the actual application root
echo "FINDING APP ROOT:\n";
$paths = [
    '/usr/www/users/smartucbmh/public_html/application/',
    '/usr/www/users/smartucbmh/public_html/',
];
foreach ($paths as $p) {
    echo "$p => " . (is_dir($p) ? "EXISTS" : "MISSING") . "\n";
    if (is_dir($p)) {
        $artisan = $p . 'artisan';
        echo "  artisan: " . (file_exists($artisan) ? "EXISTS" : "MISSING") . "\n";
        $storage = $p . 'storage/';
        echo "  storage/: " . (is_dir($storage) ? "EXISTS" : "MISSING") . "\n";
        if (is_dir($storage)) {
            foreach (scandir($storage) as $f) {
                if ($f === '.' || $f === '..') continue;
                echo "    storage/$f\n";
            }
        }
        $storageApp = $p . 'storage/app/';
        echo "  storage/app/: " . (is_dir($storageApp) ? "EXISTS" : "MISSING") . "\n";
        $storageAppPublic = $p . 'storage/app/public/';
        echo "  storage/app/public/: " . (is_dir($storageAppPublic) ? "EXISTS" : "MISSING") . "\n";
        if (is_dir($storageAppPublic)) {
            foreach (scandir($storageAppPublic) as $f) {
                if ($f === '.' || $f === '..') continue;
                echo "    storage/app/public/$f\n";
            }
        }
    }
}

// Search more broadly
echo "\nSEARCHING ALL STORAGE DIRS:\n";
$base = '/usr/www/users/smartucbmh/';
exec("find {$base} -type d -name 'client_docs' 2>/dev/null", $output);
foreach ($output as $line) {
    echo "  $line\n";
}

echo "\nSEARCHING FOR BANKING PDF:\n";
exec("find {$base} -name '*BANKING*' -type f 2>/dev/null", $output2);
foreach ($output2 as $line) {
    echo "  $line\n";
}

// Check laravel log for upload errors
echo "\nRECENT LARAVEL LOG (last 30 lines):\n";
$logFile = '/usr/www/users/smartucbmh/public_html/application/storage/logs/laravel.log';
$logFile2 = '/usr/www/users/smartucbmh/storage/logs/laravel.log';
foreach ([$logFile, $logFile2] as $lf) {
    if (file_exists($lf)) {
        echo "LOG: $lf\n";
        $lines = file($lf);
        $last = array_slice($lines, -30);
        echo implode('', $last);
        break;
    } else {
        echo "LOG NOT FOUND: $lf\n";
    }
}
