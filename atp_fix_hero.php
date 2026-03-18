<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

$data = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 971 AND meta_key = '_elementor_data'");

// Find the hero text context
echo "=== Searching for hero text ===\n";
$searches = array('Generation', 'Startup', 'Creative Agency', 'great design');
foreach ($searches as $s) {
    $pos = strpos($data, $s);
    if ($pos !== false) {
        echo "\nFOUND '$s' at pos $pos:\n";
        echo substr($data, max(0, $pos - 100), 250) . "\n";
    } else {
        echo "NOT FOUND: '$s'\n";
    }
}

// Also check for the Home dropdown content Multipage/Onepage
echo "\n=== Searching for Multipage/Onepage ===\n";
$pos = strpos($data, 'Multipage');
if ($pos !== false) {
    echo "FOUND in Home 2 page data at pos $pos\n";
    echo substr($data, max(0, $pos - 100), 300) . "\n";
} else {
    echo "NOT in Home 2 page\n";
}

// Check in the header
$hdata = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 1477 AND meta_key = '_elementor_data'");
$pos2 = strpos($hdata, 'Multipage');
if ($pos2 !== false) {
    echo "\nFOUND in Header Home 2 at pos $pos2\n";
} else {
    echo "NOT in Header Home 2\n";
}

// Check WordPress nav menu
$pos3 = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_title IN ('Multipage','Onepage') AND post_type = 'nav_menu_item'");
echo "Nav menu items with Multipage/Onepage: $pos3\n";
