<?php
require_once dirname(__FILE__) . '/wp-load.php';

// Main Page Title template ID: 44
$template_id = 44;

// Original image: bg-page-title1.jpg (attachment ID 1844)
$orig_url = 'https://atpservices.co.za/wp-content/uploads/2024/04/bg-page-title1.jpg';
$orig_id = 1844;

$data = get_post_meta($template_id, '_elementor_data', true);
$elements = json_decode($data, true);

if ($elements && isset($elements[0]['settings']['background_image'])) {
    $old = $elements[0]['settings']['background_image']['url'];
    $elements[0]['settings']['background_image']['url'] = $orig_url;
    $elements[0]['settings']['background_image']['id'] = $orig_id;

    $new_data = wp_slash(json_encode($elements));
    update_post_meta($template_id, '_elementor_data', $new_data);
    delete_post_meta($template_id, '_elementor_css');

    echo "Reverted from: $old\n";
    echo "Back to: $orig_url\n";
}

if (function_exists('wp_cache_flush')) wp_cache_flush();
if (class_exists('LiteSpeed\Purge')) LiteSpeed\Purge::purge_all();
if (class_exists('\Elementor\Plugin')) \Elementor\Plugin::$instance->files_manager->clear_cache();
echo "Done!\n";
