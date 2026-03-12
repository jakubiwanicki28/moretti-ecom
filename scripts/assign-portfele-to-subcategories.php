<?php
/**
 * Jednorazowo przypisuje wszystkie produkty z kategorii "Portfele" do podkategorii
 * "Portfele męskie" i "Portfele damskie". Dzięki temu na stronach DLA NIEGO / DLA NIEJ
 * coś się wyświetli. Potem w WooCommerce możesz odznaczyć niepotrzebną podkategorię
 * (np. przy portfelach damskich odznacz "Portfele męskie").
 *
 * Uruchom raz z przeglądarki (zalogowany jako admin):
 *   https://twoja-domena.pl/wp-content/themes/NAZWA-MOTYWU/scripts/assign-portfele-to-subcategories.php
 * lub przez WP-CLI (w katalogu motywu): wp eval-file scripts/assign-portfele-to-subcategories.php
 */
if (!defined('ABSPATH')) {
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

$portfele       = get_term_by('slug', 'portfele', 'product_cat');
$portfele_meskie = get_term_by('slug', 'portfele-meskie', 'product_cat');
$portfele_damskie = get_term_by('slug', 'portfele-damskie', 'product_cat');

if (!$portfele || is_wp_error($portfele)) {
    exit('Brak kategorii "Portfele" (slug: portfele). Utwórz ją w WooCommerce → Produkty → Kategorie.');
}
if (!$portfele_meskie || is_wp_error($portfele_meskie)) {
    exit('Brak kategorii "Portfele męskie" (slug: portfele-meskie). Utwórz ją w WooCommerce.');
}
if (!$portfele_damskie || is_wp_error($portfele_damskie)) {
    exit('Brak kategorii "Portfele damskie" (slug: portfele-damskie). Utwórz ją w WooCommerce.');
}

$product_ids = get_posts(array(
    'post_type'      => 'product',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'fields'         => 'ids',
    'tax_query'      => array(
        array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => (int) $portfele->term_id,
        ),
    ),
));

$updated = 0;
foreach ($product_ids as $product_id) {
    $term_ids = wp_get_object_terms($product_id, 'product_cat');
    $current  = array();
    foreach ($term_ids as $t) {
        $current[] = (int) $t->term_id;
    }
    $add_meskie  = !in_array((int) $portfele_meskie->term_id, $current, true);
    $add_damskie = !in_array((int) $portfele_damskie->term_id, $current, true);
    if (!$add_meskie && !$add_damskie) {
        continue;
    }
    if ($add_meskie) {
        $current[] = (int) $portfele_meskie->term_id;
    }
    if ($add_damskie) {
        $current[] = (int) $portfele_damskie->term_id;
    }
    wp_set_object_terms($product_id, $current, 'product_cat');
    $updated++;
}

$msg = "Gotowe. Przypisano podkategorie do {$updated} produktów z kategorii Portfele.\n\n";
$msg .= "Na stronie „Portfele męskie” i „Portfele damskie” powinny się teraz wyświetlać produkty.\n";
$msg .= "Jeśli chcesz rozdzielić: w WooCommerce → Produkty odznacz przy każdym produkcie niepotrzebną podkategorię (np. przy damskich odznacz „Portfele męskie”).\n";

if (defined('WP_CLI') && WP_CLI && class_exists('WP_CLI')) {
    \WP_CLI::success(trim($msg));
} else {
    header('Content-Type: text/plain; charset=utf-8');
    echo $msg;
}
