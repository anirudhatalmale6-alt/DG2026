<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

echo "=== Current Main Menu (ID: 45) ===\n";
$items = wp_get_nav_menu_items(45);
foreach ($items as $item) {
    $indent = $item->menu_item_parent ? '  -- ' : '- ';
    echo "{$indent}{$item->title} (ID: {$item->ID}, parent: {$item->menu_item_parent}, url: {$item->url})\n";
}

// Strategy: Delete all existing menu items, then create fresh clean ones
echo "\n=== Rebuilding menu ===\n";

// First, get the URLs we need
$home_url = home_url('/');
$about_page = $wpdb->get_row("SELECT ID FROM {$wpdb->posts} WHERE post_name = 'about-us' AND post_type = 'page' AND post_status = 'publish'");
$services_page = $wpdb->get_row("SELECT ID FROM {$wpdb->posts} WHERE post_name = 'our-services' AND post_type = 'page' AND post_status = 'publish'");
$faq_page = $wpdb->get_row("SELECT ID FROM {$wpdb->posts} WHERE post_name = 'faqs' AND post_type = 'page' AND post_status = 'publish'");
$blog_page = $wpdb->get_row("SELECT ID FROM {$wpdb->posts} WHERE post_name = 'blog-grid-without-sidebar' AND post_type = 'page' AND post_status = 'publish'");
$shop_page = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'woocommerce_shop_page_id'");
$contact_page = $wpdb->get_row("SELECT ID FROM {$wpdb->posts} WHERE post_name = 'contact-us' AND post_type = 'page' AND post_status = 'publish'");

echo "About: " . ($about_page ? $about_page->ID : 'NOT FOUND') . "\n";
echo "Services: " . ($services_page ? $services_page->ID : 'NOT FOUND') . "\n";
echo "FAQs: " . ($faq_page ? $faq_page->ID : 'NOT FOUND') . "\n";
echo "Blog: " . ($blog_page ? $blog_page->ID : 'NOT FOUND') . "\n";
echo "Shop page ID: $shop_page\n";
echo "Contact: " . ($contact_page ? $contact_page->ID : 'NOT FOUND') . "\n";

// Delete ALL existing menu items from Main Menu (45)
foreach ($items as $item) {
    wp_delete_post($item->ID, true);
}
echo "\nDeleted all old menu items.\n";

// Create new clean menu items
$menu_id = 45;
$order = 1;

// 1. Home
$home_item = wp_update_nav_menu_item($menu_id, 0, array(
    'menu-item-title' => 'Home',
    'menu-item-url' => $home_url,
    'menu-item-status' => 'publish',
    'menu-item-type' => 'custom',
    'menu-item-position' => $order++,
));
echo "Created: Home (ID: $home_item)\n";

// 2. About Us
$about_item = wp_update_nav_menu_item($menu_id, 0, array(
    'menu-item-title' => 'About Us',
    'menu-item-object-id' => $about_page->ID,
    'menu-item-object' => 'page',
    'menu-item-type' => 'post_type',
    'menu-item-status' => 'publish',
    'menu-item-position' => $order++,
));
echo "Created: About Us (ID: $about_item)\n";

// 3. Services
$services_item = wp_update_nav_menu_item($menu_id, 0, array(
    'menu-item-title' => 'Services',
    'menu-item-object-id' => $services_page->ID,
    'menu-item-object' => 'page',
    'menu-item-type' => 'post_type',
    'menu-item-status' => 'publish',
    'menu-item-position' => $order++,
));
echo "Created: Services (ID: $services_item)\n";

// 4. FAQs
$faq_item = wp_update_nav_menu_item($menu_id, 0, array(
    'menu-item-title' => 'FAQs',
    'menu-item-object-id' => $faq_page->ID,
    'menu-item-object' => 'page',
    'menu-item-type' => 'post_type',
    'menu-item-status' => 'publish',
    'menu-item-position' => $order++,
));
echo "Created: FAQs (ID: $faq_item)\n";

