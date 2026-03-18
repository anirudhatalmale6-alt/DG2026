<?php
require_once dirname(__FILE__) . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
global $wpdb;

echo "=== ATP Full Homepage Update ===\n\n";

// -------------------------------------------------------
// 1. FIX LOGO in Header Home 2 (ID: 1477), Sticky (1479), Mobile (1481)
// -------------------------------------------------------
echo "--- 1. Fixing logos ---\n";
$black_logo_url = 'https://atpservices.co.za/wp-content/uploads/2026/03/atplogolong.png';
$black_logo_id = 5093;

$header_ids = array(1477, 1479, 1481);
foreach ($header_ids as $hid) {
    $data = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = '_elementor_data'", $hid));
    if (!$data) continue;
    $original = $data;

    // Replace any casethemes.net logo URLs
    $data = preg_replace('#https?://demo\.casethemes\.net/[^"]*?logo[^"]*?\.(png|jpg|svg)#', $black_logo_url, $data);
    // Also replace escaped versions
    $data = preg_replace('#https?:\\\\/\\\\/demo\.casethemes\.net\\\\/[^"]*?logo[^"]*?\.(png|jpg|svg)#', str_replace('/', '\\/', $black_logo_url), $data);

    if ($data !== $original) {
        $wpdb->update($wpdb->postmeta, array('meta_value' => $data), array('post_id' => $hid, 'meta_key' => '_elementor_data'));
        echo "Logo fixed in: " . get_the_title($hid) . " (ID: $hid)\n";
    }
}

// Also fix logo in Home 2 page meta (logo_m field)
$wpdb->query($wpdb->prepare(
    "UPDATE {$wpdb->postmeta} SET meta_value = %s WHERE post_id = 971 AND meta_key = 'logo_m'",
    serialize(array('url' => $black_logo_url, 'id' => strval($black_logo_id), 'height' => '', 'width' => '', 'thumbnail' => ''))
));
echo "Page-level logo meta updated for Home 2.\n";

// -------------------------------------------------------
// 2. UPDATE HEADER HOME 2 (ID: 1477) - top bar content
// -------------------------------------------------------
echo "\n--- 2. Updating Header Home 2 top bar ---\n";
$data = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 1477 AND meta_key = '_elementor_data'");
if ($data) {
    $replacements = array(
        'Hello!! Welcome to Proactive' => 'Welcome to Accounting Taxation and Payroll',
        'info@gmail.com' => 'info@atpservices.co.za',
        '(213)839-93-9839' => '( 064 ) 507 2274',
        '213)839-93-9839' => '064 ) 507 2274',
        'Start Consult' => 'Book a Consultation',
        'Investors' => 'Tax Services',
        'Download' => 'Payroll',
        'Career' => 'Contact Us',
    );
    foreach ($replacements as $old => $new) {
        $data = str_replace($old, $new, $data);
    }
    $wpdb->update($wpdb->postmeta, array('meta_value' => $data), array('post_id' => 1477, 'meta_key' => '_elementor_data'));
    // Also update post_content
    $pc = get_post_field('post_content', 1477);
    foreach ($replacements as $old => $new) {
        $pc = str_replace($old, $new, $pc);
    }
    $wpdb->update($wpdb->posts, array('post_content' => $pc), array('ID' => 1477));
    echo "Header Home 2 updated.\n";
}

// Also update Sticky Header (1479) and Mobile Header (1481) if they have demo content
foreach (array(1479, 1481) as $hid) {
    $data = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = '_elementor_data'", $hid));
    if (!$data) continue;
    $original = $data;
    $reps = array(
        'Hello!! Welcome to Proactive' => 'Welcome to Accounting Taxation and Payroll',
        'info@gmail.com' => 'info@atpservices.co.za',
        '(213)839-93-9839' => '( 064 ) 507 2274',
        'Start Consult' => 'Book a Consultation',
        'Investors' => 'Tax Services',
        'Download' => 'Payroll',
        'Career' => 'Contact Us',
    );
    foreach ($reps as $old => $new) {
        $data = str_replace($old, $new, $data);
    }
    if ($data !== $original) {
        $wpdb->update($wpdb->postmeta, array('meta_value' => $data), array('post_id' => $hid, 'meta_key' => '_elementor_data'));
        $pc = get_post_field('post_content', $hid);
        foreach ($reps as $old => $new) { $pc = str_replace($old, $new, $pc); }
        $wpdb->update($wpdb->posts, array('post_content' => $pc), array('ID' => $hid));
        echo "Updated: " . get_the_title($hid) . " (ID: $hid)\n";
    }
}

