<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

// -------------------------------------------------------
// 1. FIX REVSLIDER HERO (slider 2 "Home 2", slide 4)
// -------------------------------------------------------
echo "--- 1. Fixing RevSlider Hero ---\n";
$slide = $wpdb->get_row("SELECT id, layers, params FROM {$wpdb->prefix}revslider_slides WHERE id = 4");
if ($slide) {
    $layers = $slide->layers;
    $params = $slide->params;

    $hero_replacements = array(
        'Generation' => 'Your Trusted',
        'Startup Agency.' => 'Financial Partner.',
        'Startup Agency' => 'Financial Partner',
        'Welcome Creative Agency' => 'Welcome to ATP Services',
        'We believe that great design should not be out of reach,our services are less than half the price of a full-time designer.' => 'Expert accounting, taxation, and payroll solutions to help your business thrive. We handle the numbers so you can focus on what matters.',
        'We believe that great design should not be out of reach' => 'Expert accounting, taxation, and payroll solutions to help your business thrive',
        'our services are less than half the price of a full-time designer' => 'We handle the numbers so you can focus on what matters',
    );

    foreach ($hero_replacements as $old => $new) {
        $layers = str_replace($old, $new, $layers);
        $params = str_replace($old, $new, $params);
    }

    $wpdb->update(
        "{$wpdb->prefix}revslider_slides",
        array('layers' => $layers, 'params' => $params),
        array('id' => 4)
    );
    echo "RevSlider slide 4 updated.\n";

    // Also check other slides in slider 2
    $other_slides = $wpdb->get_results("SELECT id, layers FROM {$wpdb->prefix}revslider_slides WHERE slider_id = 2 AND id != 4");
    foreach ($other_slides as $os) {
        $ol = $os->layers;
        $orig = $ol;
        foreach ($hero_replacements as $old => $new) {
            $ol = str_replace($old, $new, $ol);
        }
        if ($ol !== $orig) {
            $wpdb->update("{$wpdb->prefix}revslider_slides", array('layers' => $ol), array('id' => $os->id));
            echo "RevSlider slide {$os->id} also updated.\n";
        }
    }
} else {
    echo "Slide 4 not found!\n";
}

// Also check revslider_slides7 table
$slide7 = $wpdb->get_results("SELECT id, layers FROM {$wpdb->prefix}revslider_slides7 WHERE slider_id = 2");
foreach ($slide7 as $s7) {
    $l = $s7->layers;
    $orig = $l;
    $hero_reps7 = array(
        'Generation' => 'Your Trusted',
        'Startup Agency' => 'Financial Partner',
        'Welcome Creative Agency' => 'Welcome to ATP Services',
        'great design should not be out of reach' => 'accounting, taxation, and payroll solutions to help your business thrive',
    );
    foreach ($hero_reps7 as $old => $new) { $l = str_replace($old, $new, $l); }
    if ($l !== $orig) {
        $wpdb->update("{$wpdb->prefix}revslider_slides7", array('layers' => $l), array('id' => $s7->id));
        echo "RevSlider7 slide {$s7->id} updated.\n";
    }
}

// -------------------------------------------------------
// 2. FIX ELEMENTOR LIBRARY HOME 2 (ID: 4959)
// -------------------------------------------------------
echo "\n--- 2. Fixing Elementor Library Home 2 ---\n";
$data = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 4959 AND meta_key = '_elementor_data'");
if ($data) {
    $lib_replacements = array(
        'Startup Agency.' => 'Financial Partner.',
        'Startup Agency' => 'Financial Partner',
        'Welcome Creative Agency' => 'Welcome to ATP Services',
        'great design should not be out of reach' => 'accounting, taxation, and payroll solutions',
        'SEO Audit' => 'Tax Planning',
        'Keyword Research' => 'Bookkeeping',
        'Content Marketing' => 'Payroll Management',
        'Digital Marketing' => 'Tax Compliance',
        'Social Marketing' => 'Financial Reporting',
        'Strategic Planning' => 'Business Advisory',
    );
    foreach ($lib_replacements as $old => $new) { $data = str_replace($old, $new, $data); }
    $wpdb->update($wpdb->postmeta, array('meta_value' => $data), array('post_id' => 4959, 'meta_key' => '_elementor_data'));
    echo "Elementor Library Home 2 updated.\n";
}

// -------------------------------------------------------
// 3. FIX MEGA MENU - Remove Multipage/Onepage
// -------------------------------------------------------
echo "\n--- 3. Fixing Mega Menu ---\n";
$data = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 1160 AND meta_key = '_elementor_data'");
if ($data) {
    // Replace Multipage/Onepage with clean links
    $data = str_replace('Multipage', 'Home', $data);
    $data = str_replace('Onepage', '', $data);
    $wpdb->update($wpdb->postmeta, array('meta_value' => $data), array('post_id' => 1160, 'meta_key' => '_elementor_data'));

    $pc = get_post_field('post_content', 1160);
    $pc = str_replace('Multipage', 'Home', $pc);
    $pc = str_replace('Onepage', '', $pc);
    $wpdb->update($wpdb->posts, array('post_content' => $pc), array('ID' => 1160));
    echo "Mega Menu updated.\n";
}

// Also check what's in the mega menu
echo "\nMega Menu content snippets:\n";
$data2 = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = 1160 AND meta_key = '_elementor_data'");
preg_match_all('#"title"\s*:\s*"([^"]+)"#', $data2, $titles);
if (!empty($titles[1])) {
    foreach (array_unique($titles[1]) as $t) echo "  Title: $t\n";
}

// -------------------------------------------------------
// 4. Clear RevSlider cache
// -------------------------------------------------------
echo "\n--- 4. Clearing caches ---\n";
// Clear RevSlider transients
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '%revslider%' AND option_name LIKE '%transient%'");
if (function_exists('wp_cache_flush')) wp_cache_flush();
if (class_exists('LiteSpeed\Purge')) LiteSpeed\Purge::purge_all();
if (class_exists('\Elementor\Plugin')) \Elementor\Plugin::$instance->files_manager->clear_cache();
echo "All caches cleared.\n\nDone!\n";