// 5. Blog
if ($blog_page) {
    $blog_item = wp_update_nav_menu_item($menu_id, 0, array(
        'menu-item-title' => 'Blog',
        'menu-item-object-id' => $blog_page->ID,
        'menu-item-object' => 'page',
        'menu-item-type' => 'post_type',
        'menu-item-status' => 'publish',
        'menu-item-position' => $order++,
    ));
} else {
    $blog_item = wp_update_nav_menu_item($menu_id, 0, array(
        'menu-item-title' => 'Blog',
        'menu-item-url' => home_url('/blog/'),
        'menu-item-type' => 'custom',
        'menu-item-status' => 'publish',
        'menu-item-position' => $order++,
    ));
}
echo "Created: Blog (ID: $blog_item)\n";

// 6. Shop
$shop_item = wp_update_nav_menu_item($menu_id, 0, array(
    'menu-item-title' => 'Shop',
    'menu-item-object-id' => $shop_page,
    'menu-item-object' => 'page',
    'menu-item-type' => 'post_type',
    'menu-item-status' => 'publish',
    'menu-item-position' => $order++,
));
echo "Created: Shop (ID: $shop_item)\n";

// 7. Contact Us
$contact_item = wp_update_nav_menu_item($menu_id, 0, array(
    'menu-item-title' => 'Contact Us',
    'menu-item-object-id' => $contact_page->ID,
    'menu-item-object' => 'page',
    'menu-item-type' => 'post_type',
    'menu-item-status' => 'publish',
    'menu-item-position' => $order++,
));
echo "Created: Contact Us (ID: $contact_item)\n";

// Also rebuild Mobile Menu (ID: 50) with the same structure
echo "\n=== Rebuilding Mobile Menu (ID: 50) ===\n";
$mobile_items = wp_get_nav_menu_items(50);
foreach ($mobile_items as $item) {
    wp_delete_post($item->ID, true);
}
echo "Deleted old mobile menu items.\n";

$mobile_menu_id = 50;
$order = 1;

$mobile_entries = array(
    array('title' => 'Home', 'url' => $home_url, 'type' => 'custom'),
    array('title' => 'About Us', 'id' => $about_page->ID),
    array('title' => 'Services', 'id' => $services_page->ID),
    array('title' => 'FAQs', 'id' => $faq_page->ID),
    array('title' => 'Blog', 'id' => $blog_page ? $blog_page->ID : 0, 'url' => home_url('/blog/')),
    array('title' => 'Shop', 'id' => $shop_page),
    array('title' => 'Contact Us', 'id' => $contact_page->ID),
);

foreach ($mobile_entries as $entry) {
    if (isset($entry['type']) && $entry['type'] === 'custom') {
        $mid = wp_update_nav_menu_item($mobile_menu_id, 0, array(
            'menu-item-title' => $entry['title'],
            'menu-item-url' => $entry['url'],
            'menu-item-type' => 'custom',
            'menu-item-status' => 'publish',
            'menu-item-position' => $order++,
        ));
    } else {
        $oid = isset($entry['id']) ? $entry['id'] : 0;
        if ($oid) {
            $mid = wp_update_nav_menu_item($mobile_menu_id, 0, array(
                'menu-item-title' => $entry['title'],
                'menu-item-object-id' => $oid,
                'menu-item-object' => 'page',
                'menu-item-type' => 'post_type',
                'menu-item-status' => 'publish',
                'menu-item-position' => $order++,
            ));
        } else {
            $mid = wp_update_nav_menu_item($mobile_menu_id, 0, array(
                'menu-item-title' => $entry['title'],
                'menu-item-url' => $entry['url'],
                'menu-item-type' => 'custom',
                'menu-item-status' => 'publish',
                'menu-item-position' => $order++,
            ));
        }
    }
    echo "Created mobile: {$entry['title']} (ID: $mid)\n";
}

// Clear caches
if (function_exists('wp_cache_flush')) wp_cache_flush();
if (class_exists('LiteSpeed\Purge')) LiteSpeed\Purge::purge_all();
if (class_exists('\Elementor\Plugin')) \Elementor\Plugin::$instance->files_manager->clear_cache();

echo "\nDone! Menu rebuilt.\n";
