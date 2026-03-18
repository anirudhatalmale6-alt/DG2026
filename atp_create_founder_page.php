<?php
require_once dirname(__FILE__) . '/wp-load.php';
require_once(ABSPATH . 'wp-admin/includes/image.php');
global $wpdb;

// ============================================
// STEP 1: Upload founder photo to media library
// ============================================
$upload_dir = wp_upload_dir();
$source = ABSPATH . 'dadclear.jpg';
$dest = $upload_dir['path'] . '/krish-moodley-founder.jpg';

if (!file_exists($source)) {
    echo "ERROR: dadclear.jpg not found in WordPress root\n";
    exit;
}

// Check if already uploaded
$existing = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_type='attachment' AND post_title='krish-moodley-founder'");
if ($existing) {
    $photo_id = $existing;
    echo "Photo already in media library: ID $photo_id\n";
} else {
    copy($source, $dest);
    $photo_id = wp_insert_attachment(array(
        'post_mime_type' => 'image/jpeg',
        'post_title' => 'krish-moodley-founder',
        'post_content' => '',
        'post_status' => 'inherit'
    ), $dest);
    $meta = wp_generate_attachment_metadata($photo_id, $dest);
    wp_update_attachment_metadata($photo_id, $meta);
    echo "Photo uploaded: ID $photo_id\n";
}
$photo_url = wp_get_attachment_url($photo_id);
echo "Photo URL: $photo_url\n";

// ============================================
// STEP 2: Create the page
// ============================================
$page_slug = 'message-from-the-founder';
$existing_page = get_page_by_path($page_slug);

if ($existing_page) {
    $page_id = $existing_page->ID;
    echo "Page already exists: ID $page_id\n";
} else {
    $page_id = wp_insert_post(array(
        'post_title' => 'Message from the Founder',
        'post_name' => $page_slug,
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_content' => '',
        'comment_status' => 'closed',
        'ping_status' => 'closed',
    ));
    echo "Page created: ID $page_id\n";
}

// ============================================
// STEP 3: Set page meta (same as other inner pages)
// ============================================
update_post_meta($page_id, 'header_layout', '1477');
update_post_meta($page_id, 'header_layout_sticky', '1479');
update_post_meta($page_id, 'header_mobile_layout', '1481');
update_post_meta($page_id, 'footer_layout', '1475');
update_post_meta($page_id, 'pt_mode', 'bd');
update_post_meta($page_id, 'ptitle_layout', '44');
update_post_meta($page_id, 'page_mobile_style', 'light');
update_post_meta($page_id, 'primary_color', '#e65025');
update_post_meta($page_id, 'gradient_color', array('from' => '#faa61a', 'to' => '#e65025'));
echo "Page meta set.\n";

