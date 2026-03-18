<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

$page_id = 5194; // Founder's Message page
$photo_id = 5193;
$photo_url = wp_get_attachment_url($photo_id);

echo "Photo URL: $photo_url\n";

// Get a theme image for backgrounds
$bg_section = 'https://atpservices.co.za/wp-content/uploads/2024/05/h1-bg-section3.jpg';
$bg_section5 = 'https://atpservices.co.za/wp-content/uploads/2024/05/h1-bg-section5.jpg';
$shape1 = 'https://atpservices.co.za/wp-content/uploads/2024/05/h1-shape-1.png';

// Build rich Elementor data using Proactive theme widgets
$elementor_data = array(

    // ============================================
    // SECTION 1: Hero intro with photo + heading
    // ============================================
    array(
        'id' => 'fn_sec1',
        'elType' => 'section',
        'settings' => array(
            'gap' => 'extended',
            'structure' => '20',
            'padding' => array(
                'unit' => 'px',
                'top' => '80',
                'right' => '0',
                'bottom' => '60',
                'left' => '0',
                'isLinked' => false,
            ),
            'padding_mobile' => array(
                'unit' => 'px',
                'top' => '40',
                'right' => '15',
                'bottom' => '30',
                'left' => '15',
                'isLinked' => false,
            ),
        ),
        'elements' => array(
            // Left column - Photo with styling
            array(
                'id' => 'fn_col1',
                'elType' => 'column',
                'settings' => array(
                    '_column_size' => 42,
                    '_inline_size' => 42,
                    '_inline_size_tablet' => 100,
                ),
                'elements' => array(
                    // Founder photo
                    array(
                        'id' => 'fn_img1',
                        'elType' => 'widget',
                        'widgetType' => 'pxl_image',
                        'settings' => array(
                            'image' => array(
                                'url' => $photo_url,
                                'id' => $photo_id,
                            ),
                            'img_size' => 'full',
                            'pxl_animate' => 'wow PXLfadeInLeft',
                        ),
                    ),
                ),
            ),
            // Right column - Heading + intro text
            array(
                'id' => 'fn_col2',
                'elType' => 'column',
                'settings' => array(
                    '_column_size' => 58,
                    '_inline_size' => 58,
                    '_inline_size_tablet' => 100,
                    'margin' => array(
                        'unit' => 'px',
                        'top' => '0',
                        'right' => '0',
                        'bottom' => '0',
                        'left' => '20',
                        'isLinked' => false,
                    ),
                    'margin_tablet' => array(
                        'unit' => 'px',
                        'top' => '30',
                        'right' => '0',
                        'bottom' => '0',
                        'left' => '0',
                        'isLinked' => false,
                    ),
                ),
                'elements' => array(
                    // Heading with subtitle
                    array(
                        'id' => 'fn_hdg1',
                        'elType' => 'widget',
                        'widgetType' => 'pxl_heading',
                        'settings' => array(
                            'sub_title' => 'Meet Our Founder',
                            'title' => 'A Message from Krish Moodley',
                            'title_tag' => 'h2',
                            'sub_title_style' => 'px-sub-title-shape',
                            'hight_light_title' => '',
                            'pxl_animate' => 'pxl-split-text split-in-right',
                            'title_typography_typography' => 'custom',
                            'title_typography_font_size' => array('unit' => 'px', 'size' => 42),
                            'title_typography_font_size_tablet_extra' => array('unit' => 'px', 'size' => 36),
                            'title_typography_font_size_tablet' => array('unit' => 'px', 'size' => 32),
                            'title_typography_font_size_mobile' => array('unit' => 'px', 'size' => 28),
                            'title_typography_line_height' => array('unit' => 'em', 'size' => 1.2),
                            'title_space' => array('unit' => 'px', 'size' => 20),
                        ),
                    ),
                    // Intro text
                    array(
                        'id' => 'fn_txt1',
                        'elType' => 'widget',
                        'widgetType' => 'pxl_text_editor',
                        'settings' => array(
                            'text_editor' => '<p>At Accounting, Taxation and Payroll (ATP Services), our journey began with a simple but powerful objective — to provide businesses in South Africa with reliable, professional, and structured financial services that go beyond traditional accounting.</p><p>Over the years, I have seen many businesses struggle — not because they lack potential, but because they lack proper financial structure, compliance support, and clear visibility over their numbers. This is where we made a decision to do things differently.</p><p>ATP Services was built on the belief that every business deserves access to accurate financial management, expert tax guidance, efficient payroll systems, and full compliance with SARS and CIPC requirements — without complexity or confusion.</p>',
                            'text_editor_typography_typography' => 'custom',
                            'text_editor_typography_font_size' => array('unit' => 'px', 'size' => 17),
                            'text_editor_typography_line_height' => array('unit' => 'em', 'size' => 1.75),
                            'text_color' => '#555555',
                            'max_width' => array('unit' => 'px', 'size' => 540),
                        ),
                    ),
                    // Name + Title
                    array(
                        'id' => 'fn_nm1',
                        'elType' => 'widget',
                        'widgetType' => 'pxl_heading',
                        'settings' => array(
                            'title' => 'Krish Moodley',
                            'sub_title' => 'Founder & Director',
                            'title_tag' => 'h4',
                            'sub_title_style' => '',
                            'title_typography_typography' => 'custom',
                            'title_typography_font_size' => array('unit' => 'px', 'size' => 20),
                            'title_typography_font_weight' => '600',
                            'title_color' => '#1a1a1a',
                            'sub_title_color' => '#e65025',
                            'title_space' => array('unit' => 'px', 'size' => 5),
                        ),
                    ),
                    // CTA buttons
                    array(
                        'id' => 'fn_btns',
                        'elType' => 'section',
                        'isInner' => true,
                        'settings' => array(
                            'structure' => '20',
                            'gap' => 'default',
                            'padding' => array(
                                'unit' => 'px',
                                'top' => '20',
                                'right' => '0',
                                'bottom' => '0',
                                'left' => '0',
                                'isLinked' => false,
                            ),
                        ),
                        'elements' => array(
                            array(
                                'id' => 'fn_bc1',
                                'elType' => 'column',
                                'settings' => array('_column_size' => 50, '_inline_size' => 50),
                                'elements' => array(
                                    array(
                                        'id' => 'fn_btn1',
                                        'elType' => 'widget',
                                        'widgetType' => 'pxl_button',
                                        'settings' => array(
                                            'text' => 'Book a Consultation',
                                            'link' => array('url' => '/contact-us/', 'is_external' => '', 'nofollow' => ''),
                                            'style' => 'btn-icon-box',
                                            'selected_icon' => array('value' => 'flaticon-up-right-arrow', 'library' => ''),
                                            'btn_text_effect' => 'btn-text-parallax',
                                            'pxl_animate' => 'wow PXLfadeInUp',
                                        ),
                                    ),
                                ),
                            ),
                            array(
                                'id' => 'fn_bc2',
                                'elType' => 'column',
                                'settings' => array('_column_size' => 50, '_inline_size' => 50),
                                'elements' => array(
                                    array(
                                        'id' => 'fn_cp1',
                                        'elType' => 'widget',
                                        'widgetType' => 'pxl_call_phone',
                                        'settings' => array(
                                            'title' => 'Call Anytime',
                                            'phone_number' => '( 064 ) 507 2274',
                                            'style' => 'style-2',
                                            'selected_icon' => array('value' => 'flaticon-call', 'library' => ''),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    // ============================================
    // SECTION 2: Dark quote section - "We don't just process numbers"
    // ============================================
    array(
        'id' => 'fn_sec2',
        'elType' => 'section',
        'settings' => array(
            'background_background' => 'classic',
            'background_color' => '#1A1A1A',
            'background_image' => array(
                'url' => $bg_section,
                'id' => '',
            ),
            'background_size' => 'cover',
            'background_position' => 'center center',
            'background_overlay_background' => 'classic',
            'background_overlay_color' => 'rgba(26, 26, 26, 0.92)',
            'layout' => 'full_width',
            'stretch_section' => 'section-stretched',
            'gap' => 'no',
            'padding' => array(
                'unit' => 'px',
                'top' => '100',
                'right' => '0',
                'bottom' => '100',
                'left' => '0',
                'isLinked' => false,
            ),
            'padding_mobile' => array(
                'unit' => 'px',
                'top' => '60',
                'right' => '20',
                'bottom' => '60',
                'left' => '20',
                'isLinked' => false,
            ),
        ),
        'elements' => array(
            array(
                'id' => 'fn_qc1',
                'elType' => 'column',
                'settings' => array('_column_size' => 100),
                'elements' => array(
                    array(
                        'id' => 'fn_qh1',
                        'elType' => 'widget',
                        'widgetType' => 'pxl_heading',
                        'settings' => array(
                            'sub_title' => 'Our Philosophy',
                            'title' => 'We Don\'t Just Process Numbers.',
                            'title_tag' => 'h2',
                            'sub_title_style' => 'px-sub-title-shape',
                            'align' => 'center',
                            'title_color' => '#FFFFFF',
                            'sub_title_color' => '#e65025',
                            'pxl_animate' => 'pxl-split-text split-in-right',
                            'title_typography_typography' => 'custom',
                            'title_typography_font_size' => array('unit' => 'px', 'size' => 48),
                            'title_typography_font_size_tablet' => array('unit' => 'px', 'size' => 36),
                            'title_typography_font_size_mobile' => array('unit' => 'px', 'size' => 28),
                            'title_typography_line_height' => array('unit' => 'em', 'size' => 1.3),
                            'title_space' => array('unit' => 'px', 'size' => 30),
                            'h_width' => array('unit' => 'px', 'size' => 700),
                        ),
                    ),
                    // Three counters/statements
                    array(
                        'id' => 'fn_qin',
                        'elType' => 'section',
                        'isInner' => true,
                        'settings' => array(
                            'structure' => '30',
                            'gap' => 'extended',
                            'content_width' => array('unit' => 'px', 'size' => 900),
                            'padding' => array(
                                'unit' => 'px',
                                'top' => '20',
                                'right' => '0',
                                'bottom' => '0',
                                'left' => '0',
                                'isLinked' => false,
                            ),
                        ),
                        'elements' => array(
                            array(
                                'id' => 'fn_qc2',
                                'elType' => 'column',
                                'settings' => array('_column_size' => 33, '_inline_size' => 33),
                                'elements' => array(
                                    array(
                                        'id' => 'fn_ib1',
                                        'elType' => 'widget',
                                        'widgetType' => 'pxl_icon_box',
                                        'settings' => array(
                                            'title' => 'We Build Systems',
                                            'description' => 'Structured financial processes tailored to your business needs.',
                                            'selected_icon' => array('value' => 'flaticon-results', 'library' => ''),
                                            'title_color' => '#FFFFFF',
                                            'desc_color' => '#B6B6B6',
                                            'icon_color' => '#e65025',
                                            'position' => 'top',
                                            'pxl_animate' => 'wow PXLfadeInUp',
                                        ),
                                    ),
                                ),
                            ),
                            array(
                                'id' => 'fn_qc3',
                                'elType' => 'column',
                                'settings' => array('_column_size' => 33, '_inline_size' => 33),
                                'elements' => array(
                                    array(
                                        'id' => 'fn_ib2',
                                        'elType' => 'widget',
                                        'widgetType' => 'pxl_icon_box',
                                        'settings' => array(
                                            'title' => 'We Create Clarity',
                                            'description' => 'Clear visibility over your numbers, compliance, and growth.',
                                            'selected_icon' => array('value' => 'flaticon-seo', 'library' => ''),
                                            'title_color' => '#FFFFFF',
                                            'desc_color' => '#B6B6B6',
                                            'icon_color' => '#e65025',
                                            'position' => 'top',
                                            'pxl_animate' => 'wow PXLfadeInUp',
                                            'pxl_animate_delay' => '100',
                                        ),
                                    ),
                                ),
                            ),
                            array(
                                'id' => 'fn_qc4',
                                'elType' => 'column',
                                'settings' => array('_column_size' => 33, '_inline_size' => 33),
                                'elements' => array(
                                    array(
                                        'id' => 'fn_ib3',
                                        'elType' => 'widget',
                                        'widgetType' => 'pxl_icon_box',
                                        'settings' => array(
                                            'title' => 'We Provide Confidence',
                                            'description' => 'Peace of mind that your business is compliant and positioned for growth.',
                                            'selected_icon' => array('value' => 'flaticon-technical-s', 'library' => ''),
                                            'title_color' => '#FFFFFF',
                                            'desc_color' => '#B6B6B6',
                                            'icon_color' => '#e65025',
                                            'position' => 'top',
                                            'pxl_animate' => 'wow PXLfadeInUp',
                                            'pxl_animate_delay' => '200',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    // ============================================
    // SECTION 3: Continued message - Our Approach
    // ============================================
    array(
        'id' => 'fn_sec3',
        'elType' => 'section',
        'settings' => array(
            'gap' => 'extended',
            'structure' => '20',
            'padding' => array(
                'unit' => 'px',
                'top' => '80',
                'right' => '0',
                'bottom' => '40',
                'left' => '0',
                'isLinked' => false,
            ),
        ),
        'elements' => array(
            array(
                'id' => 'fn_s3c1',
                'elType' => 'column',
                'settings' => array(
                    '_column_size' => 50,
                    '_inline_size' => 50,
                    '_inline_size_tablet' => 100,
                ),
                'elements' => array(
                    array(
                        'id' => 'fn_s3h1',
                        'elType' => 'widget',
                        'widgetType' => 'pxl_heading',
                        'settings' => array(
                            'sub_title' => 'Our Approach',
                            'title' => 'Precision, Integrity, and Proactive Support',
                            'title_tag' => 'h2',
                            'sub_title_style' => 'px-sub-title-shape',
                            'pxl_animate' => 'pxl-split-text split-in-right',
                            'title_typography_typography' => 'custom',
                            'title_typography_font_size' => array('unit' => 'px', 'size' => 36),
                            'title_typography_font_size_tablet' => array('unit' => 'px', 'size' => 30),
                            'title_typography_font_size_mobile' => array('unit' => 'px', 'size' => 26),
                            'title_typography_line_height' => array('unit' => 'em', 'size' => 1.2),
                            'title_space' => array('unit' => 'px', 'size' => 20),
                        ),
                    ),
                    array(
                        'id' => 'fn_s3t1',
                        'elType' => 'widget',
                        'widgetType' => 'pxl_text_editor',
                        'settings' => array(
                            'text_editor' => '<p>Our approach is centred on precision, integrity, and proactive support. We work closely with our clients to ensure that their businesses are not only compliant, but also positioned for growth and long-term success.</p><p>In today\'s business environment, compliance is not optional — it is essential. But beyond compliance, businesses need a partner who understands their challenges and provides practical, reliable solutions.</p>',
                            'text_editor_typography_typography' => 'custom',
                            'text_editor_typography_font_size' => array('unit' => 'px', 'size' => 17),
                            'text_editor_typography_line_height' => array('unit' => 'em', 'size' => 1.75),
                            'text_color' => '#555555',
                            'max_width' => array('unit' => 'px', 'size' => 520),
                        ),
                    ),
                ),
            ),
            array(
                'id' => 'fn_s3c2',
                'elType' => 'column',
                'settings' => array(
                    '_column_size' => 50,
                    '_inline_size' => 50,
                    '_inline_size_tablet' => 100,
                ),
                'elements' => array(
                    array(
                        'id' => 'fn_s3h2',
                        'elType' => 'widget',
                        'widgetType' => 'pxl_heading',
                        'settings' => array(
                            'sub_title' => 'Our Commitment',
                            'title' => 'That Is What ATP Services Represents',
                            'title_tag' => 'h2',
                            'sub_title_style' => 'px-sub-title-shape',
                            'pxl_animate' => 'pxl-split-text split-in-right',
                            'title_typography_typography' => 'custom',
                            'title_typography_font_size' => array('unit' => 'px', 'size' => 36),
                            'title_typography_font_size_tablet' => array('unit' => 'px', 'size' => 30),
                            'title_typography_font_size_mobile' => array('unit' => 'px', 'size' => 26),
                            'title_typography_line_height' => array('unit' => 'em', 'size' => 1.2),
                            'title_space' => array('unit' => 'px', 'size' => 20),
                        ),
                    ),
                    array(
                        'id' => 'fn_s3t2',
                        'elType' => 'widget',
                        'widgetType' => 'pxl_text_editor',
                        'settings' => array(
                            'text_editor' => '<p>Whether you are starting a new venture or managing an established business, our commitment remains the same — to deliver professional accounting, taxation, payroll, and compliance services in South Africa that you can trust.</p><p>We look forward to working with you and supporting your business journey.</p>',
                            'text_editor_typography_typography' => 'custom',
                            'text_editor_typography_font_size' => array('unit' => 'px', 'size' => 17),
                            'text_editor_typography_line_height' => array('unit' => 'em', 'size' => 1.75),
                            'text_color' => '#555555',
                            'max_width' => array('unit' => 'px', 'size' => 520),
                        ),
                    ),
                    // Signature-style name
                    array(
                        'id' => 'fn_s3nm',
                        'elType' => 'widget',
                        'widgetType' => 'pxl_heading',
                        'settings' => array(
                            'title' => 'Krish Moodley',
                            'sub_title' => 'Founder & Director — Accounting, Taxation and Payroll (Pty) Ltd',
                            'title_tag' => 'h4',
                            'sub_title_style' => '',
                            'title_typography_typography' => 'custom',
                            'title_typography_font_size' => array('unit' => 'px', 'size' => 22),
                            'title_typography_font_weight' => '600',
                            'title_typography_font_style' => 'italic',
                            'title_color' => '#e65025',
                            'sub_title_color' => '#888888',
                            'title_space' => array('unit' => 'px', 'size' => 5),
                        ),
                    ),
                ),
            ),
        ),
    ),

    // ============================================
    // SECTION 4: CTA - matching About Us style
    // ============================================
    array(
        'id' => 'fn_sec4',
        'elType' => 'section',
        'settings' => array(
            'background_background' => 'classic',
            'background_color' => '#1A1A1A',
            'background_image' => array(
                'url' => $bg_section5,
                'id' => '',
            ),
            'background_size' => 'cover',
            'background_position' => 'center center',
            'background_overlay_background' => 'classic',
            'background_overlay_color' => 'rgba(26, 26, 26, 0.85)',
            'layout' => 'full_width',
            'stretch_section' => 'section-stretched',
            'gap' => 'no',
            'padding' => array(
                'unit' => 'px',
                'top' => '100',
                'right' => '0',
                'bottom' => '100',
                'left' => '0',
                'isLinked' => false,
            ),
            'padding_mobile' => array(
                'unit' => 'px',
                'top' => '60',
                'right' => '20',
                'bottom' => '60',
                'left' => '20',
                'isLinked' => false,
            ),
        ),
        'elements' => array(
            array(
                'id' => 'fn_s4c1',
                'elType' => 'column',
                'settings' => array('_column_size' => 100),
                'elements' => array(
                    array(
                        'id' => 'fn_s4h1',
                        'elType' => 'widget',
                        'widgetType' => 'pxl_heading',
                        'settings' => array(
                            'sub_title' => 'Let\'s Get Started',
                            'title' => 'Ready to Build a Strong Financial Foundation?',
                            'title_tag' => 'h2',
                            'sub_title_style' => 'px-sub-title-shape',
                            'align' => 'center',
                            'title_color' => '#FFFFFF',
                            'sub_title_color' => '#e65025',
                            'pxl_animate' => 'pxl-split-text split-in-right',
                            'title_typography_typography' => 'custom',
                            'title_typography_font_size' => array('unit' => 'px', 'size' => 42),
                            'title_typography_font_size_tablet' => array('unit' => 'px', 'size' => 32),
                            'title_typography_font_size_mobile' => array('unit' => 'px', 'size' => 26),
                            'title_typography_line_height' => array('unit' => 'em', 'size' => 1.3),
                            'title_space' => array('unit' => 'px', 'size' => 30),
                            'h_width' => array('unit' => 'px', 'size' => 650),
                        ),
                    ),
                    array(
                        'id' => 'fn_s4b1',
                        'elType' => 'widget',
                        'widgetType' => 'pxl_button',
                        'settings' => array(
                            'text' => 'Book a Consultation',
                            'link' => array('url' => '/contact-us/', 'is_external' => '', 'nofollow' => ''),
                            'style' => 'btn-icon-box',
                            'selected_icon' => array('value' => 'flaticon-up-right-arrow', 'library' => ''),
                            'btn_text_effect' => 'btn-text-parallax',
                            'align' => 'center',
                            'pxl_animate' => 'wow PXLfadeInUp',
                            'pxl_animate_delay' => '100',
                        ),
                    ),
                ),
            ),
        ),
    ),
);

// Save
$json = wp_slash(json_encode($elementor_data));
update_post_meta($page_id, '_elementor_data', $json);
delete_post_meta($page_id, '_elementor_css');

// Clear caches
if (function_exists('wp_cache_flush')) wp_cache_flush();
if (class_exists('LiteSpeed\Purge')) LiteSpeed\Purge::purge_all();
if (class_exists('\Elementor\Plugin')) \Elementor\Plugin::$instance->files_manager->clear_cache();

echo "Founder page redesigned with Proactive theme widgets!\n";
echo "URL: https://atpservices.co.za/message-from-the-founder/\n";
