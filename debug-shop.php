<?php
/**
 * Debug why shop page shows no products
 * Run this: http://localhost:8080/wp-content/themes/moretti-theme/debug-shop.php
 */

// Load WordPress
require_once('../../../wp-load.php');

if (!class_exists('WooCommerce')) {
    die('WooCommerce is not active!');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>🔍 Shop Debug</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, monospace;
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            font-size: 13px;
        }
        h1 { font-size: 20px; }
        h2 { font-size: 16px; background: #f0f0f0; padding: 8px; margin-top: 20px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        th { background: #333; color: white; }
        code { background: #f4f4f4; padding: 2px 6px; }
        pre { background: #f4f4f4; padding: 10px; overflow-x: auto; font-size: 11px; }
    </style>
</head>
<body>
    <h1>🔍 Shop Page Debug</h1>

    <h2>WooCommerce Shop Page Settings</h2>
    <table>
        <tr>
            <th>Setting</th>
            <th>Value</th>
        </tr>
        <tr>
            <td>Shop Page ID</td>
            <td><?php 
                $shop_id = wc_get_page_id('shop');
                echo $shop_id > 0 ? '<span class="success">' . $shop_id . '</span>' : '<span class="error">NOT SET</span>';
            ?></td>
        </tr>
        <?php if ($shop_id > 0) : 
            $shop_page = get_post($shop_id);
        ?>
        <tr>
            <td>Shop Page Status</td>
            <td><?php echo $shop_page->post_status; ?></td>
        </tr>
        <tr>
            <td>Shop Page URL</td>
            <td><code><?php echo get_permalink($shop_id); ?></code></td>
        </tr>
        <tr>
            <td>Shop Page Slug</td>
            <td><code><?php echo $shop_page->post_name; ?></code></td>
        </tr>
        <?php endif; ?>
    </table>

    <h2>Products Count</h2>
    <?php
    $all_products = wc_get_products(array('limit' => -1, 'status' => 'publish'));
    $product_count = count($all_products);
    ?>
    <table>
        <tr>
            <th>Type</th>
            <th>Count</th>
        </tr>
        <tr>
            <td>Total Published Products</td>
            <td class="<?php echo $product_count > 0 ? 'success' : 'error'; ?>">
                <?php echo $product_count; ?>
            </td>
        </tr>
        <?php
        $variable_count = 0;
        $simple_count = 0;
        foreach ($all_products as $p) {
            if ($p->is_type('variable')) $variable_count++;
            if ($p->is_type('simple')) $simple_count++;
        }
        ?>
        <tr>
            <td>Variable Products</td>
            <td><?php echo $variable_count; ?></td>
        </tr>
        <tr>
            <td>Simple Products</td>
            <td><?php echo $simple_count; ?></td>
        </tr>
    </table>

    <h2>Test WooCommerce Query</h2>
    <?php
    // Simulate what happens on shop page
    global $wp_query;
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 12,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
    );
    $test_query = new WP_Query($args);
    ?>
    <table>
        <tr>
            <th>Property</th>
            <th>Value</th>
        </tr>
        <tr>
            <td>Found Posts</td>
            <td class="<?php echo $test_query->found_posts > 0 ? 'success' : 'error'; ?>">
                <?php echo $test_query->found_posts; ?>
            </td>
        </tr>
        <tr>
            <td>Post Count</td>
            <td><?php echo $test_query->post_count; ?></td>
        </tr>
        <tr>
            <td>Have Posts</td>
            <td><?php echo $test_query->have_posts() ? '✅ YES' : '❌ NO'; ?></td>
        </tr>
    </table>

    <?php if ($test_query->have_posts()) : ?>
        <h2>Sample Products</h2>
        <ul>
        <?php while ($test_query->have_posts()) : $test_query->the_post(); 
            $product = wc_get_product(get_the_ID());
        ?>
            <li>
                <strong><?php the_title(); ?></strong> - 
                <?php echo $product->get_type(); ?> - 
                <?php echo $product->get_price_html(); ?>
            </li>
        <?php endwhile; wp_reset_postdata(); ?>
        </ul>
    <?php endif; ?>

    <h2>Permalink Settings</h2>
    <table>
        <tr>
            <th>Setting</th>
            <th>Value</th>
        </tr>
        <tr>
            <td>Permalink Structure</td>
            <td><code><?php echo get_option('permalink_structure') ?: 'Plain (default)'; ?></code></td>
        </tr>
        <tr>
            <td>Product Base</td>
            <td><code><?php echo get_option('woocommerce_permalinks')['product_base'] ?? 'product'; ?></code></td>
        </tr>
        <tr>
            <td>Category Base</td>
            <td><code><?php echo get_option('woocommerce_permalinks')['category_base'] ?? 'product-category'; ?></code></td>
        </tr>
    </table>

    <h2>Diagnosis</h2>
    <?php if ($product_count == 0) : ?>
        <p class="error">❌ <strong>NO PRODUCTS FOUND!</strong></p>
        <p>Run <a href="recreate-products.php">recreate-products.php</a> to create sample products.</p>
    <?php elseif ($shop_id <= 0) : ?>
        <p class="error">❌ <strong>SHOP PAGE NOT SET!</strong></p>
        <p>Go to WooCommerce → Settings → Products → Advanced and set the Shop page.</p>
    <?php elseif (!$test_query->have_posts()) : ?>
        <p class="error">❌ <strong>QUERY RETURNS NO PRODUCTS!</strong></p>
        <p>Products exist but WP_Query can't find them. Try:</p>
        <ol>
            <li>Run <a href="nuclear-reset.php">nuclear-reset.php</a></li>
            <li>Restart WordPress: <code>docker restart moretti-wordpress</code></li>
        </ol>
    <?php else : ?>
        <p class="success">✅ <strong>EVERYTHING LOOKS GOOD!</strong></p>
        <p>Products exist and query works. The issue is likely cache. Run <a href="nuclear-reset.php">nuclear-reset.php</a></p>
    <?php endif; ?>

    <p style="margin-top: 40px;">
        <a href="nuclear-reset.php" style="padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 4px;">☢️ Nuclear Reset</a>
        <a href="<?php echo home_url('/shop'); ?>" style="padding: 10px 20px; background: #333; color: white; text-decoration: none; border-radius: 4px; margin-left: 10px;">← Back to Shop</a>
    </p>
</body>
</html>
