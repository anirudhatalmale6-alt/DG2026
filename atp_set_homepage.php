<?php
require_once dirname(__FILE__) . '/wp-load.php';
global $wpdb;

// Find the Home 2 page
$home2 = $wpdb->get_row("SELECT ID, post_title, post_name FROM {$wpdb->posts} WHERE post_type='page' AND post_status='publish' AND (post_title = 'Home 2' OR post_name = 'home-2')");

if (!$home2) {
    die('Home 2 page not found!');
}

echo "Found: {$home2->post_title} (ID: {$home2->ID}, slug: {$home2->post_name})\n";

// Set WordPress to use a static front page
update_option('show_on_front', 'page');
update_option('page_on_front', $home2->ID);

echo "Homepage set to: {$home2->post_title} (ID: {$home2->ID})\n";

// Check if there's a blog page set
$blog_page = get_option('page_for_posts');
if (!$blog_page) {
    // Find a blog page if one exists
    $blog = $wpdb->get_row("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type='page' AND post_status='publish' AND (post_title LIKE '%blog%' OR post_name LIKE '%blog%') LIMIT 1");
    if ($blog) {
        update_option('page_for_posts', $blog->ID);
        echo "Blog page set to: {$blog->post_title} (ID: {$blog->ID})\n";
    }
}

echo "\nCurrent settings:\n";
echo "show_on_front: " . get_option('show_on_front') . "\n";
echo "page_on_front: " . get_option('page_on_front') . "\n";
echo "page_for_posts: " . get_option('page_for_posts') . "\n";

echo "\nDone! Homepage is now set to Home 2.\n";
