<?php
/**
 * Skrypt diagnostyczny: wyświetla slugi termów dla atrybutów i kategorii używanych w CTA banerów hero.
 * Uruchom w przeglądarce (jako admin): ?moretti_hero_slugs=1
 * lub przez WP-CLI: wp eval-file scripts/hero-banner-cta-slugs.php
 *
 * Skopiuj podane slugi do hero-banners-config.php (cta_filters / cta_category_slug).
 */

if (!defined('ABSPATH')) {
    if (php_sapi_name() === 'cli') {
        $wp_load = dirname(__DIR__, 2) . '/wp-load.php';
        if (is_readable($wp_load)) {
            require_once $wp_load;
        }
    }
    if (!defined('ABSPATH')) {
        die('Uruchom w kontekście WordPress (np. ?moretti_hero_slugs=1 na stronie jako admin).');
    }
}

$taxonomies = array(
    'pa_kolekcja'  => 'Kolekcja (filter_kolekcja)',
    'pa_material'  => 'Materiał (filter_material)',
    'pa_materiał'  => 'Materiał [pa_materiał] (filter_material)',
    'product_cat'  => 'Kategorie (cta_category_slug)',
);

echo "=== Slugi do hero-banners-config.php ===\n\n";

foreach ($taxonomies as $tax => $label) {
    if (!taxonomy_exists($tax)) {
        echo "[{$label}] Taksonomia {$tax} nie istnieje – pomijam.\n";
        continue;
    }

    $terms = get_terms(array(
        'taxonomy'   => $tax,
        'hide_empty' => false,
        'orderby'    => 'name',
    ));

    if (is_wp_error($terms) || empty($terms)) {
        echo "[{$label}] Brak termów.\n";
        continue;
    }

    echo "[{$label}]\n";
    foreach ($terms as $t) {
        $count = isset($t->count) ? (int) $t->count : 0;
        echo "  nazwa: " . $t->name . "  →  slug: \"" . $t->slug . "\"  (produktów: {$count})\n";
    }
    echo "\n";
}

// Slugów używanych w configu nie trzeba zmieniać w WP – wystarczy wpisać w config dokładnie to, co wyświetlił skrypt.
echo "W configu używaj wartości z kolumny 'slug'. Jeśli 'produktów: 0', filtr będzie pusty dopóki nie przypiszesz atrybutu do produktów w WordPressie.\n";
