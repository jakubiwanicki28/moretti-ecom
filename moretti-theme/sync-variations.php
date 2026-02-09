<?php
/**
 * Sync all product variations
 * Run this once: http://localhost:8080/wp-content/themes/moretti-theme/sync-variations.php
 */

// Load WordPress
require_once('../../../wp-load.php');

if (!class_exists('WooCommerce')) {
    die('WooCommerce is not active!');
}

echo '<h1>Syncing Product Variations...</h1>';
echo '<pre>';

// Get all variable products
$args = array(
    'post_type' => 'product',
    'posts_per_page' => -1,
    'post_status' => 'publish',
);

$products = get_posts($args);
$synced_count = 0;

foreach ($products as $post) {
    $product = wc_get_product($post->ID);
    
    if ($product && $product->is_type('variable')) {
        echo "Syncing product: {$product->get_name()} (ID: {$post->ID})\n";
        
        // Get all variations
        $variations = $product->get_available_variations();
        echo "  - Found " . count($variations) . " variations\n";
        
        // Sync variation prices
        WC_Product_Variable::sync($post->ID);
        
        // Update product meta
        $product = wc_get_product($post->ID); // Reload
        $min_price = $product->get_variation_price('min');
        $max_price = $product->get_variation_price('max');
        
        echo "  - Price range: $" . $min_price . " - $" . $max_price . "\n";
        echo "  - Status: " . $product->get_stock_status() . "\n";
        
        $synced_count++;
    }
}

echo "\n✅ Successfully synced {$synced_count} variable products!\n";
echo '</pre>';

echo '<p><a href="' . home_url('/shop') . '">← Back to Shop</a></p>';
