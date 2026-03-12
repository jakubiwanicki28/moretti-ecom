<?php
/**
 * Przypisuje produkty do termów atrybutów Kolekcja i Materiał na podstawie CSV
 * (tego samego co migrate-categories-to-attributes-csv.py generuje).
 * Omija importer WooCommerce – bezpośrednio wp_set_object_terms().
 *
 * 1. Skopiuj plik moretti-migracja-kolekcja-material.csv do folderu scripts/ w motywie.
 * 2. Jako admin wejdź: ?moretti_assign_kolekcja_material=1
 *    lub WP-CLI: wp eval-file scripts/assign-kolekcja-material-from-csv.php
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

$csv_path = get_template_directory() . '/scripts/moretti-migracja-kolekcja-material.csv';
if (!is_file($csv_path) || !is_readable($csv_path)) {
    exit('Skopiuj plik moretti-migracja-kolekcja-material.csv do folderu: ' . get_template_directory() . '/scripts/');
}

$tax_kolekcja = taxonomy_exists('pa_kolekcja') ? 'pa_kolekcja' : '';
$tax_material = taxonomy_exists('pa_material') ? 'pa_material' : (taxonomy_exists('pa_materiał') ? 'pa_materiał' : '');
if (!$tax_kolekcja) {
    exit('Brak taksonomii pa_kolekcja. Utwórz atrybut Kolekcja w WooCommerce → Produkty → Atrybuty.');
}
if (!$tax_material) {
    exit('Brak taksonomii pa_material / pa_materiał. Utwórz atrybut Materiał.');
}

$handle = fopen($csv_path, 'r');
if (!$handle) {
    exit('Nie można otworzyć pliku CSV.');
}
$header = fgetcsv($handle, 0, ',');
if ($header === false) {
    fclose($handle);
    exit('Pusty lub nieprawidłowy CSV.');
}
$idx_id = array_search('Identyfikator', $header);
$idx_k = array_search('Wartości atrybutu 1', $header);
$idx_m = array_search('Wartości atrybutu 2', $header);
if ($idx_id === false || $idx_k === false || $idx_m === false) {
    fclose($handle);
    exit('Brak wymaganych kolumn: Identyfikator, Wartości atrybutu 1, Wartości atrybutu 2.');
}

$updated = 0;
$errors = array();
while (($row = fgetcsv($handle, 0, ',')) !== false) {
    if (count($row) <= max($idx_id, $idx_k, $idx_m)) {
        continue;
    }
    $pid = (int) trim($row[$idx_id]);
    $kolekcja = trim($row[$idx_k] ?? '');
    $material = trim($row[$idx_m] ?? '');
    if ($pid <= 0) {
        continue;
    }
    if (get_post_type($pid) !== 'product') {
        $errors[] = "ID {$pid} nie jest produktem.";
        continue;
    }

    $done = false;
    if ($kolekcja !== '') {
        $term = get_term_by('name', $kolekcja, $tax_kolekcja) ?: get_term_by('slug', sanitize_title($kolekcja), $tax_kolekcja);
        if ($term && !is_wp_error($term)) {
            wp_set_object_terms($pid, array((int) $term->term_id), $tax_kolekcja);
            $done = true;
        } else {
            $errors[] = "Produkt {$pid}: nie znaleziono termu Kolekcja '{$kolekcja}'.";
        }
    }
    if ($material !== '') {
        $term = get_term_by('name', $material, $tax_material) ?: get_term_by('slug', sanitize_title($material), $tax_material);
        if ($term && !is_wp_error($term)) {
            wp_set_object_terms($pid, array((int) $term->term_id), $tax_material);
            $done = true;
        } else {
            $errors[] = "Produkt {$pid}: nie znaleziono termu Materiał '{$material}'.";
        }
    }
    if ($done) {
        $updated++;
    }
}
fclose($handle);

// Odśwież liczniki termów
if (function_exists('wc_delete_product_transients')) {
    $product_ids = get_posts(array('post_type' => 'product', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids'));
    foreach ($product_ids as $id) {
        wc_delete_product_transients($id);
    }
}
wp_cache_flush();

if (defined('WP_CLI') && WP_CLI) {
    echo "Zaktualizowano {$updated} produktów.\n";
    if (!empty($errors)) {
        foreach (array_slice($errors, 0, 20) as $e) {
            echo "  " . $e . "\n";
        }
        if (count($errors) > 20) {
            echo "  ... i " . (count($errors) - 20) . " innych.\n";
        }
    }
} else {
    wp_safe_redirect(add_query_arg(array(
        'moretti_assign_done' => $updated,
        'moretti_assign_errors' => count($errors),
    ), remove_query_arg('moretti_assign_kolekcja_material')));
    exit;
}
