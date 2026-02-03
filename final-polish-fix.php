<?php
/**
 * ULTIMATE FIX: Set Polish Shop Page and Flush Everything
 * Run this: http://localhost:8080/wp-content/themes/moretti-theme/final-polish-fix.php
 */

// Load WordPress
require_once('../../../wp-load.php');

if (!is_admin()) {
    wp_set_current_user(1); // Force admin
}

echo '<h1>🛠️ Final Polish Shop Fix...</h1>';
echo '<pre>';

// 1. Znajdź stronę "Sklep"
$sklep_page = get_page_by_path('sklep');
if (!$sklep_page) {
    $sklep_page = get_page_by_title('Sklep');
}

if ($sklep_page) {
    // 2. Ustaw tę stronę jako oficjalny sklep WooCommerce
    update_option('woocommerce_shop_page_id', $sklep_page->ID);
    echo "✅ Ustawiono stronę 'Sklep' (ID: {$sklep_page->ID}) jako główny sklep.\n";
} else {
    // Jeśli nie ma takiej strony, stwórz ją
    $new_id = wp_insert_post(array(
        'post_title' => 'Sklep',
        'post_name' => 'sklep',
        'post_status' => 'publish',
        'post_type' => 'page'
    ));
    update_option('woocommerce_shop_page_id', $new_id);
    echo "✅ Stworzono nową stronę 'Sklep' (ID: $new_id) i ustawiono jako sklep.\n";
}

// 3. Ustawienia Permalinka
update_option('permalink_structure', '/%postname%/');
echo "✅ Odświeżono strukturę linków (/%postname%/).\n";

// 4. WYMUŚ PUBLIKACJĘ PRODUKTÓW
$products = wc_get_products(array('limit' => -1, 'status' => 'any'));
$count = 0;
foreach ($products as $product) {
    $product->set_status('publish');
    $product->save();
    $count++;
}
echo "✅ Upewniono się, że wszystkie produkty ($count) są opublikowane.\n";

// 5. CZYŚCIMY WSZYSTKIE CACHE ŚWIATA
flush_rewrite_rules(true);
wp_cache_flush();
if (class_exists('WC_Cache_Helper')) {
    WC_Cache_Helper::get_transient_version('product', true);
}
delete_transient('wc_products_onsale');
echo "✅ Wyczyszczono cache i transienty.\n";

echo "\n🚀 GOTOWE! Teraz sklep musi działać.\n";
echo '</pre>';

echo '<p><a href="' . home_url('/sklep') . '" style="padding: 12px 24px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;">🛍️ IDŹ DO SKLEPU</a></p>';
