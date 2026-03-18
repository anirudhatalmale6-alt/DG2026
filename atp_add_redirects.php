<?php
require_once dirname(__FILE__) . '/wp-load.php';

$htaccess = ABSPATH . '.htaccess';
$content = file_get_contents($htaccess);

// Old URLs found in Google search results mapped to new pages
$redirects = array(
    // Exact matches from Google index
    '/contact/' => '/contact-us/',
    '/contact' => '/contact-us/',
    '/accounting-services/' => '/our-services/',
    '/accounting-services' => '/our-services/',
    '/about-accounting-taxation-and-payroll/' => '/about-us/',
    '/about-accounting-taxation-and-payroll' => '/about-us/',

    // Common old URL patterns that might also be indexed
    '/about/' => '/about-us/',
    '/about' => '/about-us/',
    '/services/' => '/our-services/',
    '/services' => '/our-services/',
    '/blog/' => '/blogs/',
    '/blog' => '/blogs/',
    '/faq/' => '/faqs/',
    '/faq' => '/faqs/',
    '/team/' => '/about-us/',
    '/team' => '/about-us/',
    '/our-team/' => '/about-us/',
    '/our-team' => '/about-us/',
    '/pricing/' => '/our-services/',
    '/pricing' => '/our-services/',
    '/tax-services/' => '/our-services/',
    '/tax-services' => '/our-services/',
    '/payroll-services/' => '/our-services/',
    '/payroll-services' => '/our-services/',
    '/payroll/' => '/our-services/',
    '/payroll' => '/our-services/',
    '/bookkeeping/' => '/our-services/',
    '/bookkeeping' => '/our-services/',
);

// Build the redirect rules
$redirect_block = "# BEGIN ATP 301 REDIRECTS\n";
$redirect_block .= "<IfModule mod_rewrite.c>\n";
$redirect_block .= "RewriteEngine On\n";

// Add each redirect
$added = array();
foreach ($redirects as $old => $new) {
    // Avoid duplicates
    $key = $old . '>' . $new;
    if (isset($added[$key])) continue;
    $added[$key] = true;

    // Escape the old path for regex
    $old_escaped = str_replace('/', '\\/', ltrim($old, '/'));
    $redirect_block .= "RewriteRule ^{$old_escaped}$ {$new} [R=301,L]\n";
}

$redirect_block .= "</IfModule>\n";
$redirect_block .= "# END ATP 301 REDIRECTS\n\n";

// Check if redirects already exist
if (strpos($content, 'ATP 301 REDIRECTS') !== false) {
    // Replace existing block
    $content = preg_replace('/# BEGIN ATP 301 REDIRECTS.*?# END ATP 301 REDIRECTS\n*/s', $redirect_block, $content);
    echo "Updated existing redirect block.\n";
} else {
    // Insert before WordPress rules
    $content = str_replace('# BEGIN WordPress', $redirect_block . '# BEGIN WordPress', $content);
    echo "Added new redirect block before WordPress rules.\n";
}

// Write back
file_put_contents($htaccess, $content);
echo "Redirects saved to .htaccess\n\n";

// Show what was added
echo "=== REDIRECTS CONFIGURED ===\n";
$unique_redirects = array();
foreach ($redirects as $old => $new) {
    // Show unique old->new pairs (without trailing slash duplicates)
    $clean_old = rtrim($old, '/');
    if (!isset($unique_redirects[$clean_old])) {
        echo "$old => $new\n";
        $unique_redirects[$clean_old] = true;
    }
}

// Clear LiteSpeed cache
if (class_exists('LiteSpeed\Purge')) {
    LiteSpeed\Purge::purge_all();
    echo "\nLiteSpeed cache purged.\n";
}

echo "\nDone! Testing redirects...\n";
