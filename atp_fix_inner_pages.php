<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

// Home 2 settings to copy
$settings = array(
    'header_layout' => '1477',
    'header_layout_sticky' => '1479',
    'header_mobile_layout' => '1481',
    'footer_layout' => '1475',
    'pt_mode' => 'none',
    'page_mobile_style' => 'light',
    'primary_color' => '#e65025',
    'gradient_color' => serialize(array('from' => '#faa61a', 'to' => '#e65025')),
);

// All published pages that need updating
$pages = $wpdb->get_results("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'publish' AND ID != 971");

echo "Updating " . count($pages) . " pages to use Home 2 header/footer...\n\n";

foreach ($pages as $page) {
    foreach ($settings as $key => $value) {
        update_post_meta($page->ID, $key, $value);
    }
    echo "Updated: {$page->post_title} (ID: {$page->ID})\n";
}

// Also update WooCommerce pages (shop, cart, checkout, my-account)
$woo_pages = array(
    get_option('woocommerce_shop_page_id'),
    get_option('woocommerce_cart_page_id'),
    get_option('woocommerce_checkout_page_id'),
    get_option('woocommerce_myaccount_page_id'),
);
foreach ($woo_pages as $wid) {
    if ($wid && !in_array($wid, array_column((array)$pages, 'ID'))) {
        foreach ($settings as $key => $value) {
            update_post_meta($wid, $key, $value);
        }
        echo "Updated WooCommerce page ID: $wid\n";
    }
}

// Also try to set the global default via theme options
$opts = get_option('proactive_options', array());
if (!is_array($opts)) $opts = array();
$opts['header_layout'] = '1477';
$opts['header_layout_sticky'] = '1479';
$opts['header_mobile_layout'] = '1481';
$opts['footer_layout'] = '1475';
$opts['ptitle_layout'] = '';
$opts['pt_mode'] = 'none';
update_option('proactive_options', $opts);
echo "\nGlobal theme options updated.\n";

// Clear caches
if (function_exists('wp_cache_flush')) wp_cache_flush();
if (class_exists('LiteSpeed\Purge')) LiteSpeed\Purge::purge_all();
if (class_exists('\Elementor\Plugin')) \Elementor\Plugin::$instance->files_manager->clear_cache();
echo "Caches cleared. Done!\n";
