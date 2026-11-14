<?php
defined('ABSPATH') || exit; // pliki trafiają na serwer razem z motywem — tylko przez wp-cli
/**
 * Kopiuje N losowych produktów z produkcji (publiczne Store API, tylko odczyt) do lokalnej bazy: ceny (z promocją),
 * opisy, kategorie z rodzicami, atrybuty (kolor, materiał, kolekcja) i do 4 zdjęć (główne = pierwsze w galerii,
 * zob. szablon karty produktu). Pomija produkty, których SKU już jest lokalnie.
 *
 *   docker compose exec cli wp eval-file /local-env/import-random-products.php 10
 *   docker compose exec cli wp eval-file /local-env/import-random-products.php 12 okazje   # tylko z tej kategorii
 */

if (wp_get_environment_type() !== 'local') {
    WP_CLI::error('Tylko środowisko lokalne.');
}

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$count    = max(1, (int) ($args[0] ?? 10));
$category = isset($args[1]) ? sanitize_title($args[1]) : '';
$store  = 'https://www.morettifashion.com/wp-json/wc/store/v1/products';
$images = 4;

// Wszystkie produkty do kupienia od ręki (Store API pokazuje tylko opublikowane).
$all = [];
for ($page = 1; $page <= 10; $page++) {
    $res   = wp_remote_get("$store?per_page=100&page=$page" . ($category ? "&category=$category" : ''), ['timeout' => 60]);
    $items = json_decode(wp_remote_retrieve_body($res), true) ?: [];
    $all   = array_merge($all, $items);
    if (count($items) < 100) {
        break;
    }
}
$candidates = array_values(array_filter($all, fn($p) => $p['is_purchasable'] && $p['is_in_stock'] && $p['type'] === 'simple'
    && !wc_get_product_id_by_sku($p['sku'] ?: 'prod-' . $p['id'])));
shuffle($candidates);
WP_CLI::log(sprintf('Produkcja%s: %d produktów, do wyboru %d, importuję %d.', $category ? " (kategoria $category)" : '', count($all), count($candidates), min($count, count($candidates))));

/** Kategoria po slugu; rodzic z adresu kategorii (…/kategoria-produktu/rodzic/dziecko/). */
function moretti_local_category(array $cat): int {
    $path   = array_values(array_filter(explode('/', (string) parse_url($cat['link'], PHP_URL_PATH))));
    $parent = 0;
    $slugs  = array_slice($path, 1); // bez „kategoria-produktu”
    foreach ($slugs as $i => $slug) {
        $term = get_term_by('slug', $slug, 'product_cat');
        if (!$term) {
            $name    = $i === count($slugs) - 1 ? html_entity_decode($cat['name']) : ucfirst(str_replace('-', ' ', $slug));
            $created = wp_insert_term($name, 'product_cat', ['slug' => $slug, 'parent' => $parent]);
            $parent  = is_wp_error($created) ? 0 : (int) $created['term_id'];
        } else {
            $parent = (int) $term->term_id;
        }
    }
    return $parent;
}

/** Atrybut globalny (pa_*) z terminem; tworzy brakujący atrybut i termin. */
function moretti_local_attribute(array $attr): ?WC_Product_Attribute {
    $taxonomy = $attr['taxonomy'];
    $name     = substr($taxonomy, 3);
    if (!taxonomy_exists($taxonomy)) {
        wc_create_attribute(['name' => $attr['name'], 'slug' => $name]);
        register_taxonomy($taxonomy, 'product', ['hierarchical' => false]);
    }
    $ids = [];
    foreach ($attr['terms'] as $t) {
        $term = get_term_by('slug', $t['slug'], $taxonomy) ?: wp_insert_term(html_entity_decode($t['name']), $taxonomy, ['slug' => $t['slug']]);
        $ids[] = is_array($term) ? (int) $term['term_id'] : (int) $term->term_id;
    }
    $a = new WC_Product_Attribute();
    $a->set_id(wc_attribute_taxonomy_id_by_name($taxonomy));
    $a->set_name($taxonomy);
    $a->set_options(array_filter($ids));
    $a->set_visible(true);
    return $a;
}

foreach (array_slice($candidates, 0, $count) as $p) {
    $sku     = $p['sku'] ?: 'prod-' . $p['id'];
    $product = new WC_Product_Simple();
    $product->set_name(html_entity_decode($p['name']));
    $product->set_slug($p['slug']);
    $product->set_sku($sku);
    $product->set_regular_price(wc_format_decimal($p['prices']['regular_price'] / 100));
    if ($p['on_sale'] && $p['prices']['sale_price'] !== $p['prices']['regular_price']) {
        $product->set_sale_price(wc_format_decimal($p['prices']['sale_price'] / 100));
    }
    $product->set_short_description($p['short_description']);
    $product->set_description($p['description']);
    $product->set_status('publish');
    $product->set_manage_stock(false);
    $product->set_category_ids(array_filter(array_map('moretti_local_category', $p['categories'])));
    $product->set_attributes(array_filter(array_map('moretti_local_attribute', $p['attributes'])));
    $id = $product->save();

    $gallery = [];
    foreach (array_slice($p['images'], 0, $images) as $img) {
        $att = media_sideload_image($img['src'], $id, $p['name'], 'id');
        if (!is_wp_error($att)) {
            $gallery[] = (int) $att;
        }
    }
    if ($gallery) {
        $product->set_image_id($gallery[0]);
        $product->set_gallery_image_ids($gallery);
        $product->save();
    }

    WP_CLI::log(sprintf('+ %s (%s) — %s zł%s, zdjęć %d, %s',
        $product->get_name(), $sku, $product->get_regular_price(),
        $product->get_sale_price() ? ' → ' . $product->get_sale_price() . ' zł' : '',
        count($gallery), get_permalink($id)));
}
