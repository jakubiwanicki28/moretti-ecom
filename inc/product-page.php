<?php
/**
 * Strona produktu na telefonie (audyt 2026-11-12, pkt 6): cena i „Dodaj do koszyka” od pierwszego ekranu.
 *
 * - Okruszki bez nazwy produktu (tytuł jest tuż pod zdjęciem), na telefonie w jednej linii.
 * - Przyklejony dolny pasek „cena · Dodaj do koszyka”, widoczny, dopóki właściwy przycisk nie jest na ekranie.
 *   Kliknięcie uruchamia ten sam formularz co przycisk na stronie, więc okienko „Dodano do koszyka” działa jak dotąd.
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

add_filter('woocommerce_get_breadcrumb', function ($crumbs) {
    if (is_product() && count($crumbs) > 1) {
        array_pop($crumbs);
    }
    return $crumbs;
});

function moretti_product_page_assets() {
    if (!is_product()) {
        return;
    }
    $dir = get_template_directory();
    $uri = get_template_directory_uri();
    wp_enqueue_style('moretti-product-page', $uri . '/assets/css/product-page.css', ['moretti-main-style'], filemtime($dir . '/assets/css/product-page.css'));
    wp_enqueue_script('moretti-product-page', $uri . '/assets/js/product-page.js', [], filemtime($dir . '/assets/js/product-page.js'), true);
}
add_action('wp_enqueue_scripts', 'moretti_product_page_assets', 20);

/** Pasek zakupu (tylko telefon, tylko produkt do kupienia od ręki). */
add_action('wp_footer', function () {
    if (!is_product()) {
        return;
    }
    $product = wc_get_product(get_queried_object_id());
    if (!$product || !$product->is_purchasable() || !$product->is_in_stock() || !$product->is_type('simple')) {
        return;
    }
    ?>
    <div class="moretti-sticky-atc" id="moretti-sticky-atc" hidden>
        <div class="moretti-sticky-atc__price"><?php echo wp_kses_post($product->get_price_html()); ?></div>
        <button type="button" class="moretti-sticky-atc__button">Dodaj do koszyka</button>
    </div>
    <?php
}, 5); // przed skryptami stopki (wp_print_footer_scripts, priorytet 20)
