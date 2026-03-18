<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

// Fix Hidden Panel Sidebar Home 2 (ID: 1532)
$data = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 1532 AND meta_key = '_elementor_data'");

// Phone: set sub_title to second number
$data = str_replace(
    '"title":"Call Us: ( 031 ) 101 3876","sub_title":""',
    '"title":"Call Us: ( 031 ) 101 3876","sub_title":"( 064 ) 507 2274"',
    $data
);

// Hours: update sub_title
$data = str_replace(
    '"title":"Monday - Friday","sub_title":"(8am - 5pm)"',
    '"title":"Monday - Friday","sub_title":"8am - 5pm | Saturday: By Appointment"',
    $data
);

$wpdb->update($wpdb->postmeta, array('meta_value' => $data), array('post_id' => 1532, 'meta_key' => '_elementor_data'));

// Verify
$data2 = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 1532 AND meta_key = '_elementor_data'");
$pos = strpos($data2, 'Call Us:');
echo "Phone: " . substr($data2, $pos, 100) . "\n";
$pos2 = strpos($data2, 'Monday');
echo "Hours: " . substr($data2, $pos2, 120) . "\n";

// Also fix Footer Home 2 (ID: 1475) same structure
$fdata = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 1475 AND meta_key = '_elementor_data'");
if ($fdata && strpos($fdata, 'Monday') !== false) {
    $fdata = str_replace(
        '"title":"Monday - Friday","sub_title":"(8am - 5pm)"',
        '"title":"Monday - Friday","sub_title":"8am - 5pm | Saturday: By Appointment"',
        $fdata
    );
    $wpdb->update($wpdb->postmeta, array('meta_value' => $fdata), array('post_id' => 1475, 'meta_key' => '_elementor_data'));
    echo "Footer also fixed.\n";
}

if (function_exists('wp_cache_flush')) wp_cache_flush();
if (class_exists('LiteSpeed\Purge')) LiteSpeed\Purge::purge_all();
if (class_exists('\Elementor\Plugin')) \Elementor\Plugin::$instance->files_manager->clear_cache();
echo "Done!\n";
