<?php
require_once dirname(__FILE__) . '/wp-load.php';

// First, let's check what pages currently exist on the new site
global $wpdb;
$pages = $wpdb->get_results("SELECT ID, post_name, post_title FROM {$wpdb->posts} WHERE post_type='page' AND post_status='publish' ORDER BY post_title");

echo "=== CURRENT PAGES ===\n";
foreach ($pages as $p) {
    echo "/{$p->post_name}/ => {$p->post_title} (ID: {$p->ID})\n";
}

// Now let's check if .htaccess exists and what's in it
echo "\n=== CURRENT .HTACCESS ===\n";
$htaccess = ABSPATH . '.htaccess';
if (file_exists($htaccess)) {
    $content = file_get_contents($htaccess);
    echo $content . "\n";
} else {
    echo "No .htaccess found\n";
}

// Check if there are any existing redirects
echo "\n=== CHECKING FOR REDIRECT PLUGINS ===\n";
$active_plugins = get_option('active_plugins', array());
foreach ($active_plugins as $plugin) {
    if (stripos($plugin, 'redirect') !== false || stripos($plugin, '301') !== false) {
        echo "Found redirect plugin: $plugin\n";
    }
}
echo "Active plugins: " . implode(', ', $active_plugins) . "\n";

echo "\nDone!\n";
