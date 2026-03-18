<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

$changes = 0;

// Get all Elementor data
$all = $wpdb->get_results(
    "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_data' AND meta_value != ''"
);

echo "Scanning " . count($all) . " templates...\n\n";

// Phone/tel replacements - catch all variations
$replacements = array(
    // Visible text numbers
    '+12 345 550 87 15' => '(064) 507 2274',
    '+12345550 87 15' => '(064) 507 2274',
    '+1234555087 15' => '(064) 507 2274',
    '+12345550 8715' => '(064) 507 2274',
    '+123455508715' => '(064) 507 2274',
    '+12 123 456 7890' => '(031) 101 3876',
    '+121234567890' => '(031) 101 3876',
    '(210) 123-451' => '(064) 507 2274',
    '210123451' => '+27645072274',
    '+1 239 800 0987' => '(064) 507 2274',
    '+1239800987' => '+27645072274',
    '1239800987' => '+27645072274',
    '239800987' => '+27645072274',
    // tel: link values
    'tel:+121234567890' => 'tel:+27311013876',
    'tel:+1239800987' => 'tel:+27645072274',
    'tel:210123451' => 'tel:+27645072274',
    'tel:+273110138768715' => 'tel:+27311013876',
    'tel:+12345550' => 'tel:+27645072274',
    // Escaped versions for Elementor JSON
    'tel:+121234567890' => 'tel:+27311013876',
);

foreach ($all as $row) {
    $data = $row->meta_value;
    $original = $data;
    $title = get_the_title($row->post_id);

    foreach ($replacements as $old => $new) {
        $data = str_replace($old, $new, $data);
    }

    if ($data !== $original) {
        $wpdb->update(
            $wpdb->postmeta,
            array('meta_value' => $data),
            array('post_id' => $row->post_id, 'meta_key' => '_elementor_data')
        );
        echo "UPDATED: {$title} (ID: {$row->post_id})\n";
        $changes++;
    }
}

// Also fix post_content
echo "\nUpdating post_content...\n";
foreach ($replacements as $old => $new) {
    $affected = $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s) WHERE post_content LIKE %s",
        $old, $new, '%' . $wpdb->esc_like($old) . '%'
    ));
    if ($affected > 0) {
        echo "  Replaced '$old' -> '$new' in $affected posts\n";
    }
}

// Clear Elementor cache
if (class_exists('\Elementor\Plugin')) {
    \Elementor\Plugin::$instance->files_manager->clear_cache();
    echo "\nElementor cache cleared.\n";
}

echo "\nTotal templates updated: $changes\nDone!\n";
