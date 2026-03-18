<?php
header('Content-Type: text/plain');

$root = '/usr/www/users/smartucbmh/';

// Check logo locations
echo "LOGO FILES:\n";
$logoDirs = [
    $root . 'storage/logos/app/',
    $root . 'public_html/storage/logos/app/',
    $root . 'application/storage/app/public/logos/app/',
    $root . 'application/public/storage/logos/app/',
];
foreach ($logoDirs as $d) {
    echo "$d\n";
    if (is_dir($d)) {
        foreach (scandir($d) as $f) {
            if ($f === '.' || $f === '..') continue;
            echo "  $f (" . filesize($d.$f) . "b)\n";
        }
    } else {
        echo "  DIR MISSING\n";
    }
    echo "\n";
}

// Check avatar locations
echo "AVATAR FILES:\n";
$avatarDirs = [
    $root . 'storage/avatars/',
    $root . 'public_html/storage/avatars/',
    $root . 'application/storage/app/public/avatars/',
    $root . 'application/public/storage/avatars/',
];
foreach ($avatarDirs as $d) {
    echo "$d\n";
    if (is_dir($d)) {
        foreach (scandir($d) as $f) {
            if ($f === '.' || $f === '..') continue;
            $full = $d . $f;
            if (is_dir($full)) {
                echo "  $f/ =>\n";
                foreach (scandir($full) as $sf) {
                    if ($sf === '.' || $sf === '..') continue;
                    echo "    $sf (" . filesize($full.'/'.$sf) . "b)\n";
                }
            } else {
                echo "  $f (" . filesize($full) . "b)\n";
            }
        }
    } else {
        echo "  DIR MISSING\n";
    }
    echo "\n";
}
