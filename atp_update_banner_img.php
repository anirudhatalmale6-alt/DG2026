<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

// Main Page Title template ID: 44
$template_id = 44;

// New image: service-5.jpg (attachment ID 2005)
$new_img_url = 'https://atpservices.co.za/wp-content/uploads/2024/04/service-5.jpg';
$new_img_id = 2005;

// Old image: bg-page-title1.jpg (attachment ID 1844)
$old_img_url = 'bg-page-title1.jpg';

// Get current Elementor data
$data = get_post_meta($template_id, '_elementor_data', true);
if (!$data) {
    echo "ERROR: No Elementor data found for template $template_id\n";
    exit;
}

echo "Current data length: " . strlen($data) . "\n";

// Show current background image reference
if (preg_match('/bg-page-title1/', $data)) {
    echo "Found bg-page-title1 reference in data\n";
}

// Decode JSON
$elements = json_decode($data, true);
if (!$elements) {
    echo "ERROR: Could not decode JSON\n";
    exit;
}

// Function to recursively update background image
function update_bg_image(&$elements, $new_url, $new_id) {
    $changed = false;
    foreach ($elements as &$el) {
        // Check settings for background_image
        if (isset($el['settings']['background_image']['url'])) {
            $old = $el['settings']['background_image']['url'];
            $el['settings']['background_image']['url'] = $new_url;
            $el['settings']['background_image']['id'] = $new_id;
            echo "Updated section bg from: $old\n  to: $new_url\n";
            $changed = true;
        }
        // Also check background_overlay_image
        if (isset($el['settings']['background_overlay_image']['url'])) {
            $old = $el['settings']['background_overlay_image']['url'];
            $el['settings']['background_overlay_image']['url'] = $new_url;
            $el['settings']['background_overlay_image']['id'] = $new_id;
            echo "Updated overlay bg from: $old\n  to: $new_url\n";
            $changed = true;
        }
        // Recurse into elements
        if (isset($el['elements']) && is_array($el['elements'])) {
            if (update_bg_image($el['elements'], $new_url, $new_id)) {
                $changed = true;
            }
        }
    }
    return $changed;
}

$changed = update_bg_image($elements, $new_img_url, $new_img_id);

if ($changed) {
    $new_data = wp_slash(json_encode($elements));
    update_post_meta($template_id, '_elementor_data', $new_data);
    echo "\nTemplate updated successfully!\n";
} else {
    echo "No background image found to update.\n";
    // Let's dump the section settings to debug
    echo "\nSection settings:\n";
    echo json_encode($elements[0]['settings'], JSON_PRETTY_PRINT) . "\n";
}

// Clear caches
if (function_exists('wp_cache_flush')) wp_cache_flush();
if (class_exists('LiteSpeed\Purge')) LiteSpeed\Purge::purge_all();
if (class_exists('\Elementor\Plugin')) {
    \Elementor\Plugin::$instance->files_manager->clear_cache();
}

// Also clear Elementor CSS for this template
delete_post_meta($template_id, '_elementor_css');
echo "Elementor CSS cache cleared for template.\n";
echo "Done!\n";
