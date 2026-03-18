<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

// Find the Contact Us page
$pages = $wpdb->get_results("SELECT ID, post_title, post_name FROM {$wpdb->posts} WHERE post_type='page' AND post_status='publish' AND (post_title LIKE '%contact%' OR post_name LIKE '%contact%')");

foreach ($pages as $p) {
    echo "Page: {$p->post_title} (ID: {$p->ID}, slug: {$p->post_name})\n";
}

if (!empty($pages)) {
    $page_id = $pages[0]->ID;
    $data = $wpdb->get_var($wpdb->prepare(
        "SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = '_elementor_data'",
        $page_id
    ));
    if ($data) {
        echo "\n--- ELEMENTOR DATA (first 10000 chars) ---\n";
        echo substr($data, 0, 10000);
    }
}
