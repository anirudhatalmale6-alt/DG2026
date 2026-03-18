<?php
$file = '/usr/www/users/smartucbmh/application/Modules/CIMS_EMP201/Resources/views/emp201/statement.blade.php';
$content = file_get_contents($file);

// Check the sars-statement CSS
if (preg_match('/\.sars-statement\s*\{[^}]+\}/s', $content, $m)) {
    echo "=== .sars-statement CSS ===\n";
    echo $m[0] . "\n\n";
}

// Check the empsa-page CSS
if (preg_match('/\.empsa-page\s*\{[^}]+\}/s', $content, $m)) {
    echo "=== .empsa-page CSS ===\n";
    echo $m[0] . "\n\n";
}

// Check compiled blade views
$views = glob('/usr/www/users/smartucbmh/application/storage/framework/views/*.php');
echo "Compiled views count: " . count($views) . "\n";
