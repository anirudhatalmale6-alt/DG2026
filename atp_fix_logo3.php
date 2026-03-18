<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

$black_logo = 'https://atpservices.co.za/wp-content/uploads/2026/03/atplogolong.png';
$white_logo = 'https://atpservices.co.za/wp-content/uploads/2026/03/atp-logo-white.png';
$black_id = 5093;
$white_id = 5098;

// Old logo URLs to replace
$old_logos = array(
    'https://atpservices.co.za/wp-content/uploads/2024/05/h2-logo-dark.png' => $black_logo,
    'https://atpservices.co.za/wp-content/uploads/2024/05/h2-logo-light.png' => $white_logo,
    'https://atpservices.co.za/wp-content/uploads/2024/04/logo-light.png' => $white_logo,
);

$changes = 0;
$all = $wpdb->get_results("SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_data' AND (meta_value LIKE '%h2-logo%' OR meta_value LIKE '%logo-light%' OR meta_value LIKE '%logo-dark%')");

echo "Found " . count($all) . " templates with old logos.\n";

foreach ($all as $row) {
    $data = $row->meta_value;
    $original = $data;
    $title = get_the_title($row->post_id);

    foreach ($old_logos as $old => $new) {
        // Normal URL
        $data = str_replace($old, $new, $data);
        // Escaped URL (Elementor JSON)
        $data = str_replace(str_replace('/', '\\/', $old), str_replace('/', '\\/', $new), $data);
    }

    // Also fix attachment IDs for the old logos
    // h2-logo-dark ID is 1490
    $data = str_replace('"id":1490', '"id":' . $black_id, $data);
    $data = str_replace('"id":"1490"', '"id":"' . $black_id . '"', $data);

    if ($data !== $original) {
        $wpdb->update($wpdb->postmeta, array('meta_value' => $data), array('post_id' => $row->post_id, 'meta_key' => '_elementor_data'));
        // Also update post_content
        $pc = get_post_field('post_content', $row->post_id);
        foreach ($old_logos as $old => $new) { $pc = str_replace($old, $new, $pc); }
        $wpdb->update($wpdb->posts, array('post_content' => $pc), array('ID' => $row->post_id));
        echo "UPDATED: {$title} (ID: {$row->post_id})\n";
        $changes++;
    }
}

// Clear caches
if (function_exists('wp_cache_flush')) wp_cache_flush();
if (class_exists('LiteSpeed\Purge')) LiteSpeed\Purge::purge_all();
if (class_exists('\Elementor\Plugin')) \Elementor\Plugin::$instance->files_manager->clear_cache();

echo "\nUpdated $changes templates. Caches cleared.\n";
