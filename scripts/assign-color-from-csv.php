<?php
/**
 * Przypisuje produktom TYLKO atrybut Kolor (pa_kolor) na podstawie CSV.
 * Nie używa importera WooCommerce – nie nadpisuje innych atrybutów (Materiał, Kolekcja itd.).
 *
 * Uruchomienie:
 *   1. Skopiuj import-id-kolor.csv do katalogu głównego motywu (tam gdzie jest functions.php).
 *   2. W przeglądarce (zalogowany jako admin): https://twoja-domena.pl/?moretti_assign_color=1
 *      lub WP-CLI: wp eval-file scripts/assign-color-from-csv.php
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

$csv_path = dirname(__DIR__) . '/import-id-kolor.csv';
if (!is_file($csv_path) || !is_readable($csv_path)) {
    exit('Brak pliku: ' . $csv_path . ' – skopiuj import-id-kolor.csv do katalogu głównego motywu.');
}

$color_taxonomy = function_exists('moretti_resolve_attribute_taxonomy')
    ? moretti_resolve_attribute_taxonomy(array('pa_color', 'pa_kolor', 'pa_colour'), '', 'color')
    : (taxonomy_exists('pa_kolor') ? 'pa_kolor' : (taxonomy_exists('pa_color') ? 'pa_color' : ''));
if (!$color_taxonomy || !taxonomy_exists($color_taxonomy)) {
    exit('Brak taksonomii koloru (pa_kolor / pa_color). Utwórz atrybut Kolor w WooCommerce → Produkty → Atrybuty.');
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
$header = array_map('trim', $header);
$idx_id = array_search('Identyfikator', $header);
if ($idx_id === false) {
    $idx_id = array_search('ID', $header);
}
$idx_color = array_search('Wartości atrybutu 1', $header);
if ($idx_color === false) {
    $idx_color = array_search('Kolor', $header);
}
if ($idx_id === false || $idx_color === false) {
    fclose($handle);
    exit('Brak wymaganych kolumn: Identyfikator (lub ID) oraz Wartości atrybutu 1 (lub Kolor). Kolumny: ' . implode(', ', $header));
}

$updated = 0;
$skipped_empty = 0;
$errors = array();
while (($row = fgetcsv($handle, 0, ',')) !== false) {
    if (count($row) <= max($idx_id, $idx_color)) {
        continue;
    }
    $pid = (int) trim($row[$idx_id]);
    $color_slug = trim($row[$idx_color] ?? '');
    if ($pid <= 0) {
        continue;
    }
    if (get_post_type($pid) !== 'product') {
        $errors[] = "ID {$pid} nie jest produktem.";
        continue;
    }
    if ($color_slug === '') {
        $skipped_empty++;
        continue;
    }

    $term = get_term_by('slug', $color_slug, $color_taxonomy) ?: get_term_by('name', $color_slug, $color_taxonomy);
    if (!$term || is_wp_error($term)) {
        $errors[] = "Produkt {$pid}: nie znaleziono termu koloru '{$color_slug}' w {$color_taxonomy}.";
        continue;
    }

    wp_set_object_terms($pid, array((int) $term->term_id), $color_taxonomy);
    $updated++;
}

fclose($handle);

if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::success("Zaktualizowano kolor dla {$updated} produktów.");
    if ($skipped_empty > 0) {
        WP_CLI::log("Pominięto (pusty kolor): {$skipped_empty} wierszy.");
    }
    if (!empty($errors)) {
        foreach ($errors as $e) {
            WP_CLI::warning($e);
        }
    }
} else {
    wp_safe_redirect(add_query_arg(array(
        'moretti_assign_color_done' => $updated,
        'moretti_assign_color_errors' => count($errors),
    ), remove_query_arg('moretti_assign_color')));
    exit;
}
