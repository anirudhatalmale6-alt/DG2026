<?php
/**
 * Find and replace the old demo logo with the new ATP logo in Elementor templates.
 * Delete after use.
 */
require_once dirname(__FILE__) . '/wp-load.php';

$new_logo_url = wp_get_attachment_url(5093);
$new_logo_id = 5093;

if (!$new_logo_url) {
    die('New logo attachment not found (ID 5093)');
}

echo "New logo URL: $new_logo_url\n";

// Find all Elementor templates/pages that contain the old logo
global $wpdb;
$old_logo = 'logo-light.png';

$results = $wpdb->get_results($wpdb->prepare(
    "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_data' AND meta_value LIKE %s",
    '%' . $wpdb->esc_like($old_logo) . '%'
));

echo "Found " . count($results) . " Elementor templates with old logo.\n";

foreach ($results as $row) {
    $post_title = get_the_title($row->post_id);
    echo "\nUpdating post ID {$row->post_id} ({$post_title})...\n";

    $data = $row->meta_value;

    // Get old logo URL pattern
    $old_pattern = 'wp-content/uploads/2024/04/logo-light.png';
    $new_pattern = str_replace(site_url() . '/', '', $new_logo_url);
    // Also handle the full URL
    $old_full_url = site_url() . '/wp-content/uploads/2024/04/logo-light.png';

    // Replace URLs in the JSON data
    $data = str_replace($old_full_url, $new_logo_url, $data);

    // Also replace escaped URLs (Elementor stores JSON with escaped slashes)
    $old_escaped = str_replace('/', '\\/', $old_full_url);
    $new_escaped = str_replace('/', '\\/', $new_logo_url);
    $data = str_replace($old_escaped, $new_escaped, $data);

    // Update the attachment ID references for the old logo
    // Find old logo attachment ID
    $old_logo_post = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE guid LIKE '%logo-light.png' AND post_type='attachment' LIMIT 1");
    if ($old_logo_post) {
        echo "Old logo attachment ID: $old_logo_post\n";
        // Replace "id":"OLD_ID" patterns near logo references (be careful with JSON)
        // Simple string replace of the old ID with new where it appears near logo references
        $data = str_replace('"id":' . $old_logo_post, '"id":' . $new_logo_id, $data);
        $data = str_replace('"id":"' . $old_logo_post . '"', '"id":"' . $new_logo_id . '"', $data);
    }

    $wpdb->update(
        $wpdb->postmeta,
        array('meta_value' => $data),
        array('post_id' => $row->post_id, 'meta_key' => '_elementor_data')
    );

    echo "Updated.\n";
}

// Also update the theme logo in footer/header logo
$old_url = site_url() . '/wp-content/uploads/2024/04/logo-light.png';
echo "\nAlso checking theme logo in proactive/theme footer logo...\n";
$results2 = $wpdb->get_results($wpdb->prepare(
    "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_data' AND meta_value LIKE %s",
    '%logo%'
));
echo "Found " . count($results2) . " templates with any logo reference.\n";

// Clear Elementor cache
if (class_exists('\Elementor\Plugin')) {
    \Elementor\Plugin::$instance->files_manager->clear_cache();
    echo "\nElementor cache cleared.\n";
}

echo "\nDone! Logo updated to: $new_logo_url\n";
