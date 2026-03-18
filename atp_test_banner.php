<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

// Step 1: Check if atpcover.jpg is already in media library
$existing = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_type='attachment' AND post_title='atpcover'");
if ($existing) {
    $attach_id = $existing;
    echo "Image already in media library: ID $attach_id\n";
} else {
    // Register in media library
    $upload_dir = wp_upload_dir();
    $source = ABSPATH . 'atpcover.jpg';
    $dest = $upload_dir['path'] . '/atpcover.jpg';

    if (!file_exists($source)) {
        echo "ERROR: atpcover.jpg not found in WordPress root\n";
        exit;
    }

    copy($source, $dest);
    $file_url = $upload_dir['url'] . '/atpcover.jpg';
    $file_path = $upload_dir['subdir'] . '/atpcover.jpg';

    $attach_id = wp_insert_attachment(array(
        'post_mime_type' => 'image/jpeg',
        'post_title' => 'atpcover',
        'post_content' => '',
        'post_status' => 'inherit'
    ), $dest);

    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $meta = wp_generate_attachment_metadata($attach_id, $dest);
    wp_update_attachment_metadata($attach_id, $meta);

    echo "Uploaded to media library: ID $attach_id\n";
    echo "URL: $file_url\n";
}

// Get the URL
$img_url = wp_get_attachment_url($attach_id);
echo "Image URL: $img_url\n";

// Step 2: Update ONLY the Main Page Title template (ID: 44) background image
$template_id = 44;
$data = get_post_meta($template_id, '_elementor_data', true);
$elements = json_decode($data, true);

if (!$elements) {
    echo "ERROR: Could not decode template data\n";
    exit;
}

// Update background image in first section
if (isset($elements[0]['settings']['background_image'])) {
    $old_url = $elements[0]['settings']['background_image']['url'];
    $elements[0]['settings']['background_image']['url'] = $img_url;
    $elements[0]['settings']['background_image']['id'] = $attach_id;
    echo "Updated banner image from: $old_url\n";
    echo "  to: $img_url\n";
}

$new_data = wp_slash(json_encode($elements));
update_post_meta($template_id, '_elementor_data', $new_data);
delete_post_meta($template_id, '_elementor_css');

// Clear caches
if (function_exists('wp_cache_flush')) wp_cache_flush();
if (class_exists('LiteSpeed\Purge')) LiteSpeed\Purge::purge_all();
if (class_exists('\Elementor\Plugin')) {
    \Elementor\Plugin::$instance->files_manager->clear_cache();
}

echo "\nBanner image updated. Test it on About Us page.\n";
echo "Done!\n";
