<?php
defined('ABSPATH') || exit; // pliki trafiają na serwer razem z motywem — tylko przez wp-cli
/**
 * Kopiuje kilkanaście opublikowanych produktów z produkcji (publiczne Store API) do lokalnej bazy,
 * razem z pierwszym zdjęciem. Idempotentne — produkt z tym samym SKU jest pomijany.
 *
 *   docker compose exec cli wp eval-file /local-env/import-products.php
 */

if (wp_get_environment_type() !== 'local') {
    WP_CLI::error('Tylko środowisko lokalne.');
}

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$store = 'https://www.morettifashion.com/wp-json/wc/store/v1/products';
$queries = [
    '?per_page=8&orderby=date',
    '?search=Street', '?search=Jamnik', '?search=Milano', '?search=Animals', '?search=Klasyczny',
];

$seen = [];
foreach ($queries as $q) {
    $res = wp_remote_get($store . $q, ['timeout' => 30]);
    $items = json_decode(wp_remote_retrieve_body($res), true) ?: [];

    foreach (array_slice($items, 0, 3) as $p) {
        $sku = $p['sku'] ?: 'prod-' . $p['id'];
        if (isset($seen[$sku]) || wc_get_product_id_by_sku($sku)) {
            continue;
        }
        $seen[$sku] = true;

        $product = new WC_Product_Simple();
        $product->set_name(html_entity_decode($p['name']));
        $product->set_sku($sku);
        $product->set_regular_price(wc_format_decimal($p['prices']['regular_price'] / 100));
        if ($p['prices']['sale_price'] !== $p['prices']['regular_price']) {
            $product->set_sale_price(wc_format_decimal($p['prices']['sale_price'] / 100));
        }
        $product->set_short_description($p['short_description']);
        $product->set_description($p['description']);
        $product->set_status('publish');
        $product->set_manage_stock(false);
        $id = $product->save();

        if (!empty($p['images'][0]['src'])) {
            $img = media_sideload_image($p['images'][0]['src'], $id, $p['name'], 'id');
            if (!is_wp_error($img)) {
                $product->set_image_id($img);
                $product->save();
            }
        }

        WP_CLI::log("+ {$p['name']} ({$sku})");
    }
}

WP_CLI::success(count($seen) . ' produktów zaimportowanych.');
