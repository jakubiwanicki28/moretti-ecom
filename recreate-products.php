<?php
/**
 * FORCE Recreate Products
 * Run this ONCE: http://localhost:8080/wp-content/themes/moretti-theme/recreate-products.php
 */

// Load WordPress
require_once('../../../wp-load.php');

if (!class_exists('WooCommerce')) {
    die('WooCommerce is not active!');
}

echo '<h1>🔄 Recreating Products...</h1>';
echo '<pre>';

// FORCE delete the flag so products can be created again
delete_option('moretti_sample_products_created');
delete_option('moretti_sample_product_ids');

echo "✅ Deleted creation flags\n";
echo "✅ Calling moretti_create_sample_products()...\n\n";

// Include functions.php to get the function
require_once(get_template_directory() . '/functions.php');

// Call the function directly
moretti_create_sample_products();

echo "\n✅ Products created!\n";
echo '</pre>';

// Count products
$products = wc_get_products(array('limit' => -1, 'status' => 'publish'));
echo '<p><strong>Total products in database: ' . count($products) . '</strong></p>';

if (count($products) > 0) {
    echo '<h2>Created Products:</h2>';
    echo '<ul>';
    foreach ($products as $product) {
        echo '<li><strong>' . $product->get_name() . '</strong> - ';
        if ($product->is_type('variable')) {
            $variations = $product->get_available_variations();
            echo count($variations) . ' variations - ';
            echo 'Price: $' . $product->get_variation_price('min');
            if ($product->get_variation_price('min') !== $product->get_variation_price('max')) {
                echo ' - $' . $product->get_variation_price('max');
            }
        } else {
            echo 'Price: ' . $product->get_price_html();
        }
        echo '</li>';
    }
    echo '</ul>';
}

echo '<p style="margin-top: 30px;">';
echo '<a href="' . home_url('/shop') . '" style="padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 4px;">✅ Go to Shop</a> ';
echo '<a href="flush-cache.php" style="padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin-left: 10px;">🗑️ Flush Cache</a>';
echo '</p>';
