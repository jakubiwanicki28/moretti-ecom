<?php
/**
 * Flush WordPress cache and rewrite rules
 * Run this to clear all caches: http://localhost:8080/wp-content/themes/moretti-theme/flush-cache.php
 */

// Load WordPress
require_once('../../../wp-load.php');

// Flush rewrite rules
flush_rewrite_rules(true);

// Clear all transients
global $wpdb;
$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_%'");
$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_site_transient_%'");

// Clear WooCommerce transients
if (class_exists('WC_Cache_Helper')) {
    WC_Cache_Helper::get_transient_version('shipping', true);
    WC_Cache_Helper::get_transient_version('product', true);
}

// Clear object cache
wp_cache_flush();

// Force WooCommerce to re-check template versions
delete_transient('wc_template_version');

// Clear WooCommerce system status tool cache
delete_transient('wc_system_status');

// Force theme to re-register
delete_option('_site_transient_theme_roots');

// Clear all caches
if (function_exists('wp_cache_clear_cache')) {
    wp_cache_clear_cache();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Cache Flushed</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            line-height: 1.6;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background: #333;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 10px;
            margin-right: 10px;
        }
        .button:hover {
            background: #555;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        ul {
            margin: 15px 0;
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <div class="success">
        <h1>✅ Cache Flushed!</h1>
        <p><strong>Cleared:</strong></p>
        <ul>
            <li>Rewrite rules flushed</li>
            <li>Transients cleared</li>
            <li>WooCommerce caches cleared</li>
            <li>Object cache flushed</li>
            <li>Template version cache cleared</li>
            <li>Theme cache cleared</li>
        </ul>
    </div>
    
    <p><strong>Now hard refresh (Cmd+Shift+R) the shop page!</strong></p>
    
    <a href="<?php echo home_url('/shop'); ?>" class="button">← Back to Shop</a>
    <a href="<?php echo home_url(); ?>" class="button">← Back to Home</a>
</body>
</html>
