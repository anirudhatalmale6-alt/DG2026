<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

// Homepage ID - keep pt_mode=none
$homepage_id = 971;

// Get all published pages except homepage
$pages = $wpdb->get_results($wpdb->prepare(
    "SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'publish' AND ID != %d",
    $homepage_id
));

echo "Enabling Page Title Banner on inner pages...\n\n";

foreach ($pages as $page) {
    // Set pt_mode to 'bd' (Builder) and ptitle_layout to 44 (Main Page Title template)
    update_post_meta($page->ID, 'pt_mode', 'bd');
    update_post_meta($page->ID, 'ptitle_layout', '44');
    echo "Enabled banner: {$page->post_title} (ID: {$page->ID})\n";
}

echo "\nTotal: " . count($pages) . " pages updated.\n";

// Also update theme-level defaults so any new pages get the banner
$opts = get_option('proactive_options', array());
if (!is_array($opts)) $opts = array();
$opts['pt_mode'] = 'bd';
$opts['ptitle_layout'] = '44';
update_option('proactive_options', $opts);
echo "Theme default set to Builder mode with Main Page Title template.\n";

// Clear caches
if (function_exists('wp_cache_flush')) wp_cache_flush();
if (class_exists('LiteSpeed\Purge')) LiteSpeed\Purge::purge_all();
if (class_exists('\Elementor\Plugin')) \Elementor\Plugin::$instance->files_manager->clear_cache();
echo "Caches cleared. Done!\n";
