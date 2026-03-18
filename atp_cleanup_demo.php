<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

$changes = 0;

// All templates that might have demo content
$all_templates = $wpdb->get_results(
    "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_data' AND meta_value != ''"
);

echo "Scanning " . count($all_templates) . " Elementor templates...\n\n";

foreach ($all_templates as $row) {
    $data = $row->meta_value;
    $original = $data;
    $title = get_the_title($row->post_id);

    // 1. Footer tagline
    $data = str_replace(
        'Improve efficiency, provide a better Customer experience with modern Technolo services available.',
        'Professional accounting, taxation and payroll services tailored to your business needs. We are just a call or message away.',
        $data
    );
    $data = str_replace(
        'Improve efficiency, provide a better Customer experience with modern Technology services available.',
        'Professional accounting, taxation and payroll services tailored to your business needs. We are just a call or message away.',
        $data
    );

    // 2. Demo phone numbers -> real numbers
    $data = str_replace('+12 123 456 7890', '(031) 101 3876', $data);
    $data = str_replace('+12345550 87 15', '(064) 507 2274', $data);
    $data = str_replace('(210) 123-451', '(031) 101 3876', $data);
    $data = str_replace('+1 (210) 123-451', '(031) 101 3876', $data);

    // 3. Demo address in header
    $data = str_replace('6A V. Lobanovsky Ave, office 145', '29 Coedmore Road, Bellair, Durban 4094', $data);
    $data = str_replace('6A V. Lobanovsky Ave', '29 Coedmore Road, Bellair, Durban 4094', $data);

    // 4. Demo address Melbourne
    $data = str_replace('380 St Kilda Road, Melbourne, Australia', '29 Coedmore Road, Bellair, Durban 4094', $data);

    // 5. London Eye map reference
    $data = str_replace('London Eye, London, United Kingdom', '29 Coedmore Road, Bellair, Durban, South Africa', $data);

    // 6. Demo emails
    $data = str_replace('info@proactive.com', 'info@atpservices.co.za', $data);
    $data = str_replace('support@proactive.com', 'support@atpservices.co.za', $data);
    $data = str_replace('hello@proactive.com', 'info@atpservices.co.za', $data);
    $data = str_replace('contact@proactive.com', 'info@atpservices.co.za', $data);

    // 7. Demo tel links
    $data = str_replace('tel:2295550109', 'tel:+27311013876', $data);
    $data = str_replace('tel:+12345550', 'tel:+27311013876', $data);

    // 8. Google Maps links
    $data = str_replace(
        'maps?q=380+St+Kilda+Road,+Melbourne,+Australia',
        'maps?q=29+Coedmore+Road,+Bellair,+Durban,+4094,+South+Africa',
        $data
    );

    // 9. Hero text on contact page (if not already done)
    $data = str_replace(
        "That's fantastic to hear! Having a reliable team ready to assist ensures smooth operations and swift problem-solving. Your dedication is invaluable to our collective success.",
        "Whether you need expert tax advice, payroll management, or accounting support, our team is here to help. Reach out to us and let\xe2\x80\x99s get your finances in order.",
        $data
    );
    $data = str_replace(
        "That&#039;s fantastic to hear! Having a reliable team ready to assist ensures smooth operations and swift problem-solving.",
        "Whether you need expert tax advice, payroll management, or accounting support, our team is here to help.",
        $data
    );

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

// Also update post_content for the same replacements
$post_replacements = array(
    'Improve efficiency, provide a better Customer experience with modern Technolo services available.' => 'Professional accounting, taxation and payroll services tailored to your business needs. We are just a call or message away.',
    '+12 123 456 7890' => '(031) 101 3876',
    '+12345550 87 15' => '(064) 507 2274',
    '(210) 123-451' => '(031) 101 3876',
    '6A V. Lobanovsky Ave, office 145' => '29 Coedmore Road, Bellair, Durban 4094',
    '380 St Kilda Road, Melbourne, Australia' => '29 Coedmore Road, Bellair, Durban 4094',
    'London Eye, London, United Kingdom' => '29 Coedmore Road, Bellair, Durban, South Africa',
    'info@proactive.com' => 'info@atpservices.co.za',
    'support@proactive.com' => 'support@atpservices.co.za',
    'hello@proactive.com' => 'info@atpservices.co.za',
);

echo "\nUpdating post_content...\n";
foreach ($post_replacements as $old => $new) {
    $affected = $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s) WHERE post_content LIKE %s",
        $old, $new, '%' . $wpdb->esc_like($old) . '%'
    ));
    if ($affected > 0) {
        echo "  Replaced '$old' in $affected posts\n";
    }
}

// Clear Elementor cache
if (class_exists('\Elementor\Plugin')) {
    \Elementor\Plugin::$instance->files_manager->clear_cache();
    echo "\nElementor cache cleared.\n";
}

echo "\nTotal Elementor templates updated: $changes\n";
echo "Done!\n";
