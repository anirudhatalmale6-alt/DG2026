<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

// Get the panel data
$data = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 1532 AND meta_key = '_elementor_data'");

// Show the current phone and hours widget context
preg_match_all('#"title"\s*:\s*"([^"]*(?:Call|Monday|031|064|8am)[^"]*)"#i', $data, $titles);
echo "Current titles:\n";
foreach ($titles[1] as $t) echo "  $t\n";

preg_match_all('#"pxl_content"\s*:\s*"([^"]*(?:5pm|Thursday|Appointment|064|031)[^"]*)"#i', $data, $contents);
echo "\nCurrent pxl_content:\n";
foreach ($contents[1] as $c) echo "  $c\n";

// Fix phone: title = "Call Us: ( 031 ) 101 3876", pxl_content = "( 064 ) 507 2274"
$data = str_replace(
    'Call Us: ( 031 ) 101 3876 / ( 064 ) 507 2274',
    'Call Us: ( 031 ) 101 3876',
    $data
);

// The pxl_content for the phone widget - currently empty after removing "(Sat - Thursday)"
// Need to find the phone widget and set its pxl_content
// Look for the pattern near the phone title
$data = preg_replace(
    '#("title"\s*:\s*"Call Us: \( 031 \) 101 3876"[^}]*"pxl_content"\s*:\s*")([^"]*)(")#',
    '${1}( 064 ) 507 2274${3}',
    $data
);

// Fix hours: title = "Monday - Friday", pxl_content should have the schedule
$data = str_replace(
    'Monday - Friday (8am - 5pm)<br/>Saturday: Open by Appointment',
    'Monday - Friday',
    $data
);
$data = str_replace(
    'Monday - Friday (8am - 5pm)<br\\/>' . 'Saturday: Open by Appointment',
    'Monday - Friday',
    $data
);

// Update the pxl_content for hours widget
$data = preg_replace(
    '#("title"\s*:\s*"Monday - Friday"[^}]*"pxl_content"\s*:\s*")([^"]*)(")#',
    '${1}8am - 5pm | Saturday: By Appointment${3}',
    $data
);

// Double check - remove any remaining raw <br/> in titles
$data = str_replace('<br\\/>', ' | ', $data);
$data = str_replace('<br/>', ' | ', $data);

$wpdb->update($wpdb->postmeta, array('meta_value' => $data), array('post_id' => 1532, 'meta_key' => '_elementor_data'));

// Also fix post_content
$pc = get_post_field('post_content', 1532);
$pc = str_replace('Call Us: ( 031 ) 101 3876 / ( 064 ) 507 2274', 'Call Us: ( 031 ) 101 3876', $pc);
$pc = str_replace('Monday - Friday (8am - 5pm)<br/>Saturday: Open by Appointment', 'Monday - Friday', $pc);
$pc = str_replace('<br/>', ' | ', $pc);
$wpdb->update($wpdb->posts, array('post_content' => $pc), array('ID' => 1532));

echo "\n--- After fix ---\n";
$data2 = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 1532 AND meta_key = '_elementor_data'");
preg_match_all('#"title"\s*:\s*"([^"]*(?:Call|Monday|031|064|8am)[^"]*)"#i', $data2, $titles2);
echo "Titles:\n";
foreach ($titles2[1] as $t) echo "  $t\n";
preg_match_all('#"pxl_content"\s*:\s*"([^"]*(?:5pm|Appointment|064|031)[^"]*)"#i', $data2, $contents2);
echo "pxl_content:\n";
foreach ($contents2[1] as $c) echo "  $c\n";

// Also fix footer (ID: 1475) same way
$fdata = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 1475 AND meta_key = '_elementor_data'");
if ($fdata) {
    $fdata = str_replace('Monday - Friday (8am - 5pm)<br/>Saturday: Open by Appointment', 'Monday - Friday', $fdata);
    $fdata = str_replace('Monday - Friday (8am - 5pm) | Saturday: Open by Appointment', 'Monday - Friday', $fdata);
    $fdata = str_replace('<br\\/>', ' | ', $fdata);
    $fdata = preg_replace(
        '#("title"\s*:\s*"Monday - Friday"[^}]*"pxl_content"\s*:\s*")([^"]*)(")#',
        '${1}8am - 5pm | Saturday: By Appointment${3}',
        $fdata
    );
    $wpdb->update($wpdb->postmeta, array('meta_value' => $fdata), array('post_id' => 1475, 'meta_key' => '_elementor_data'));
    echo "Footer fixed too.\n";
}

if (function_exists('wp_cache_flush')) wp_cache_flush();
if (class_exists('LiteSpeed\Purge')) LiteSpeed\Purge::purge_all();
if (class_exists('\Elementor\Plugin')) \Elementor\Plugin::$instance->files_manager->clear_cache();
echo "Done!\n";
