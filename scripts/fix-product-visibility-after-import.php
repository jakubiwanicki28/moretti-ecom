<?php
/**
 * Jednorazowa naprawa po imporcie CSV migracji.
 * Importer WooCommerce przy aktualizacji produktów z CSV bez kolumn
 * "Opublikowano" / "Widoczność w katalogu" może ustawić status na "Szkic"
 * lub widoczność na "ukryty". Ten skrypt przywraca wszystkie produkty
 * do Opublikowane + Widoczny w katalogu.
 *
 * Uruchom raz z przeglądarki (zalogowany jako admin):
 *   https://twoja-domena.pl/wp-content/themes/moretti-theme/scripts/fix-product-visibility-after-import.php
 * lub przez WP-CLI: wp eval-file scripts/fix-product-visibility-after-import.php
 */
if (!defined('ABSPATH')) {
    // Z przeglądarki: skrypt jest w .../wp-content/themes/moretti-theme/scripts/ → wp-load w .../ (root)
    $wp_load = dirname(__DIR__, 4) . '/wp-load.php';
    if (!is_file($wp_load)) {
        $wp_load = dirname(__DIR__, 3) . '/wp-load.php';
    }
    require_once $wp_load;
}

if (!current_user_can('manage_woocommerce') && !defined('WP_CLI')) {
    status_header(403);
    exit('Brak uprawnień.');
}

if (!class_exists('WooCommerce')) {
    exit('WooCommerce nie jest aktywny.');
}

$updated = 0;
$ids = get_posts(array(
    'post_type'      => 'product',
    'post_status'    => 'any',
    'posts_per_page' => -1,
    'fields'         => 'ids',
));

foreach ($ids as $product_id) {
    $post = get_post($product_id);
    $product = wc_get_product($product_id);
    $changed = false;

    if ($post->post_status !== 'publish') {
        wp_update_post(array('ID' => $product_id, 'post_status' => 'publish'));
        $changed = true;
    }

    if ($product && $product->get_catalog_visibility() !== 'visible') {
        $product->set_catalog_visibility('visible');
        $product->save();
        $changed = true;
    }

    if ($changed) {
        $updated++;
    }
}

if (defined('WP_CLI') && WP_CLI && class_exists('WP_CLI')) {
    \WP_CLI::success("Zaktualizowano {$updated} produktów (status = Opublikowany, widoczność = visible).");
} else {
    header('Content-Type: text/plain; charset=utf-8');
    echo "Zaktualizowano {$updated} produktów.\n";
    echo "Wszystkie produkty mają teraz status Opublikowany i widoczność w katalogu.\n";
    echo "\nMożesz zamknąć tę kartę i odświeżyć stronę sklepu.\n";
}
