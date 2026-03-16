<?php
/**
 * FORCE TEMPLATE ASSIGNMENT
 * Run this: http://localhost:8080/wp-content/themes/moretti-theme/assign-template.php
 */

// Load WordPress
require_once('../../../wp-load.php');

if (!is_admin()) {
    wp_set_current_user(1);
}

echo '<h1>🎯 Assigning Template...</h1>';
echo '<pre>';

$sklep = get_page_by_path('sklep');
if ($sklep) {
    update_post_meta($sklep->ID, '_wp_page_template', 'page-sklep.php');
    echo "✅ Assigned 'Sklep Template' to page ID: {$sklep->ID}\n";
    
    // Also make sure WooCommerce DOES NOT think this is the shop page 
    // because WooCommerce takes over the shop page and ignores templates.
    // If we unset it, WordPress uses the page template!
    update_option('woocommerce_shop_page_id', 0); 
    echo "✅ Unset WooCommerce Shop Page ID (This allows our template to work)\n";
} else {
    echo "❌ Page 'sklep' not found!\n";
}

flush_rewrite_rules(true);
echo "✅ Flushed rewrite rules\n";

echo "\n🚀 Done! Go to /sklep/ now.\n";
echo '</pre>';
?>
