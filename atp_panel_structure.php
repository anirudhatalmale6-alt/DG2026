<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

$data = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 1532 AND meta_key = '_elementor_data'");

// Find all contact info widgets - show their full settings
preg_match_all('#\{"id":"([^"]+)","elType":"widget","settings":\{([^}]+(?:\{[^}]*\}[^}]*)*)\},"elements":\[\],"widgetType":"pxl_contact_info"\}#', $data, $matches, PREG_SET_ORDER);

echo "Found " . count($matches) . " pxl_contact_info widgets:\n\n";
foreach ($matches as $i => $m) {
    echo "--- Widget $i (id: {$m[1]}) ---\n";
    echo $m[0] . "\n\n";
}

// Also show the full section containing these widgets for context
$pos = strpos($data, '29 Coedmore');
if ($pos !== false) {
    // Go back to find the section start
    $start = max(0, $pos - 500);
    echo "\n=== Address section context ===\n";
    echo substr($data, $start, 3000) . "\n";
}
