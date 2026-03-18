<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

$old_text = 'Expert accounting, taxation, and payroll solutions to help your business thrive. We handle the numbers so you can focus on what matters.';
$new_text = 'Expert accounting, taxation, and payroll solutions. We handle the numbers so you can focus on what matters.';

// Update RevSlider slide
$slide = $wpdb->get_row("SELECT id, layers, params FROM {$wpdb->prefix}revslider_slides WHERE id = 4");
if ($slide) {
    $layers = str_replace($old_text, $new_text, $slide->layers);
    $params = str_replace($old_text, $new_text, $slide->params);
    $wpdb->update("{$wpdb->prefix}revslider_slides", array('layers' => $layers, 'params' => $params), array('id' => 4));
    echo "RevSlider slide 4 updated.\n";
}

// Also check other slides in slider 2
$others = $wpdb->get_results("SELECT id, layers FROM {$wpdb->prefix}revslider_slides WHERE slider_id = 2 AND id != 4");
foreach ($others as $s) {
    $l = str_replace($old_text, $new_text, $s->layers);
    if ($l !== $s->layers) {
        $wpdb->update("{$wpdb->prefix}revslider_slides", array('layers' => $l), array('id' => $s->id));
        echo "RevSlider slide {$s->id} also updated.\n";
    }
}

// Check revslider_slides7 too
$s7 = $wpdb->get_results("SELECT id, layers FROM {$wpdb->prefix}revslider_slides7 WHERE slider_id = 2");
foreach ($s7 as $s) {
    $l = str_replace($old_text, $new_text, $s->layers);
    if ($l !== $s->layers) {
        $wpdb->update("{$wpdb->prefix}revslider_slides7", array('layers' => $l), array('id' => $s->id));
        echo "RevSlider7 slide {$s->id} updated.\n";
    }
}

// Also update Elementor Library Home 2 (ID: 4959) and Home 2 page (ID: 971)
foreach (array(4959, 971) as $pid) {
    $data = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = '_elementor_data'", $pid));
    if ($data && strpos($data, $old_text) !== false) {
        $data = str_replace($old_text, $new_text, $data);
        $wpdb->update($wpdb->postmeta, array('meta_value' => $data), array('post_id' => $pid, 'meta_key' => '_elementor_data'));
        echo "Elementor post $pid updated.\n";
    }
}

// Clear caches
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '%revslider%' AND option_name LIKE '%transient%'");
if (function_exists('wp_cache_flush')) wp_cache_flush();
if (class_exists('LiteSpeed\Purge')) LiteSpeed\Purge::purge_all();
if (class_exists('\Elementor\Plugin')) \Elementor\Plugin::$instance->files_manager->clear_cache();
echo "Caches cleared. Done!\n";
