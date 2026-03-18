<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

// Update Hidden Panel Sidebar Home 2 (ID: 1532)
$data = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 1532 AND meta_key = '_elementor_data'");

if (!$data) { die('No data found'); }

// 1. Replace phone number line - add both numbers, remove "(Sat - Thursday)"
$data = str_replace('Call Us: ( 031 ) 101 3876', 'Call Us: ( 031 ) 101 3876 / ( 064 ) 507 2274', $data);
$data = str_replace('(Sat - Thursday)', '', $data);
$data = str_replace('(Sat -Thursday)', '', $data);
$data = str_replace('Sat - Thursday', '', $data);
$data = str_replace('Sat -Thursday', '', $data);

// 2. Update business hours
$data = str_replace('10am - 05 pm', '8am - 5pm', $data);
$data = str_replace('10am - 05pm', '8am - 5pm', $data);
$data = str_replace('(10am - 05 pm)', '(8am - 5pm)', $data);

// Add Saturday hours - replace "Monday - Friday" section
$data = str_replace('Monday - Friday', 'Monday - Friday (8am - 5pm)<br/>Saturday: Open by Appointment', $data);

// Clean up any double time display
$data = str_replace('Monday - Friday (8am - 5pm)<br/>Saturday: Open by Appointment<\\/a><\\/span>","pxl_content":"(8am - 5pm)', 'Monday - Friday<br/>Saturday: Open by Appointment<\\/a><\\/span>","pxl_content":"(8am - 5pm)', $data);

$wpdb->update($wpdb->postmeta, array('meta_value' => $data), array('post_id' => 1532, 'meta_key' => '_elementor_data'));

// Also update post_content
$pc = get_post_field('post_content', 1532);
$pc = str_replace('Call Us: ( 031 ) 101 3876', 'Call Us: ( 031 ) 101 3876 / ( 064 ) 507 2274', $pc);
$pc = str_replace('(Sat - Thursday)', '', $pc);
$pc = str_replace('Sat - Thursday', '', $pc);
$pc = str_replace('10am - 05 pm', '8am - 5pm', $pc);
$pc = str_replace('10am - 05pm', '8am - 5pm', $pc);
$pc = str_replace('Monday - Friday', 'Monday - Friday (8am - 5pm)<br/>Saturday: Open by Appointment', $pc);
$wpdb->update($wpdb->posts, array('post_content' => $pc), array('ID' => 1532));

echo "Panel updated.\n";

// Also update the footer which has the same info (Footer Home 2, ID: 1475)
$fdata = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 1475 AND meta_key = '_elementor_data'");
if ($fdata) {
    $fdata = str_replace('(Sat - Thursday)', '', $fdata);
    $fdata = str_replace('Sat - Thursday', '', $fdata);
    $fdata = str_replace('10am - 05 pm', '8am - 5pm', $fdata);
    $fdata = str_replace('10am - 05pm', '8am - 5pm', $fdata);
    $fdata = str_replace('Monday - Friday', 'Monday - Friday (8am - 5pm)<br/>Saturday: Open by Appointment', $fdata);
    $wpdb->update($wpdb->postmeta, array('meta_value' => $fdata), array('post_id' => 1475, 'meta_key' => '_elementor_data'));
    echo "Footer also updated.\n";
}

// Clear caches
if (function_exists('wp_cache_flush')) wp_cache_flush();
if (class_exists('LiteSpeed\Purge')) LiteSpeed\Purge::purge_all();
if (class_exists('\Elementor\Plugin')) \Elementor\Plugin::$instance->files_manager->clear_cache();
echo "Caches cleared. Done!\n";
