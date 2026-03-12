<?php
/**
 * Przypisuje kategorię "Portfele męskie" (i rodzica "Portfele") do produktów,
 * które w migracji CSV miały "Portfele, Portfele męskie".
 * Użyj, jeśli po imporcie CSV strona /kategoria-produktu/portfele/portfele-meskie/ pokazuje 0 produktów.
 *
 * Jako admin: ?moretti_restore_portfele_meskie=1
 * Lub WP-CLI: wp eval-file scripts/restore-portfele-meskie.php
 */
if (!defined('ABSPATH')) {
    require_once dirname(__DIR__, 3) . '/wp-load.php';
}
if (!current_user_can('manage_woocommerce') && !defined('WP_CLI')) {
    status_header(403);
    exit('Brak uprawnień.');
}

$portfele = get_term_by('slug', 'portfele', 'product_cat');
$portfele_meskie = get_term_by('slug', 'portfele-meskie', 'product_cat');
if (!$portfele_meskie || is_wp_error($portfele_meskie)) {
    exit('Brak kategorii Portfele męskie (slug: portfele-meskie). Utwórz ją w WooCommerce.');
}

// ID produktów, które w CSV migracji miały "Portfele, Portfele męskie" (z moretti-migracja-kolekcja-material.csv)
$product_ids = array(997, 998, 999, 1000, 1001, 1002, 1003, 1004, 1005, 1006, 1007);

$updated = 0;
foreach ($product_ids as $pid) {
    if (get_post_type($pid) !== 'product') {
        continue;
    }
    $current = wp_get_object_terms($pid, 'product_cat');
    $term_ids = array();
    if (!is_wp_error($current)) {
        $term_ids = array_map(function ($t) {
            return (int) $t->term_id;
        }, $current);
    }
    if (!in_array($portfele_meskie->term_id, $term_ids, true)) {
        $term_ids[] = $portfele_meskie->term_id;
    }
    if ($portfele && !is_wp_error($portfele) && !in_array($portfele->term_id, $term_ids, true)) {
        $term_ids[] = $portfele->term_id;
    }
    if (!empty($term_ids)) {
        wp_set_object_terms($pid, $term_ids, 'product_cat');
        $updated++;
    }
}

if (function_exists('wc_delete_product_transients')) {
    foreach ($product_ids as $id) {
        wc_delete_product_transients($id);
    }
}

if (defined('WP_CLI') && WP_CLI) {
    echo "Przypisano Portfele męskie do {$updated} produktów (ID: " . implode(', ', $product_ids) . ").\n";
} else {
    wp_safe_redirect(add_query_arg('moretti_restore_meskie_done', $updated, remove_query_arg('moretti_restore_portfele_meskie')));
    exit;
}
