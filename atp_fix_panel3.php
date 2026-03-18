<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

$data = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 1532 AND meta_key = '_elementor_data'");

// Find phone widget context
$pos = strpos($data, 'Call Us:');
if ($pos !== false) {
    echo "=== Phone widget context ===\n";
    echo substr($data, max(0, $pos - 50), 400) . "\n";
}

// Find hours widget context
$pos2 = strpos($data, 'Monday');
if ($pos2 !== false) {
    echo "\n=== Hours widget context ===\n";
    echo substr($data, max(0, $pos2 - 50), 400) . "\n";
}
