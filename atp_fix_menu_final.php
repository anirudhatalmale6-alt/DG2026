<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

// 1. Remove mega menu from Home item (ID: 5004) in Main Menu
update_post_meta(5004, '_menu_item_pxl_megaprofile', '');
update_post_meta(5004, '_menu_item_url', home_url('/'));
echo "Main Menu: Removed mega menu from Home, set URL to homepage.\n";

// 2. Also check if the Main Header and Sticky Header post_content has Multipage
// These are templates 35 and 37 (used by other pages, not Home 2 specifically)
foreach (array(35, 37) as $tid) {
    $pc = get_post_field('post_content', $tid);
    if (strpos($pc, 'Multipage') !== false) {
        $pc = str_replace('Multipage', '', $pc);
        $pc = str_replace('Onepage', '', $pc);
        $wpdb->update($wpdb->posts, array('post_content' => $pc), array('ID' => $tid));
        echo "Cleaned Multipage/Onepage from " . get_the_title($tid) . " post_content.\n";
    }
}

// 3. Clear all caches
if (function_exists('wp_cache_flush')) wp_cache_flush();
if (class_exists('LiteSpeed\Purge')) LiteSpeed\Purge::purge_all();
if (class_exists('\Elementor\Plugin')) \Elementor\Plugin::$instance->files_manager->clear_cache();

echo "\nDone! Home is now a simple direct link.\n";
