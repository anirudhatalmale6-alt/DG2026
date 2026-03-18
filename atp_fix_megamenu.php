<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

// Find ALL posts that contain Multipage or Onepage
echo "=== Searching everywhere for Multipage/Onepage ===\n";

// postmeta
$r1 = $wpdb->get_results("SELECT post_id, meta_key FROM {$wpdb->postmeta} WHERE meta_value LIKE '%Multipage%'");
foreach ($r1 as $r) echo "postmeta: " . get_the_title($r->post_id) . " (ID: {$r->post_id}, key: {$r->meta_key})\n";

// posts content
$r2 = $wpdb->get_results("SELECT ID, post_title, post_type FROM {$wpdb->posts} WHERE post_content LIKE '%Multipage%'");
foreach ($r2 as $r) echo "post_content: {$r->post_title} (ID: {$r->ID}, type: {$r->post_type})\n";

// options
$r3 = $wpdb->get_results("SELECT option_name FROM {$wpdb->options} WHERE option_value LIKE '%Multipage%'");
foreach ($r3 as $r) echo "option: {$r->option_name}\n";

// Check nav_menu_item meta
$r4 = $wpdb->get_results("SELECT post_id, meta_key, meta_value FROM {$wpdb->postmeta} WHERE meta_value LIKE '%Multipage%' AND meta_key LIKE '%pxl%'");
foreach ($r4 as $r) echo "pxl meta: post {$r->post_id} ({$r->meta_key}): " . substr($r->meta_value, 0, 200) . "\n";

// Check the Home nav menu item specifically for mega menu settings
echo "\n=== Home menu item (ID: 5004) meta ===\n";
$home_meta = $wpdb->get_results("SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = 5004 AND meta_value != ''");
foreach ($home_meta as $m) {
    echo "{$m->meta_key}: " . substr($m->meta_value, 0, 200) . "\n";
}

// Check if there's a mega menu widget area or Elementor template linked to the Home menu item
echo "\n=== Mega Menu templates (pxl-template) ===\n";
$templates = $wpdb->get_results("SELECT ID, post_title, post_name FROM {$wpdb->posts} WHERE post_type = 'pxl-template' AND post_status = 'publish' AND (post_title LIKE '%mega%' OR post_title LIKE '%menu%' OR post_title LIKE '%demo%')");
foreach ($templates as $t) echo "{$t->post_title} (ID: {$t->ID})\n";

// Check ALL pxl-template types
echo "\n=== All pxl-template posts ===\n";
$all_tpl = $wpdb->get_results("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type = 'pxl-template' AND post_status = 'publish'");
foreach ($all_tpl as $t) echo "{$t->post_title} (ID: {$t->ID})\n";
