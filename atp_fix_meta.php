<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

// The gradient_color was double-serialized. Fix it on all pages.
$pages = $wpdb->get_results("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'publish'");

$correct_gradient = array('from' => '#faa61a', 'to' => '#e65025');

echo "Fixing gradient_color on all pages...\n";
foreach ($pages as $page) {
    // Fix gradient_color - pass array directly (WP will serialize it)
    update_post_meta($page->ID, 'gradient_color', $correct_gradient);
}
echo "Fixed " . count($pages) . " pages.\n";

// Test: check if About Us loads
$response = wp_remote_get(home_url('/about-us/?nocache=' . time()), array('timeout' => 10));
if (is_wp_error($response)) {
    echo "About Us: ERROR - " . $response->get_error_message() . "\n";
} else {
    $code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    $has_error = strpos($body, 'critical error') !== false;
    echo "About Us: HTTP $code" . ($has_error ? " (STILL HAS ERROR)" : " (OK)") . "\n";
}

echo "Done!\n";
