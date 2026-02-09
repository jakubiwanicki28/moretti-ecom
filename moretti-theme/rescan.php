<?php
/**
 * THEME RE-SCAN
 * Run this: http://localhost:8080/wp-content/themes/moretti-theme/rescan.php
 */

// Load WordPress
require_once('../../../wp-load.php');

if (!is_admin()) {
    wp_set_current_user(1);
}

echo '<h1>🔄 Theme Re-Scan</h1>';
echo '<pre>';

// 1. Switch to a default theme temporarily (if exists)
$current_theme = get_option('template');
$themes = wp_get_themes();
$temp_theme = '';
foreach ($themes as $slug => $theme) {
    if ($slug !== 'moretti-theme' && $slug !== 'moretti') {
        $temp_theme = $slug;
        break;
    }
}

if ($temp_theme) {
    switch_theme($temp_theme);
    echo "✅ Switched to $temp_theme\n";
    
    // Switch back
    switch_theme($current_theme);
    echo "✅ Switched back to $current_theme\n";
} else {
    echo "⚠️ No other theme found to switch to. Just flushing...\n";
    // Just force update
    update_option('template', $current_theme);
}

// 2. Clear WooCommerce template cache
delete_transient('wc_template_version');
echo "✅ Cleared template versions\n";

// 3. Flush rewrite rules
flush_rewrite_rules(true);
echo "✅ Flushed rewrite rules\n";

echo "\n🚀 Done! Try /sklep/ now.\n";
echo '</pre>';
?>
