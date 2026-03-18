<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

$changes = 0;
$all = $wpdb->get_results(
    "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_data' AND meta_value != ''"
);

echo "Scanning " . count($all) . " templates...\n\n";

$replacements = array(
    '(031) 101 3876' => '( 031 ) 101 3876',
    '(064) 507 2274' => '( 064 ) 507 2274',
);

foreach ($all as $row) {
    $data = $row->meta_value;
    $original = $data;
    foreach ($replacements as $old => $new) {
        $data = str_replace($old, $new, $data);
    }
    if ($data !== $original) {
        $wpdb->update($wpdb->postmeta, array('meta_value' => $data), array('post_id' => $row->post_id, 'meta_key' => '_elementor_data'));
        echo "UPDATED: " . get_the_title($row->post_id) . " (ID: {$row->post_id})\n";
        $changes++;
    }
}

// Also post_content
foreach ($replacements as $old => $new) {
    $a = $wpdb->query($wpdb->prepare("UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s) WHERE post_content LIKE %s", $old, $new, '%' . $wpdb->esc_like($old) . '%'));
    if ($a > 0) echo "post_content: '$old' -> '$new' in $a posts\n";
}

if (class_exists('\Elementor\Plugin')) { \Elementor\Plugin::$instance->files_manager->clear_cache(); echo "\nCache cleared.\n"; }
echo "Updated $changes templates.\nDone!\n";