// -------------------------------------------------------
// 3. CLEAN UP MENU - Remove Home sub-pages
// -------------------------------------------------------
echo "\n--- 3. Cleaning up menus ---\n";

// Main Menu (ID: 45) - remove Home sub-items
// Home item ID is 5004, sub-items would have menu_item_parent = 5004
$home_children = $wpdb->get_results("SELECT p.ID FROM {$wpdb->posts} p
    INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_menu_item_menu_item_parent' AND pm.meta_value = '5004'
    WHERE p.post_type = 'nav_menu_item'");
foreach ($home_children as $child) {
    wp_delete_post($child->ID, true);
    echo "Removed Home sub-menu item ID: {$child->ID}\n";
}

// Mobile Menu (ID: 50) - Home item ID is 4658
$home_children_mobile = $wpdb->get_results("SELECT p.ID FROM {$wpdb->posts} p
    INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_menu_item_menu_item_parent' AND pm.meta_value = '4658'
    WHERE p.post_type = 'nav_menu_item'");
foreach ($home_children_mobile as $child) {
    wp_delete_post($child->ID, true);
    echo "Removed Mobile Home sub-menu item ID: {$child->ID}\n";
}

// Remove Portfolio from menus (Main Menu item 5006 and its children)
$portfolio_children = $wpdb->get_results("SELECT p.ID FROM {$wpdb->posts} p
    INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_menu_item_menu_item_parent' AND pm.meta_value = '5006'
    WHERE p.post_type = 'nav_menu_item'");
foreach ($portfolio_children as $child) {
    wp_delete_post($child->ID, true);
}
wp_delete_post(5006, true);
echo "Removed Portfolio from Main Menu.\n";

// Remove Portfolio from Mobile Menu (item 4660 and children)
$portfolio_mobile = $wpdb->get_results("SELECT p.ID FROM {$wpdb->posts} p
    INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_menu_item_menu_item_parent' AND pm.meta_value = '4660'
    WHERE p.post_type = 'nav_menu_item'");
foreach ($portfolio_mobile as $child) {
    wp_delete_post($child->ID, true);
}
wp_delete_post(4660, true);
echo "Removed Portfolio from Mobile Menu.\n";

// Simplify Blog - remove deep sub-items (keep Blog Grid link only)
// Blog sub-items that are too deep
$blog_deep = array(5067, 5069, 5068, 5014, 5013, 5015, 5011, 5012, 5086, 5076, 5065);
foreach ($blog_deep as $id) {
    wp_delete_post($id, true);
}
// Rename Blog Grid to just "Blog" and make it direct child
$wpdb->update($wpdb->posts, array('post_title' => 'Blog'), array('ID' => 5066));
update_post_meta(5066, '_menu_item_menu_item_parent', '5007');
echo "Simplified Blog menu.\n";

// Similarly for mobile menu blog
$mobile_blog_deep = array(5054, 5055, 5056, 4998, 4999, 5003, 5000, 5001, 5002, 5058, 5057);
foreach ($mobile_blog_deep as $id) {
    wp_delete_post($id, true);
}
$wpdb->update($wpdb->posts, array('post_title' => 'Blog'), array('ID' => 5053));
update_post_meta(5053, '_menu_item_menu_item_parent', '4997');
echo "Simplified Mobile Blog menu.\n";

// Remove Pages sub-items we don't need (Team Details, Pricing Table, 404 Error)
$remove_page_items = array(5084, 5083, 5017); // Team Details, Pricing Table, 404
foreach ($remove_page_items as $id) {
    wp_delete_post($id, true);
}
// Mobile equivalents
$remove_mobile_page = array(5049, 5050, 5010);
foreach ($remove_mobile_page as $id) {
    wp_delete_post($id, true);
}
echo "Cleaned up Pages sub-menu.\n";

// Simplify Services - remove Single Service
wp_delete_post(5078, true);
wp_delete_post(5043, true); // mobile
echo "Removed Single Service from menus.\n";

// Simplify Shop - remove Single Product
wp_delete_post(5092, true);
wp_delete_post(5075, true); // mobile
echo "Cleaned up Shop sub-menu.\n";

echo "Menu cleanup done.\n";

// -------------------------------------------------------
// 4. UPDATE HOME 2 PAGE BODY CONTENT (ID: 971)
// -------------------------------------------------------
echo "\n--- 4. Updating Home 2 page content ---\n";
$data = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 971 AND meta_key = '_elementor_data'");
if ($data) {
    $body_replacements = array(
        // Hero
        'Generation' => 'Your Trusted',
        'Startup Agency.' => 'Financial Partner.',
        'Welcome Creative Agency' => 'Welcome to ATP Services',
        'We believe that great design should not be out of reach,our services are less than half the price of a full-time designer.' => 'Expert accounting, taxation, and payroll solutions to help your business thrive. We handle the numbers so you can focus on what matters.',
        'We are the best agency' => 'Expert Financial Solutions',
        'Welcome to Our Start-up Agency' => 'Welcome to ATP Services',

        // Service boxes
        'SEO Audit' => 'Tax Planning',
        'Keyword Research' => 'Bookkeeping',
        'Content Marketing' => 'Payroll Management',

        // Service cards
        'Digital Marketing' => 'Tax Compliance',
        'The Easiest Way to Improvet Your Site Speed' => 'Professional tax services to keep your business compliant and optimized',
        'Social Marketing' => 'Financial Reporting',
        'Strategic Planning' => 'Business Advisory',

        // Middle section
        'With SEO Optimization.' => 'With Expert Financial Care.',
        'Expect Great things From your SEO Agency' => 'Expect Great Things From Your Financial Partner',
        'Turn your ideas into reality with our exceptional software design and development team. Join the growing list of clients who have leveraged our expertise to scale their business.' => 'Partner with ATP for reliable accounting, taxation, and payroll services. Join the growing list of businesses that trust us to manage their finances.',
        'We are 100+ professional software engineers with more than 10 years of experience in delivering superior products Believe it because you&#8217;ve seen it. Here are real numbers' => 'We are a team of dedicated financial professionals with years of experience delivering superior services. Our track record speaks for itself.',
        'We are 100+ professional software engineers with more than 10 years of experience in delivering superior products' => 'We are a team of dedicated financial professionals with years of experience delivering superior services',

        // Stats
        'Successful Job' => 'Happy Clients',
        'Consulting Skill' => 'Tax Compliance',
        'GRE CBT Test' => 'Client Satisfaction',
        'Moneyback Guarentee' => 'Accurate Filing',
        'JEE CBT Test' => 'Years Experience',
        'Get a free quote' => 'Get a free consultation',

        // Why choose us
        'Why You choose Us?' => 'Why Choose Us?',
        'We are the best Startup Agency in UK' => 'We Are Your Trusted Accounting Partner in South Africa',
        'We are 100+ professional software engineers with' => 'We are a team of experienced financial professionals with',
        '10 years of experience in delivering superior product' => 'over 10 years of experience delivering quality financial services',
        'Application Development' => 'Tax Returns and Filing',
        'Marketing Consulting and Implementation' => 'Payroll Processing and Compliance',

        // Portfolio section
        'Our Awesome Portfolio' => 'Our Expertise',
        'We have the best portfolio for business growth' => 'Comprehensive financial services for your business growth',
        'Business Intelligence' => 'Tax Advisory',
        'Consulting Success' => 'Payroll Solutions',
        'Performing Market' => 'Financial Planning',
        'Business Consulting' => 'Accounting Services',

        // Featured services
        'Our Featured Services' => 'Our Core Services',
        'Our Premium Services we provided' => 'Premium Financial Services We Provide',
        'Search Engine Optimization' => 'Tax Planning and Filing',
        'Social Media Marketing' => 'Payroll Management',
        'Email Marketing' => 'Bookkeeping and Accounting',
        'Effective Business Maketing Strategy' => 'Expert tax planning to minimize your liability and ensure compliance',
        'Profitable Business makes to  You Happy' => 'Efficient payroll processing that keeps your team paid on time',
        'Complete Site Speed Optimization' => 'Accurate record-keeping and financial reporting for your business',

        // Testimonials
        'Feedback for Inspiration' => 'Client Testimonials',
        'What People Say for Our Success' => 'What Our Clients Say About Us',

        // Blog
        'Our Recent News' => 'Latest Insights',
        'Learn more from our new blog' => 'Stay updated with our latest financial tips and news',

        // Remaining agency references
        'agency' => 'firm',
        'Agency' => 'Firm',

        // Latin filler
        'Sagitis himos pulvinar morb socis posuere enim non auctor' => 'Trusted by businesses across South Africa for reliable financial services',
    );

    foreach ($body_replacements as $old => $new) {
        $data = str_replace($old, $new, $data);
    }

    $wpdb->update($wpdb->postmeta, array('meta_value' => $data), array('post_id' => 971, 'meta_key' => '_elementor_data'));

    // Also update post_content
    $pc = get_post_field('post_content', 971);
    foreach ($body_replacements as $old => $new) {
        $pc = str_replace($old, $new, $pc);
    }
    $wpdb->update($wpdb->posts, array('post_content' => $pc), array('ID' => 971));

    echo "Home 2 page content updated.\n";
}

// -------------------------------------------------------
// 5. UPDATE HIDDEN PANEL SIDEBAR HOME 2 (ID: 1532)
// -------------------------------------------------------
echo "\n--- 5. Updating Side Panel Popup ---\n";
$data = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 1532 AND meta_key = '_elementor_data'");
if ($data) {
    $panel_replacements = array(
        'Welcome to Proactive' => 'Welcome to ATP Services',
        'Proactive is a Digital Agency WordPress Theme' => 'ATP Services is your trusted accounting partner',
        'for any agency, marketing agency, video, technology, creative agency.' => 'providing expert accounting, taxation, and payroll services across South Africa.',
        'Proactive is a Digital' => 'ATP Services is your trusted',
        'info@gmail.com' => 'info@atpservices.co.za',
        'support@gmail.com' => 'support@atpservices.co.za',
        '(213)839-93-9839' => '( 064 ) 507 2274',
        '+12 123 456 7890' => '( 031 ) 101 3876',
        '+ 123 ( 9800 ) 987' => '( 064 ) 507 2274',
        '380 St Kilda Road,' => '29 Coedmore Road,',
        'Melbourne, Australia' => 'Bellair, Durban 4094',
        '(210) 123-451' => '( 031 ) 101 3876',
        'hello@proactive.com' => 'info@atpservices.co.za',
    );
    foreach ($panel_replacements as $old => $new) {
        $data = str_replace($old, $new, $data);
    }
    $wpdb->update($wpdb->postmeta, array('meta_value' => $data), array('post_id' => 1532, 'meta_key' => '_elementor_data'));

    $pc = get_post_field('post_content', 1532);
    foreach ($panel_replacements as $old => $new) { $pc = str_replace($old, $new, $pc); }
    $wpdb->update($wpdb->posts, array('post_content' => $pc), array('ID' => 1532));

    echo "Side panel popup updated.\n";
}

// -------------------------------------------------------
// 6. UPDATE FOOTER HOME 2 (ID: 1475) - remaining demo content
// -------------------------------------------------------
echo "\n--- 6. Updating Footer Home 2 ---\n";
$data = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 1475 AND meta_key = '_elementor_data'");
if ($data) {
    $footer_replacements = array(
        'Proactive is a Digital Agency WordPress Theme for any agency, marketing agency, video, technology, creative agency.' => 'ATP Services provides expert accounting, taxation, and payroll services tailored to your business needs across South Africa.',
        'Proactive is a Digital' => 'ATP Services provides expert',
        '380 St Kilda Road,' => '29 Coedmore Road,',
        'Melbourne, Australia' => 'Bellair, Durban 4094',
        'info@gmail.com' => 'info@atpservices.co.za',
    );
    foreach ($footer_replacements as $old => $new) {
        $data = str_replace($old, $new, $data);
    }
    $wpdb->update($wpdb->postmeta, array('meta_value' => $data), array('post_id' => 1475, 'meta_key' => '_elementor_data'));

    $pc = get_post_field('post_content', 1475);
    foreach ($footer_replacements as $old => $new) { $pc = str_replace($old, $new, $pc); }
    $wpdb->update($wpdb->posts, array('post_content' => $pc), array('ID' => 1475));

    echo "Footer Home 2 updated.\n";
}

// -------------------------------------------------------
// 7. CLEAR ALL CACHES
// -------------------------------------------------------
echo "\n--- 7. Clearing caches ---\n";
if (function_exists('wp_cache_flush')) { wp_cache_flush(); echo "WP cache flushed.\n"; }
if (class_exists('LiteSpeed\Purge')) { LiteSpeed\Purge::purge_all(); echo "LiteSpeed purged.\n"; }
elseif (defined('LSCWP_DIR')) { do_action('litespeed_purge_all'); echo "LiteSpeed purge triggered.\n"; }
if (class_exists('\Elementor\Plugin')) { \Elementor\Plugin::$instance->files_manager->clear_cache(); echo "Elementor cache cleared.\n"; }

echo "\n=== ALL DONE! ===\n";
