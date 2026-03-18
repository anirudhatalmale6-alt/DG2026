<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

$black_logo_url = 'https://atpservices.co.za/wp-content/uploads/2026/03/atplogolong.png';
$white_logo_url = 'https://atpservices.co.za/wp-content/uploads/2026/03/atp-logo-white.png';

// Check what logo URLs are in Header Home 2
$data = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 1477 AND meta_key = '_elementor_data'");

// Find all image URLs
preg_match_all('#"url"\s*:\s*"([^"]*)"#', $data, $matches);
$img_urls = array_unique($matches[1]);
echo "=== Image URLs in Header Home 2 ===\n";
foreach ($img_urls as $url) {
    if (strpos($url, '.png') !== false || strpos($url, '.jpg') !== false || strpos($url, '.svg') !== false) {
        echo "$url\n";
    }
}

// Find logo-related content
preg_match_all('#.{0,50}logo.{0,50}#i', $data, $logo_matches);
echo "\n=== Logo context in Header Home 2 ===\n";
foreach (array_unique($logo_matches[0]) as $ctx) {
    echo "$ctx\n";
}

// Now do the same for other headers
foreach (array(1479, 1481) as $hid) {
    $d = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = '_elementor_data'", $hid));
    if ($d) {
        preg_match_all('#"url"\s*:\s*"([^"]*(?:logo|\.png|\.jpg)[^"]*)"#i', $d, $m);
        if (!empty($m[1])) {
            echo "\n=== Images in " . get_the_title($hid) . " ===\n";
            foreach (array_unique($m[1]) as $u) echo "$u\n";
        }
    }
}

// Check the current homepage for logo img tags
echo "\n=== Checking page source ===\n";
$homepage = file_get_contents('https://atpservices.co.za/?nocache=' . time());
preg_match_all('#src="([^"]*logo[^"]*)"#i', $homepage, $src_matches);
foreach (array_unique($src_matches[1]) as $src) {
    echo "Logo src: $src\n";
}
