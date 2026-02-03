<?php
/**
 * DEEP DEBUG: Why are products missing from the shop loop?
 * Run this: http://localhost:8080/wp-content/themes/moretti-theme/deep-debug.php
 */

// Load WordPress
require_once('../../../wp-load.php');

if (!is_admin()) {
    wp_set_current_user(1);
}

echo '<h1>🔍 DEEP DEBUG: Missing Products</h1>';
echo '<pre>';

// 1. Check Product Visibility & Stock Settings
echo "<h3>1. WooCommerce Global Settings</h3>";
echo "Hide Out of Stock: " . get_option('woocommerce_hide_out_of_stock_items') . "\n";
echo "Catalog Visibility (default): " . get_option('woocommerce_catalog_columns') . "\n";
echo "Shop Page ID: " . wc_get_page_id('shop') . " (URL: " . get_permalink(wc_get_page_id('shop')) . ")\n";

// 2. Check Individual Product Data
echo "<h3>2. Sample Product Check</h3>";
$products = wc_get_products(array('limit' => 5));
if (empty($products)) {
    echo "❌ NO PRODUCTS FOUND VIA wc_get_products()!\n";
} else {
    foreach ($products as $p) {
        echo "Product: {$p->get_name()} (ID: {$p->get_id()})\n";
        echo "  - Status: " . $p->get_status() . "\n";
        echo "  - Catalog Visibility: " . $p->get_catalog_visibility() . "\n";
        echo "  - Stock Status: " . $p->get_stock_status() . "\n";
        echo "  - Price: " . $p->get_price() . "\n";
        echo "  - Is Visible: " . ($p->is_visible() ? '✅ YES' : '❌ NO') . "\n";
        echo "  - Is Purchasable: " . ($p->is_purchasable() ? '✅ YES' : '❌ NO') . "\n";
    }
}

// 3. Test The Main Query (Simulated)
echo "<h3>3. Main Query Simulation</h3>";
$args = array(
    'post_type' => 'product',
    'post_status' => 'publish',
    'posts_per_page' => 12,
);

// Apply WooCommerce visibility filters manually to see what happens
$args['tax_query'][] = array(
    'taxonomy' => 'product_visibility',
    'field'    => 'name',
    'terms'    => 'exclude-from-catalog',
    'operator' => 'NOT IN',
);

$query = new WP_Query($args);
echo "Query found: " . $query->found_posts . " products\n";
echo "Last SQL: " . $query->request . "\n";

if ($query->have_posts()) {
    echo "✅ Query works! SQL should return products.\n";
} else {
    echo "❌ Query FAILED to find products.\n";
}

// 4. Check for conflicting filters in functions.php
echo "<h3>4. Conflict Check</h3>";
$hooks = array('pre_get_posts', 'woocommerce_product_query');
foreach ($hooks as $hook) {
    echo "Hook: $hook\n";
    global $wp_filter;
    if (isset($wp_filter[$hook])) {
        foreach ($wp_filter[$hook]->callbacks as $priority => $callbacks) {
            foreach ($callbacks as $cb) {
                $name = is_string($cb['function']) ? $cb['function'] : (is_array($cb['function']) ? get_class($cb['function'][0]) . '::' . $cb['function'][1] : 'Closure');
                echo "  - [$priority] $name\n";
            }
        }
    }
}

echo '</pre>';
echo '<hr>';
echo '<h3>🛠️ ACTION: FORCE FIX ALL PRODUCTS</h3>';
echo '<form method="POST"><button name="fix_all" style="padding:10px 20px; background:red; color:white; border:none; cursor:pointer;">FORCE FIX VISIBILITY & STOCK ON ALL PRODUCTS</button></form>';

if (isset($_POST['fix_all'])) {
    echo '<pre>';
    $all_ids = get_posts(array('post_type' => 'product', 'posts_per_page' => -1, 'fields' => 'ids', 'post_status' => 'any'));
    foreach ($all_ids as $id) {
        $product = wc_get_product($id);
        $product->set_status('publish');
        $product->set_catalog_visibility('visible');
        $product->set_stock_status('instock');
        $product->set_regular_price(99); // Ensure price exists
        $product->save();
        
        // Fix variations too
        if ($product->is_type('variable')) {
            $variations = $product->get_children();
            foreach ($variations as $v_id) {
                $variation = wc_get_product($v_id);
                $variation->set_status('publish');
                $variation->set_stock_status('instock');
                $variation->set_regular_price(99);
                $variation->save();
            }
        }
        echo "Fixed ID: $id\n";
    }
    echo "✅ ALL PRODUCTS FIXED!\n";
    echo '</pre>';
}
?>
