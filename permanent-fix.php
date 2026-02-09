<?php
/**
 * PERMANENT SHOP FIX
 * Run this: http://localhost:8080/wp-content/themes/moretti-theme/permanent-fix.php
 */

// Load WordPress
require_once('../../../wp-load.php');

if (!is_admin()) {
    wp_set_current_user(1);
}

echo '<h1>🚀 PERMANENT SHOP FIX</h1>';
echo '<pre>';

// 1. Force delete all possible conflicting options
delete_option('woocommerce_shop_page_display');
delete_option('woocommerce_category_archive_display');
echo "✅ Reset display settings\n";

// 2. Ensure "Sklep" is the ONLY shop page
$pages = get_posts(array('post_type' => 'page', 'post_status' => 'any', 'posts_per_page' => -1));
foreach ($pages as $p) {
    if ($p->post_name === 'shop' || $p->post_title === 'Shop') {
        wp_delete_post($p->ID, true);
        echo "✅ Deleted old 'Shop' page (ID: {$p->ID})\n";
    }
}

$sklep = get_page_by_path('sklep');
if (!$sklep) {
    $sklep_id = wp_insert_post(array(
        'post_title' => 'Sklep',
        'post_name' => 'sklep',
        'post_status' => 'publish',
        'post_type' => 'page'
    ));
} else {
    $sklep_id = $sklep->ID;
    wp_update_post(array('ID' => $sklep_id, 'post_status' => 'publish'));
}

update_option('woocommerce_shop_page_id', $sklep_id);
echo "✅ Confirmed 'Sklep' (ID: $sklep_id) is the Shop Page\n";

// 3. Fix Product Visibility
global $wpdb;
$wpdb->query("UPDATE {$wpdb->posts} SET post_status = 'publish' WHERE post_type = 'product'");
echo "✅ Forced all products to 'publish' status\n";

// 4. Reset Permalinks
update_option('permalink_structure', '/%postname%/');
flush_rewrite_rules(true);
echo "✅ Flushed rewrite rules\n";

// 5. Test Query
$q = new WP_Query(array('post_type' => 'product', 'posts_per_page' => 1));
echo "✅ Test query found: " . $q->found_posts . " products\n";

echo "\n🔥 NOW HARD REFRESH: " . home_url('/sklep/') . "\n";
echo '</pre>';
?>
