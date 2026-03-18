<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

// Check Main Header (ID 35) for the top bar content
$data = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 35 AND meta_key = '_elementor_data'");
if ($data) {
    // Find specific demo strings
    $searches = array('Welcome to Proactive', 'info@gmail.com', '213', 'Start Consult', 'Investors', 'Download', 'Career', 'Social Connect');
    foreach ($searches as $s) {
        $pos = strpos($data, $s);
        echo ($pos !== false ? "FOUND" : "NOT FOUND") . ": '$s' in Main Header\n";
    }
}

// Also check Footer Home 2 (ID 1475)
$data2 = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 1475 AND meta_key = '_elementor_data'");
if ($data2) {
    $searches2 = array('380 St Kilda', 'Melbourne', 'proactive', 'Digital Agency');
    foreach ($searches2 as $s) {
        $pos = strpos($data2, $s);
        echo ($pos !== false ? "FOUND" : "NOT FOUND") . ": '$s' in Footer Home 2\n";
    }
}

// Check Home 2 page Elementor data for demo content
$data3 = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 971 AND meta_key = '_elementor_data'");
if ($data3) {
    echo "\n=== Home 2 page demo content search ===\n";
    $searches3 = array('Startup Agency', 'Creative Agency', 'SEO', 'software engineers', 'Digital Marketing', 'Social Marketing', 'Strategic Planning', 'Improvet', 'Kelly', 'Tommy', 'Jerome', 'Proactive', 'great design', 'agency');
    foreach ($searches3 as $s) {
        $pos = strpos($data3, $s);
        echo ($pos !== false ? "FOUND" : "NOT FOUND") . ": '$s'\n";
    }
    echo "\nHome 2 Elementor data length: " . strlen($data3) . " chars\n";
}
