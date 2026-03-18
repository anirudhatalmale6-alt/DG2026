<?php
/**
 * Import white logo and replace the current dark logo in Elementor templates.
 * Delete after use.
 */
require_once dirname(__FILE__) . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$upload_dir = wp_upload_dir();
$file_path = $upload_dir['basedir'] . '/2026/03/atp-logo-white.png';

if (!file_exists($file_path)) {
    die('White logo file not found at: ' . $file_path);
}

// Check if already imported
$existing = get_posts(array(
    'post_type' => 'attachment',
    'meta_query' => array(
        array('key' => '_wp_attached_file', 'value' => '2026/03/atp-logo-white.png')
    ),
    'posts_per_page' => 1
));

if (!empty($existing)) {
    $white_id = $existing[0]->ID;
    echo "White logo already in media library (ID: $white_id).\n";
} else {
    $filetype = wp_check_filetype(basename($file_path), null);
    $attachment = array(
        'guid'           => $upload_dir['baseurl'] . '/2026/03/atp-logo-white.png',
        'post_mime_type' => $filetype['type'],
        'post_title'     => 'ATP Logo White',
        'post_content'   => '',
        'post_status'    => 'inherit'
    );
    $white_id = wp_insert_attachment($attachment, $file_path);
    if (is_wp_error($white_id)) {
        die('Error: ' . $white_id->get_error_message());
    }
    $attach_data = wp_generate_attachment_metadata($white_id, $file_path);
    wp_update_attachment_metadata($white_id, $attach_data);
    echo "White logo imported (ID: $white_id).\n";
}

$white_url = wp_get_attachment_url($white_id);
echo "White logo URL: $white_url\n";

// Current dark logo that we set earlier
$dark_url = site_url() . '/wp-content/uploads/2026/03/atplogolong.png';
$dark_id = 5093;

// Replace dark logo with white logo in all Elementor templates
global $wpdb;
$results = $wpdb->get_results($wpdb->prepare(
    "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_data' AND meta_value LIKE %s",
    '%' . $wpdb->esc_like('atplogolong.png') . '%'
));

echo "Found " . count($results) . " templates with dark logo.\n";

foreach ($results as $row) {
    $post_title = get_the_title($row->post_id);
    $data = $row->meta_value;

    // Replace URL (normal and escaped)
    $data = str_replace($dark_url, $white_url, $data);
    $data = str_replace(str_replace('/', '\\/', $dark_url), str_replace('/', '\\/', $white_url), $data);

    // Replace attachment ID
    $data = str_replace('"id":' . $dark_id, '"id":' . $white_id, $data);
    $data = str_replace('"id":"' . $dark_id . '"', '"id":"' . $white_id . '"', $data);

    $wpdb->update(
        $wpdb->postmeta,
        array('meta_value' => $data),
        array('post_id' => $row->post_id, 'meta_key' => '_elementor_data')
    );
    echo "Updated: {$post_title} (ID: {$row->post_id})\n";
}

// Update theme mod and theme options
set_theme_mod('custom_logo', $white_id);
$theme_options = get_option('proactive_options', array());
if (is_array($theme_options)) {
    $theme_options['logo'] = array('url' => $white_url, 'id' => $white_id);
    $theme_options['logo_image'] = array('url' => $white_url, 'id' => $white_id);
    update_option('proactive_options', $theme_options);
}

// Clear Elementor cache
if (class_exists('\Elementor\Plugin')) {
    \Elementor\Plugin::$instance->files_manager->clear_cache();
    echo "Elementor cache cleared.\n";
}

echo "\nDone! All logos swapped to white version.\n";
