<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

// The header content might be in the Home 2 page itself
$data = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 971 AND meta_key = '_elementor_data'");

$searches = array('Welcome to Proactive', 'info@gmail.com', '(213)', 'Start Consult', 'Investors', 'Career', 'Social Connect', '380 St Kilda', 'Digital Agency WordPress', 'Proactive is a');
foreach ($searches as $s) {
    $pos = strpos($data, $s);
    echo ($pos !== false ? "FOUND at pos $pos" : "NOT FOUND") . ": '$s' in Home 2 page\n";
}

// Check all Elementor templates for these strings
echo "\n=== Searching ALL templates ===\n";
$all = $wpdb->get_results("SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_data' AND meta_value LIKE '%Welcome to Proactive%'");
foreach ($all as $r) echo "Found 'Welcome to Proactive' in: " . get_the_title($r->post_id) . " (ID: {$r->post_id})\n";

$all2 = $wpdb->get_results("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_data' AND meta_value LIKE '%info@gmail.com%'");
foreach ($all2 as $r) echo "Found 'info@gmail.com' in: " . get_the_title($r->post_id) . " (ID: {$r->post_id})\n";

$all3 = $wpdb->get_results("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_data' AND meta_value LIKE '%Start Consult%'");
foreach ($all3 as $r) echo "Found 'Start Consult' in: " . get_the_title($r->post_id) . " (ID: {$r->post_id})\n";

// Check post_content too
$all4 = $wpdb->get_results("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_content LIKE '%Welcome to Proactive%'");
foreach ($all4 as $r) echo "Found 'Welcome to Proactive' in post_content: {$r->post_title} (ID: {$r->ID})\n";

$all5 = $wpdb->get_results("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_content LIKE '%info@gmail.com%'");
foreach ($all5 as $r) echo "Found 'info@gmail.com' in post_content: {$r->post_title} (ID: {$r->ID})\n";

// Check pxl_header_footer post type
echo "\n=== Header/Footer Templates ===\n";
$hf = $wpdb->get_results("SELECT ID, post_title, post_name, post_type FROM {$wpdb->posts} WHERE post_type IN ('pxl_header_footer', 'elementor_library', 'elementor-hf') AND post_status='publish'");
foreach ($hf as $h) echo "{$h->post_type}: {$h->post_title} (ID: {$h->ID}, slug: {$h->post_name})\n";

// Check which header Home 2 specifically uses
echo "\n=== Home 2 page-level meta (all) ===\n";
$all_meta = $wpdb->get_results("SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = 971 AND meta_value != '' AND meta_key NOT LIKE '\_%'");
foreach ($all_meta as $m) echo "{$m->meta_key}: " . substr($m->meta_value, 0, 100) . "\n";

$all_meta2 = $wpdb->get_results("SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = 971 AND meta_key LIKE '%pxl%'");
foreach ($all_meta2 as $m) echo "pxl: {$m->meta_key}: " . substr($m->meta_value, 0, 200) . "\n";