// ============================================
// STEP 4: Build Elementor data
// ============================================
$elementor_data = array(
    // Section 1: Photo + Opening text
    array(
        'id' => 'f0und3r1',
        'elType' => 'section',
        'settings' => array(
            'gap' => 'extended',
            'content_width' => array('unit' => 'px', 'size' => 1170),
            'padding' => array(
                'unit' => 'px',
                'top' => '80',
                'right' => '0',
                'bottom' => '20',
                'left' => '0',
                'isLinked' => false,
            ),
            'padding_mobile' => array(
                'unit' => 'px',
                'top' => '40',
                'right' => '20',
                'bottom' => '10',
                'left' => '20',
                'isLinked' => false,
            ),
        ),
        'elements' => array(
            // Left column - Photo
            array(
                'id' => 'f0und3r2',
                'elType' => 'column',
                'settings' => array(
                    '_column_size' => 40,
                    '_inline_size' => 40,
                    '_inline_size_tablet' => 100,
                ),
                'elements' => array(
                    array(
                        'id' => 'f0und3r3',
                        'elType' => 'widget',
                        'widgetType' => 'image',
                        'settings' => array(
                            'image' => array(
                                'url' => $photo_url,
                                'id' => $photo_id,
                            ),
                            'image_size' => 'large',
                            'align' => 'center',
                            'width' => array('unit' => '%', 'size' => 90),
                            'border_radius' => array(
                                'unit' => 'px',
                                'top' => '10',
                                'right' => '10',
                                'bottom' => '10',
                                'left' => '10',
                                'isLinked' => true,
                            ),
                            '_css_classes' => 'founder-photo',
                        ),
                    ),
                    // Name and title below photo
                    array(
                        'id' => 'f0und3r4',
                        'elType' => 'widget',
                        'widgetType' => 'heading',
                        'settings' => array(
                            'title' => 'Krish Moodley',
                            'align' => 'center',
                            'title_color' => '#1a1a1a',
                            'typography_typography' => 'custom',
                            'typography_font_family' => 'Poppins',
                            'typography_font_size' => array('unit' => 'px', 'size' => 22),
                            'typography_font_weight' => '600',
                        ),
                    ),
                    array(
                        'id' => 'f0und3r5',
                        'elType' => 'widget',
                        'widgetType' => 'heading',
                        'settings' => array(
                            'title' => 'Founder & Director',
                            'header_size' => 'h4',
                            'align' => 'center',
                            'title_color' => '#e65025',
                            'typography_typography' => 'custom',
                            'typography_font_family' => 'Poppins',
                            'typography_font_size' => array('unit' => 'px', 'size' => 15),
                            'typography_font_weight' => '500',
                        ),
                    ),
                    array(
                        'id' => 'f0und3r6',
                        'elType' => 'widget',
                        'widgetType' => 'heading',
                        'settings' => array(
                            'title' => 'Accounting, Taxation and Payroll (Pty) Ltd',
                            'header_size' => 'h5',
                            'align' => 'center',
                            'title_color' => '#666666',
                            'typography_typography' => 'custom',
                            'typography_font_family' => 'Poppins',
                            'typography_font_size' => array('unit' => 'px', 'size' => 13),
                            'typography_font_weight' => '400',
                        ),
                    ),
                ),
            ),
            // Right column - Message
            array(
                'id' => 'f0und3r7',
                'elType' => 'column',
                'settings' => array(
                    '_column_size' => 60,
                    '_inline_size' => 60,
                    '_inline_size_tablet' => 100,
                ),
                'elements' => array(
                    // Subtitle
                    array(
                        'id' => 'f0und3r8',
                        'elType' => 'widget',
                        'widgetType' => 'heading',
                        'settings' => array(
                            'title' => 'A Message from the Founder',
                            'header_size' => 'h2',
                            'title_color' => '#1a1a1a',
                            'typography_typography' => 'custom',
                            'typography_font_family' => 'Poppins',
                            'typography_font_size' => array('unit' => 'px', 'size' => 32),
                            'typography_font_size_mobile' => array('unit' => 'px', 'size' => 24),
                            'typography_font_weight' => '600',
                        ),
                    ),
                    // Decorative line
                    array(
                        'id' => 'f0und3r9',
                        'elType' => 'widget',
                        'widgetType' => 'divider',
                        'settings' => array(
                            'color' => '#e65025',
                            'weight' => array('unit' => 'px', 'size' => 3),
                            'width' => array('unit' => 'px', 'size' => 60),
                            'gap' => array('unit' => 'px', 'size' => 20),
                        ),
                    ),
                    // Message paragraphs
                    array(
                        'id' => 'f0und3ra',
                        'elType' => 'widget',
                        'widgetType' => 'text-editor',
                        'settings' => array(
                            'editor' => '<p style="color: #555; font-size: 16px; line-height: 1.8; margin-bottom: 18px;">At Accounting, Taxation and Payroll (ATP Services), our journey began with a simple but powerful objective — to provide businesses in South Africa with reliable, professional, and structured financial services that go beyond traditional accounting.</p>

<p style="color: #555; font-size: 16px; line-height: 1.8; margin-bottom: 18px;">Over the years, I have seen many businesses struggle — not because they lack potential, but because they lack proper financial structure, compliance support, and clear visibility over their numbers. This is where we made a decision to do things differently.</p>

<p style="color: #555; font-size: 16px; line-height: 1.8; margin-bottom: 18px;">ATP Services was built on the belief that every business deserves access to accurate financial management, expert tax guidance, efficient payroll systems, and full compliance with SARS and CIPC requirements — without complexity or confusion.</p>

<p style="color: #1a1a1a; font-size: 18px; line-height: 1.8; margin-bottom: 18px; font-weight: 500;">We don\'t just process numbers.<br>We build systems.<br>We create clarity.<br>We provide confidence.</p>

<p style="color: #555; font-size: 16px; line-height: 1.8; margin-bottom: 18px;">Our approach is centred on precision, integrity, and proactive support. We work closely with our clients to ensure that their businesses are not only compliant, but also positioned for growth and long-term success.</p>

<p style="color: #555; font-size: 16px; line-height: 1.8; margin-bottom: 18px;">In today\'s business environment, compliance is not optional — it is essential. But beyond compliance, businesses need a partner who understands their challenges and provides practical, reliable solutions.</p>

<p style="color: #555; font-size: 16px; line-height: 1.8; margin-bottom: 18px;">That is what ATP Services represents.</p>

<p style="color: #555; font-size: 16px; line-height: 1.8; margin-bottom: 18px;">Whether you are starting a new venture or managing an established business, our commitment remains the same — to deliver professional accounting, taxation, payroll, and compliance services in South Africa that you can trust.</p>

<p style="color: #555; font-size: 16px; line-height: 1.8; margin-bottom: 30px;">We look forward to working with you and supporting your business journey.</p>',
                            'typography_typography' => 'custom',
                            'typography_font_family' => 'Open Sans',
                        ),
                    ),
                ),
            ),
        ),
    ),
    // Section 2: CTA
    array(
        'id' => 'f0undcta',
        'elType' => 'section',
        'settings' => array(
            'gap' => 'no',
            'background_background' => 'classic',
            'background_color' => '#f5f5f5',
            'padding' => array(
                'unit' => 'px',
                'top' => '60',
                'right' => '0',
                'bottom' => '60',
                'left' => '0',
                'isLinked' => false,
            ),
            'content_width' => array('unit' => 'px', 'size' => 1170),
        ),
        'elements' => array(
            array(
                'id' => 'f0undct1',
                'elType' => 'column',
                'settings' => array('_column_size' => 100),
                'elements' => array(
                    array(
                        'id' => 'f0undct2',
                        'elType' => 'widget',
                        'widgetType' => 'heading',
                        'settings' => array(
                            'title' => 'Ready to Work With Us?',
                            'align' => 'center',
                            'title_color' => '#1a1a1a',
                            'typography_typography' => 'custom',
                            'typography_font_family' => 'Poppins',
                            'typography_font_size' => array('unit' => 'px', 'size' => 28),
                            'typography_font_weight' => '600',
                        ),
                    ),
                    array(
                        'id' => 'f0undct3',
                        'elType' => 'widget',
                        'widgetType' => 'text-editor',
                        'settings' => array(
                            'editor' => '<p style="text-align: center; color: #666; font-size: 16px;">Get in touch with our team to discuss how we can support your business.</p>',
                            'align' => 'center',
                        ),
                    ),
                    array(
                        'id' => 'f0undct4',
                        'elType' => 'widget',
                        'widgetType' => 'button',
                        'settings' => array(
                            'text' => 'Book a Consultation',
                            'link' => array(
                                'url' => '/contact-us/',
                                'is_external' => '',
                                'nofollow' => '',
                            ),
                            'align' => 'center',
                            'size' => 'md',
                            'button_background_color' => '#e65025',
                            'button_text_color' => '#ffffff',
                            'border_radius' => array(
                                'unit' => 'px',
                                'top' => '30',
                                'right' => '30',
                                'bottom' => '30',
                                'left' => '30',
                                'isLinked' => true,
                            ),
                            'text_padding' => array(
                                'unit' => 'px',
                                'top' => '15',
                                'right' => '40',
                                'bottom' => '15',
                                'left' => '40',
                                'isLinked' => false,
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);

// Save Elementor data
$json = wp_slash(json_encode($elementor_data));
update_post_meta($page_id, '_elementor_data', $json);
update_post_meta($page_id, '_elementor_edit_mode', 'builder');
update_post_meta($page_id, '_elementor_template_type', 'wp-page');
update_post_meta($page_id, '_elementor_version', '3.25.10');
delete_post_meta($page_id, '_elementor_css');

echo "Elementor data saved.\n";

// ============================================
// STEP 5: Add to menu below About Us
// ============================================
$main_menu_id = 45; // Main Menu term_id
$mobile_menu_id = 50; // Mobile Menu term_id

// Find the About Us menu item in main menu
$menu_items = wp_get_nav_menu_items($main_menu_id);
$about_menu_item_id = 0;
$about_position = 0;
$founder_exists = false;

foreach ($menu_items as $item) {
    if ($item->object_id == 1201 && $item->object == 'page') {
        $about_menu_item_id = $item->ID;
        $about_position = $item->menu_order;
        echo "Found About Us menu item: ID {$item->ID}, position {$item->menu_order}\n";
    }
    if ($item->object_id == $page_id && $item->object == 'page') {
        $founder_exists = true;
        echo "Founder page already in menu.\n";
    }
}

if (!$founder_exists) {
    // First, shift all menu items after About Us down by 1
    foreach ($menu_items as $item) {
        if ($item->menu_order > $about_position) {
            $wpdb->update(
                $wpdb->postmeta,
                array('meta_value' => $item->menu_order + 1),
                array('post_id' => $item->ID, 'meta_key' => '_menu_item_position')
            );
        }
    }

    // Add founder page to main menu
    $menu_item_id = wp_update_nav_menu_item($main_menu_id, 0, array(
        'menu-item-title' => 'Founder\'s Message',
        'menu-item-object-id' => $page_id,
        'menu-item-object' => 'page',
        'menu-item-type' => 'post_type',
        'menu-item-status' => 'publish',
        'menu-item-position' => $about_position + 1,
    ));
    echo "Added to Main Menu: item ID $menu_item_id, position " . ($about_position + 1) . "\n";

    // Add to mobile menu too
    $mobile_items = wp_get_nav_menu_items($mobile_menu_id);
    $mobile_about_pos = 0;
    foreach ($mobile_items as $item) {
        if ($item->object_id == 1201 && $item->object == 'page') {
            $mobile_about_pos = $item->menu_order;
        }
    }
    foreach ($mobile_items as $item) {
        if ($item->menu_order > $mobile_about_pos) {
            $wpdb->update(
                $wpdb->postmeta,
                array('meta_value' => $item->menu_order + 1),
                array('post_id' => $item->ID, 'meta_key' => '_menu_item_position')
            );
        }
    }
    $mobile_item_id = wp_update_nav_menu_item($mobile_menu_id, 0, array(
        'menu-item-title' => 'Founder\'s Message',
        'menu-item-object-id' => $page_id,
        'menu-item-object' => 'page',
        'menu-item-type' => 'post_type',
        'menu-item-status' => 'publish',
        'menu-item-position' => $mobile_about_pos + 1,
    ));
    echo "Added to Mobile Menu: item ID $mobile_item_id\n";
}

// Clear caches
if (function_exists('wp_cache_flush')) wp_cache_flush();
if (class_exists('LiteSpeed\Purge')) LiteSpeed\Purge::purge_all();
if (class_exists('\Elementor\Plugin')) \Elementor\Plugin::$instance->files_manager->clear_cache();

echo "\nAll done! Page URL: https://atpservices.co.za/$page_slug/\n";
