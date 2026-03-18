<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

// Search ALL postmeta for the hero text
echo "=== Searching all Elementor data for hero text ===\n";
$results = $wpdb->get_results("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_data' AND meta_value LIKE '%Startup Agency%'");
foreach ($results as $r) echo "Found 'Startup Agency' in: " . get_the_title($r->post_id) . " (ID: {$r->post_id})\n";

$results2 = $wpdb->get_results("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_data' AND meta_value LIKE '%Creative Agency%'");
foreach ($results2 as $r) echo "Found 'Creative Agency' in: " . get_the_title($r->post_id) . " (ID: {$r->post_id})\n";

// Check post_content too
$results3 = $wpdb->get_results("SELECT ID, post_title, post_type FROM {$wpdb->posts} WHERE post_content LIKE '%Startup Agency%'");
foreach ($results3 as $r) echo "Found 'Startup Agency' in post_content: {$r->post_title} (ID: {$r->ID}, type: {$r->post_type})\n";

$results4 = $wpdb->get_results("SELECT ID, post_title, post_type FROM {$wpdb->posts} WHERE post_content LIKE '%Creative Agency%'");
foreach ($results4 as $r) echo "Found 'Creative Agency' in post_content: {$r->post_title} (ID: {$r->ID}, type: {$r->post_type})\n";

// Check Elementor library template for Home 2 (ID: 4959)
$data = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 4959 AND meta_key = '_elementor_data'");
if ($data) {
    echo "\n=== Elementor Library Home 2 (ID: 4959) ===\n";
    echo "Data length: " . strlen($data) . "\n";
    $pos = strpos($data, 'Startup');
    echo "Contains 'Startup': " . ($pos !== false ? "YES at $pos" : "NO") . "\n";
    $pos2 = strpos($data, 'Generation');
    echo "Contains 'Generation': " . ($pos2 !== false ? "YES at $pos2" : "NO") . "\n";
}

// Check RevSlider
echo "\n=== Checking RevSlider ===\n";
$slider_tables = $wpdb->get_results("SHOW TABLES LIKE '%revslider%'");
foreach ($slider_tables as $t) {
    $tname = array_values(get_object_vars($t))[0];
    echo "Table: $tname\n";
}

if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}revslider_sliders'")) {
    $sliders = $wpdb->get_results("SELECT id, title, alias FROM {$wpdb->prefix}revslider_sliders");
    foreach ($sliders as $s) echo "Slider: {$s->title} (alias: {$s->alias})\n";

    $slides = $wpdb->get_results("SELECT id, slider_id FROM {$wpdb->prefix}revslider_slides WHERE layers LIKE '%Startup%' OR layers LIKE '%Creative Agency%' OR layers LIKE '%Generation%'");
    foreach ($slides as $s) echo "Found hero text in slide ID: {$s->id} (slider: {$s->slider_id})\n";
}

// Check for Multipage/Onepage - could be in mega menu metadata
echo "\n=== Checking for Multipage menu items ===\n";
$mm = $wpdb->get_results("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type = 'nav_menu_item' AND (post_title LIKE '%Multipage%' OR post_title LIKE '%Onepage%')");
foreach ($mm as $m) echo "Menu item: {$m->post_title} (ID: {$m->ID})\n";

// Maybe it's in elementor mega menu widget
$mm2 = $wpdb->get_results("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_data' AND meta_value LIKE '%Multipage%'");
foreach ($mm2 as $m) echo "Found 'Multipage' in Elementor data: " . get_the_title($m->post_id) . " (ID: {$m->post_id})\n";

// Check widget options
$mm3 = $wpdb->get_results("SELECT option_name FROM {$wpdb->options} WHERE option_value LIKE '%Multipage%' AND option_name LIKE '%widget%'");
foreach ($mm3 as $m) echo "Found in option: {$m->option_name}\n";
