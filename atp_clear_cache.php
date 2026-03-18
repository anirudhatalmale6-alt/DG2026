<?php
require_once dirname(__FILE__) . '/wp-load.php';

// Verify setting is correct
echo "page_on_front: " . get_option('page_on_front') . "\n";
echo "show_on_front: " . get_option('show_on_front') . "\n";

// Clear all caches
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo "WP object cache flushed.\n";
}

// Clear LiteSpeed cache if present
if (class_exists('LiteSpeed\Purge')) {
    LiteSpeed\Purge::purge_all();
    echo "LiteSpeed cache purged.\n";
} elseif (defined('LSCWP_DIR')) {
    do_action('litespeed_purge_all');
    echo "LiteSpeed cache purge action triggered.\n";
}

// Clear any transients related to homepage
delete_transient('front_page_id');

// Elementor cache
if (class_exists('\Elementor\Plugin')) {
    \Elementor\Plugin::$instance->files_manager->clear_cache();
    echo "Elementor cache cleared.\n";
}

// WP Super Cache
if (function_exists('wp_cache_clear_cache')) {
    wp_cache_clear_cache();
    echo "WP Super Cache cleared.\n";
}

// W3 Total Cache
if (function_exists('w3tc_flush_all')) {
    w3tc_flush_all();
    echo "W3 Total Cache cleared.\n";
}

echo "\nDone! Try loading the homepage with ?nocache=" . time() . "\n";
