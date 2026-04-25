<?php
/**
 * One-time script: assign pa_wielkosc (Wielkość) attribute to Animals & Crown products.
 * Run via: /wp-admin/ → visit any page after adding: add_action('init', ...)
 * Or simply visit: https://morettifashion.com/wp-content/themes/moretti-theme/update-wielkosc.php
 *
 * Actually — safest way: put in functions.php behind a ?run_wielkosc=1 flag.
 */

// Don't run directly — this is meant to be included via functions.php
if (!defined('ABSPATH')) {
    exit;
}

function moretti_update_wielkosc() {
    if (!isset($_GET['run_wielkosc']) || $_GET['run_wielkosc'] !== '1') {
        return;
    }
    if (!current_user_can('manage_options')) {
        wp_die('Brak uprawnień.');
    }

    // Model → Wielkość mapping (from dimensions spreadsheet)
    $size_map = array(
        '3886'  => 'Duży',
        '9992'  => 'Duży',
        '72062' => 'Duży',
        '9813'  => 'Duży',
        '3992'  => 'Duży',
        '3907'  => 'Średni',
        '9930'  => 'Średni',
        '9103'  => 'Średni',
        '017'   => 'Mały',
        '8003'  => 'Mały',
    );

    // Prefixes to process (Animals + Crown only)
    $prefixes = array('WA_', 'WS_', 'KR_');

    $taxonomy = 'pa_wielkosc';

    // Make sure taxonomy terms exist
    foreach (array('Duży', 'Średni', 'Mały') as $term_name) {
        if (!term_exists($term_name, $taxonomy)) {
            wp_insert_term($term_name, $taxonomy, array('slug' => sanitize_title($term_name)));
        }
    }

    // Get all products
    $products = wc_get_products(array(
        'limit' => -1,
        'status' => 'publish',
        'return' => 'objects',
    ));

    $updated = 0;
    $skipped = 0;
    $log = array();

    foreach ($products as $product) {
        $sku = $product->get_sku();
        if (empty($sku)) {
            continue;
        }

        // Check if this product matches our prefixes
        $matched_prefix = '';
        foreach ($prefixes as $pfx) {
            if (strpos($sku, $pfx) === 0) {
                $matched_prefix = $pfx;
                break;
            }
        }
        if (empty($matched_prefix)) {
            continue;
        }

        // Extract model number from SKU (PREFIX_MODEL-SERIA-kolor)
        $rest = substr($sku, strlen($matched_prefix));
        $parts = explode('-', $rest);
        $model = rtrim($parts[0], '.');

        if (!isset($size_map[$model])) {
            $log[] = "SKIP (no size mapping): ID={$product->get_id()} SKU={$sku} model={$model}";
            $skipped++;
            continue;
        }

        $size_value = $size_map[$model];

        // Get existing attributes
        $attributes = $product->get_attributes();

        // Create new Wielkość attribute
        $attribute = new WC_Product_Attribute();
        $attribute->set_name($taxonomy);
        $attribute->set_options(array($size_value));
        $attribute->set_visible(true);
        $attribute->set_variation(false);

        // Add/replace Wielkość in existing attributes
        $attributes[$taxonomy] = $attribute;
        $product->set_attributes($attributes);
        $product->save();

        $log[] = "OK: ID={$product->get_id()} SKU={$sku} → {$size_value}";
        $updated++;
    }

    // Output results
    header('Content-Type: text/plain; charset=utf-8');
    echo "=== MORETTI: Update Wielkość ===\n\n";
    echo "Updated: {$updated}\n";
    echo "Skipped: {$skipped}\n\n";
    foreach ($log as $line) {
        echo $line . "\n";
    }
    exit;
}
add_action('init', 'moretti_update_wielkosc');
