<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

$data = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 1532 AND meta_key = '_elementor_data'");

// Current phone widget (full JSON)
$old_phone = '{"id":"d79d1b8","elType":"widget","settings":{"title":"Call Us: ( 031 ) 101 3876","sub_title":"( 064 ) 507 2274","link":{"url":"tel:+27645072274","is_external":"","nofollow":"","custom_attributes":""},"pxl_icon":{"value":"far fa-phone-alt","library":"fa-regular"},"_margin":{"unit":"px","top":"0","right":"0","bottom":"0","left":"24","isLinked":false},"_padding":{"unit":"px","top":"0","right":"0","bottom":"26","left":"0","isLinked":false},"title_color":"#252525","subtitle_color":"#555555"},"elements":[],"widgetType":"pxl_contact_info"}';

// New: phone (landline only) + mobile (separate line)
$new_phone_and_mobile = '{"id":"d79d1b8","elType":"widget","settings":{"title":"Call Us: ( 031 ) 101 3876","sub_title":"","link":{"url":"tel:+27311013876","is_external":"","nofollow":"","custom_attributes":""},"pxl_icon":{"value":"far fa-phone-alt","library":"fa-regular"},"_margin":{"unit":"px","top":"0","right":"0","bottom":"0","left":"24","isLinked":false},"_padding":{"unit":"px","top":"0","right":"0","bottom":"26","left":"0","isLinked":false},"title_color":"#252525","subtitle_color":"#555555"},"elements":[],"widgetType":"pxl_contact_info"},{"id":"m0b1l3a","elType":"widget","settings":{"title":"Mobile: ( 064 ) 507 2274","sub_title":"","link":{"url":"tel:+27645072274","is_external":"","nofollow":"","custom_attributes":""},"pxl_icon":{"value":"fas fa-mobile-alt","library":"fa-solid"},"_margin":{"unit":"px","top":"0","right":"0","bottom":"0","left":"24","isLinked":false},"_padding":{"unit":"px","top":"0","right":"0","bottom":"26","left":"0","isLinked":false},"title_color":"#252525","subtitle_color":"#555555"},"elements":[],"widgetType":"pxl_contact_info"}';

// Current hours widget (full JSON)
$old_hours = '{"id":"348cb03","elType":"widget","settings":{"title":"Monday - Friday","sub_title":"8am - 5pm | Saturday: By Appointment","link":{"url":"#","is_external":"","nofollow":"","custom_attributes":""},"pxl_icon":{"value":"far fa-clock","library":"fa-regular"},"_margin":{"unit":"px","top":"0","right":"0","bottom":"0","left":"24","isLinked":false},"_padding":{"unit":"px","top":"0","right":"0","bottom":"44","left":"0","isLinked":false},"title_color":"#252525","subtitle_color":"#555555"},"elements":[],"widgetType":"pxl_contact_info"}';

// New: weekday hours + Saturday (separate line)
$new_hours_and_saturday = '{"id":"348cb03","elType":"widget","settings":{"title":"Monday - Friday","sub_title":"8am - 5pm","link":{"url":"#","is_external":"","nofollow":"","custom_attributes":""},"pxl_icon":{"value":"far fa-clock","library":"fa-regular"},"_margin":{"unit":"px","top":"0","right":"0","bottom":"0","left":"24","isLinked":false},"_padding":{"unit":"px","top":"0","right":"0","bottom":"26","left":"0","isLinked":false},"title_color":"#252525","subtitle_color":"#555555"},"elements":[],"widgetType":"pxl_contact_info"},{"id":"s4turd4y","elType":"widget","settings":{"title":"Saturday","sub_title":"Only by Appointment","link":{"url":"#","is_external":"","nofollow":"","custom_attributes":""},"pxl_icon":{"value":"far fa-clock","library":"fa-regular"},"_margin":{"unit":"px","top":"0","right":"0","bottom":"0","left":"24","isLinked":false},"_padding":{"unit":"px","top":"0","right":"0","bottom":"44","left":"0","isLinked":false},"title_color":"#252525","subtitle_color":"#555555"},"elements":[],"widgetType":"pxl_contact_info"}';

// Do replacements
$data = str_replace($old_phone, $new_phone_and_mobile, $data);
$data = str_replace($old_hours, $new_hours_and_saturday, $data);

$wpdb->update($wpdb->postmeta, array('meta_value' => $data), array('post_id' => 1532, 'meta_key' => '_elementor_data'));

// Verify
$data2 = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 1532 AND meta_key = '_elementor_data'");
preg_match_all('#"widgetType":"pxl_contact_info"#', $data2, $count);
echo "Contact info widgets count: " . count($count[0]) . " (should be 5)\n";

preg_match_all('#"title":"([^"]*)"[^}]*"widgetType":"pxl_contact_info"#', $data2, $titles);
echo "Widget titles:\n";
foreach ($titles[1] as $t) echo "  - $t\n";

// Clear caches
if (function_exists('wp_cache_flush')) wp_cache_flush();
if (class_exists('LiteSpeed\Purge')) LiteSpeed\Purge::purge_all();
if (class_exists('\Elementor\Plugin')) \Elementor\Plugin::$instance->files_manager->clear_cache();
echo "Done!\n";
