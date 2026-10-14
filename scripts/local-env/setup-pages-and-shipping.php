<?php
defined('ABSPATH') || exit; // pliki trafiają na serwer razem z motywem — tylko przez wp-cli
/**
 * Część setup.sh wykonywana w WordPressie: strony koszyka/kasy z treścią bloków z produkcji
 * oraz strefa „Polska” z metodami dostawy w stanie produkcyjnym (fixtures/shipping-methods-prod-*.json).
 */

if (wp_get_environment_type() !== 'local') {
    WP_CLI::error('Tylko środowisko lokalne.');
}

$fixtures = '/local-env/fixtures';

foreach (['cart' => ['koszyk', 'Koszyk', 'cart-page.html'], 'checkout' => ['kasa', 'Kasa', 'checkout-page.html']] as $key => [$slug, $title, $file]) {
    $page_id = (int) get_option("woocommerce_{$key}_page_id");
    $data = [
        'post_type'    => 'page',
        'post_status'  => 'publish',
        'post_name'    => $slug,
        'post_title'   => $title,
        'post_content' => file_get_contents("$fixtures/$file"),
    ];
    if ($page_id && get_post($page_id)) {
        $data['ID'] = $page_id;
        wp_update_post($data);
    } else {
        $page_id = wp_insert_post($data);
        update_option("woocommerce_{$key}_page_id", $page_id);
    }
}

// Strony prawne tworzy motyw (moretti_ensure_legal_pages_exist); podpinamy regulamin i politykę prywatności.
foreach (['woocommerce_terms_page_id' => 'regulamin-sklepu', 'wp_page_for_privacy_policy' => 'polityka-prywatnosci'] as $option => $slug) {
    $page = get_page_by_path($slug);
    if ($page) {
        update_option($option, $page->ID);
    }
}

// Strefa „Polska” z 9 metodami jak na produkcji (stan „przed”).
$zone = null;
foreach (WC_Shipping_Zones::get_zones() as $z) {
    if ($z['zone_name'] === 'Polska') {
        $zone = new WC_Shipping_Zone($z['zone_id']);
    }
}
if (!$zone) {
    $zone = new WC_Shipping_Zone();
    $zone->set_zone_name('Polska');
    $zone->add_location('PL', 'country');
    $zone->save();

    $methods = json_decode(file_get_contents("$fixtures/shipping-methods-prod-2026-10-14.json"), true);
    foreach ($methods as $m) {
        $instance_id = $zone->add_shipping_method('flat_rate');
        $settings = array_merge(['tax_status' => 'none'], $m['settings']);
        update_option("woocommerce_flat_rate_{$instance_id}_settings", $settings);
        global $wpdb;
        $wpdb->update(
            "{$wpdb->prefix}woocommerce_shipping_zone_methods",
            ['method_order' => $m['order'], 'is_enabled' => $m['enabled'] ? 1 : 0],
            ['instance_id' => $instance_id]
        );
    }
    WP_CLI::log('Strefa Polska: ' . count($methods) . ' metod dostawy.');
}
