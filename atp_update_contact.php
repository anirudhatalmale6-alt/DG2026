<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

$page_id = 762; // Contact Us page

$data = $wpdb->get_var($wpdb->prepare(
    "SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = '_elementor_data'",
    $page_id
));

if (!$data) {
    die('No Elementor data found for page ' . $page_id);
}

// 1. Replace hero paragraph text
$old_text = "That's fantastic to hear! Having a reliable team ready to assist ensures smooth operations and swift problem-solving. Your dedication is invaluable to our collective success.";
$new_text = "Whether you need expert tax advice, payroll management, or accounting support, our team is here to help. Reach out to us and let\u2019s get your finances in order.";
$data = str_replace($old_text, $new_text, $data);

// Also handle escaped version
$old_text_esc = str_replace("'", "\\'", $old_text);
$new_text_esc = str_replace("'", "\\'", $new_text);

// 2. Fix mailto link (demo email)
$data = str_replace('mailto:info@proactive.com', 'mailto:info@atpservices.co.za', $data);

// 3. Fix tel link (demo phone)
$data = str_replace('tel:2295550109', 'tel:+27311013876', $data);

// 4. Fix Google Maps address in widget
$data = str_replace('London Eye, London, United Kingdom', '29 Coedmore Road, Bellair, Durban, South Africa', $data);

// 5. Fix Google Maps link URL
$data = str_replace(
    'https:\/\/www.google.com\/maps?q=380+St+Kilda+Road,+Melbourne,+Australia',
    'https:\/\/www.google.com\/maps?q=29+Coedmore+Road,+Bellair,+Durban,+4094,+South+Africa',
    $data
);
// Also unescaped version
$data = str_replace(
    'https://www.google.com/maps?q=380+St+Kilda+Road,+Melbourne,+Australia',
    'https://www.google.com/maps?q=29+Coedmore+Road,+Bellair,+Durban,+4094,+South+Africa',
    $data
);

// Update the database
$result = $wpdb->update(
    $wpdb->postmeta,
    array('meta_value' => $data),
    array('post_id' => $page_id, 'meta_key' => '_elementor_data')
);

echo "Contact Us page updated. Rows affected: $result\n";

// Now check other pages/footer for remaining demo content
echo "\n--- Checking for remaining demo content across all Elementor templates ---\n";

$demo_strings = array(
    'Improve efficiency, provide a better Customer experience',
    '+12 123 456 7890',
    '+12345550 87 15',
    '(210) 123-451',
    '6A V. Lobanovsky Ave',
    '380 St Kilda Road, Melbourne',
    'proactive.com',
    'London Eye, London',
);

foreach ($demo_strings as $str) {
    $found = $wpdb->get_results($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_data' AND meta_value LIKE %s",
        '%' . $wpdb->esc_like($str) . '%'
    ));
    if (!empty($found)) {
        $ids = array_map(function($r){ return $r->post_id . ' (' . get_the_title($r->post_id) . ')'; }, $found);
        echo "FOUND '$str' in: " . implode(', ', $ids) . "\n";
    }
}

// Also check post_content for demo strings
$found_content = $wpdb->get_results(
    "SELECT ID, post_title FROM {$wpdb->posts} WHERE post_status='publish' AND (post_content LIKE '%Improve efficiency%' OR post_content LIKE '%Lobanovsky%' OR post_content LIKE '%St Kilda%' OR post_content LIKE '%proactive.com%')"
);
if (!empty($found_content)) {
    echo "\nIn post_content:\n";
    foreach ($found_content as $p) {
        echo "  {$p->ID} ({$p->post_title})\n";
    }
}

// Clear Elementor cache
if (class_exists('\Elementor\Plugin')) {
    \Elementor\Plugin::$instance->files_manager->clear_cache();
    echo "\nElementor cache cleared.\n";
}

echo "\nDone!\n";
