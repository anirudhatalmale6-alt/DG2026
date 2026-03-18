<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

// Check what header/footer Home 2 uses
$page_id = 971;
$meta = $wpdb->get_results($wpdb->prepare(
    "SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND (meta_key LIKE '%header%' OR meta_key LIKE '%footer%' OR meta_key LIKE '%menu%' OR meta_key LIKE '%template%')",
    $page_id
));
echo "=== Home 2 (ID: 971) Meta ===\n";
foreach ($meta as $m) {
    echo "{$m->meta_key}: {$m->meta_value}\n";
}

// Get the menu structure
echo "\n=== Menus ===\n";
$menus = wp_get_nav_menus();
foreach ($menus as $menu) {
    echo "\nMenu: {$menu->name} (ID: {$menu->term_id})\n";
    $items = wp_get_nav_menu_items($menu->term_id);
    if ($items) {
        foreach ($items as $item) {
            $indent = $item->menu_item_parent ? '  -- ' : '- ';
            echo "{$indent}{$item->title} (ID: {$item->ID}, parent: {$item->menu_item_parent})\n";
        }
    }
}

// Check menu locations
echo "\n=== Menu Locations ===\n";
$locations = get_nav_menu_locations();
foreach ($locations as $loc => $menu_id) {
    $menu_obj = wp_get_nav_menu_object($menu_id);
    $name = $menu_obj ? $menu_obj->name : 'none';
    echo "$loc => $name (ID: $menu_id)\n";
}

// Check which header template Home 2 uses via theme options
echo "\n=== Theme Mods (header related) ===\n";
$mods = get_theme_mods();
foreach ($mods as $k => $v) {
    if (is_string($v) && (stripos($k, 'header') !== false || stripos($k, 'footer') !== false || stripos($k, 'menu') !== false)) {
        echo "$k: $v\n";
    }
}
