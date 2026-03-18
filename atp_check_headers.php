<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

// Check theme options for default header/footer
echo "=== Theme Options (header/footer related) ===\n";
$opts = get_option('proactive_options', array());
if (is_array($opts)) {
    foreach ($opts as $k => $v) {
        if (stripos($k, 'header') !== false || stripos($k, 'footer') !== false || stripos($k, 'title') !== false || stripos($k, 'page_title') !== false) {
            if (is_array($v)) {
                echo "$k: " . json_encode($v) . "\n";
            } else {
                echo "$k: $v\n";
            }
        }
    }
}

// Check what header/footer inner pages use
echo "\n=== Inner Page Meta ===\n";
$pages = array(
    1201 => 'About Us',
    1199 => 'Services',
    1194 => 'FAQs',
    762 => 'Contact Us',
);
foreach ($pages as $pid => $name) {
    echo "\n--- $name (ID: $pid) ---\n";
    $meta = $wpdb->get_results($wpdb->prepare(
        "SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND (meta_key LIKE 'header%' OR meta_key LIKE 'footer%' OR meta_key LIKE 'page_%' OR meta_key LIKE 'pt_%' OR meta_key LIKE 'primary_%' OR meta_key LIKE 'gradient_%')",
        $pid
    ));
    foreach ($meta as $m) {
        echo "{$m->meta_key}: " . substr($m->meta_value, 0, 200) . "\n";
    }
}

// Check Home 2 page meta for comparison
echo "\n--- Home 2 (ID: 971) ---\n";
$h2meta = $wpdb->get_results("SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = 971 AND (meta_key LIKE 'header%' OR meta_key LIKE 'footer%' OR meta_key LIKE 'page_%' OR meta_key LIKE 'pt_%' OR meta_key LIKE 'primary_%' OR meta_key LIKE 'gradient_%')");
foreach ($h2meta as $m) {
    echo "{$m->meta_key}: " . substr($m->meta_value, 0, 200) . "\n";
}
