<?php
/**
 * THE PROPER WAY: Configure WooCommerce Settings
 * Run this: http://localhost:8080/wp-content/themes/moretti-theme/proper-config.php
 */

// Load WordPress
require_once('../../../wp-load.php');

if (!is_admin()) {
    wp_set_current_user(1);
}

echo '<h1>⚙️ Proper WooCommerce Configuration</h1>';
echo '<pre>';

// 1. Znajdź lub stwórz stronę Sklep
$sklep = get_page_by_path('sklep');
if (!$sklep) {
    $sklep_id = wp_insert_post(array(
        'post_title' => 'Sklep',
        'post_name' => 'sklep',
        'post_status' => 'publish',
        'post_type' => 'page'
    ));
    echo "✅ Stworzono stronę 'Sklep'\n";
} else {
    $sklep_id = $sklep->ID;
    echo "✅ Znaleziono stronę 'Sklep' (ID: $sklep_id)\n";
}

// 2. POWIEDZ WOOCOMMERCE: To jest Twój sklep!
update_option('woocommerce_shop_page_id', $sklep_id);
echo "✅ Ustawiono 'Sklep' jako oficjalną stronę sklepu w ustawieniach WooCommerce.\n";

// 3. Resetuj szablon strony (ma być "default")
update_post_meta($sklep_id, '_wp_page_template', 'default');
echo "✅ Przywrócono domyślny szablon dla strony sklepu (żeby WC przejął kontrolę).\n";

// 4. Permalinki
update_option('permalink_structure', '/%postname%/');
flush_rewrite_rules(true);
echo "✅ Odświeżono Permalinki.\n";

// 5. Wyczyść stare śmieci
delete_transient('wc_template_version');
wp_cache_flush();
echo "✅ Wyczyszczono cache.\n";

echo "\n🚀 GOTOWE! Teraz idź na /sklep/ - WooCommerce sam go wyrenderuje używając Twoich stylów.\n";
echo '</pre>';
?>
