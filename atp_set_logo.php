<?php
/**
 * Temporary script to import logo into WP media library and set as site logo.
 * Delete after use.
 */
require_once dirname(__FILE__) . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$upload_dir = wp_upload_dir();
$file_path = $upload_dir['basedir'] . '/2026/03/atplogolong.png';

if (!file_exists($file_path)) {
    die('Logo file not found at: ' . $file_path);
}

// Check if already imported
$existing = get_posts(array(
    'post_type' => 'attachment',
    'meta_query' => array(
        array('key' => '_wp_attached_file', 'value' => '2026/03/atplogolong.png')
    ),
    'posts_per_page' => 1
));

if (!empty($existing)) {
    $attach_id = $existing[0]->ID;
    echo "Logo already in media library (ID: $attach_id). ";
} else {
    $filetype = wp_check_filetype(basename($file_path), null);
    $attachment = array(
        'guid'           => $upload_dir['baseurl'] . '/2026/03/atplogolong.png',
        'post_mime_type' => $filetype['type'],
        'post_title'     => 'ATP Logo',
        'post_content'   => '',
        'post_status'    => 'inherit'
    );

    $attach_id = wp_insert_attachment($attachment, $file_path);
    if (is_wp_error($attach_id)) {
        die('Error inserting attachment: ' . $attach_id->get_error_message());
    }

    $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
    wp_update_attachment_metadata($attach_id, $attach_data);
    echo "Logo imported to media library (ID: $attach_id). ";
}

// Set as site logo (used by Customizer / Site Identity)
set_theme_mod('custom_logo', $attach_id);

// Also try theme options if Proactive uses its own logo setting
$theme_options = get_option('proactive_options', array());
if (is_array($theme_options)) {
    $logo_url = wp_get_attachment_url($attach_id);
    // Common Proactive/CaseThemes option keys for logo
    $theme_options['logo'] = array('url' => $logo_url, 'id' => $attach_id);
    $theme_options['logo_image'] = array('url' => $logo_url, 'id' => $attach_id);
    update_option('proactive_options', $theme_options);
    echo "Theme options logo updated. ";
}

echo "Site logo set successfully! Attachment ID: $attach_id";
