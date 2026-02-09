<?php
/**
 * Force Translate Pages and Menu to Polish
 * Run this: http://localhost:8080/wp-content/themes/moretti-theme/translate-polish.php
 */

// Load WordPress
require_once('../../../wp-load.php');

if (!is_admin()) {
    wp_set_current_user(1);
}

echo '<h1>🇵🇱 Translating to Polish...</h1>';
echo '<pre>';

// Delete the flag to allow re-creation
delete_option('moretti_pages_created');
echo "✅ Reset creation flag\n";

// Re-run the creation function
require_once(get_template_directory() . '/functions.php');
moretti_create_default_pages();
echo "✅ Re-created pages and menu in Polish\n";

echo "\n✅ Done! Navigation is now in Polish.\n";
echo '</pre>';

echo '<p><a href="' . home_url('/sklep') . '" style="padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 4px;">🛍️ Go to Shop</a></p>';
