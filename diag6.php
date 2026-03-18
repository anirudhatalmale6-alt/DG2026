<?php
header('Content-Type: text/plain');
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "public_html exists: " . (is_dir('/usr/www/users/smartucbmh/public_html') ? 'YES' : 'NO') . "\n";
echo "realpath of public_html: " . realpath('/usr/www/users/smartucbmh/public_html') . "\n";

// Check if there's a symlink from public_html to application/public
echo "is_link public_html: " . (is_link('/usr/www/users/smartucbmh/public_html') ? 'YES' : 'NO') . "\n";
echo "is_link application/public: " . (is_link('/usr/www/users/smartucbmh/application/public') ? 'YES' : 'NO') . "\n";
