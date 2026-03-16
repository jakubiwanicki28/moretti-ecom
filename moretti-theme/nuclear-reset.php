<?php
/**
 * NUCLEAR RESET - Clears EVERYTHING
 * Run this after ANY changes: http://localhost:8080/wp-content/themes/moretti-theme/nuclear-reset.php
 */

// Load WordPress
require_once('../../../wp-load.php');

?>
<!DOCTYPE html>
<html>
<head>
    <title>☢️ Nuclear Reset</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, monospace;
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background: #1a1a1a;
            color: #fff;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 32px;
        }
        .step {
            background: #2d2d2d;
            padding: 20px;
            margin: 15px 0;
            border-radius: 6px;
            border-left: 4px solid #667eea;
        }
        .step h3 {
            margin: 0 0 10px 0;
            color: #667eea;
        }
        .success {
            color: #4ade80;
        }
        .error {
            color: #f87171;
        }
        .buttons {
            margin-top: 40px;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 15px 30px;
            margin: 0 10px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
        .btn:hover {
            background: #764ba2;
        }
        code {
            background: #1a1a1a;
            padding: 2px 6px;
            border-radius: 3px;
            color: #fbbf24;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>☢️ NUCLEAR RESET</h1>
        <p>Clearing all WordPress & WooCommerce caches...</p>
    </div>

    <?php
    echo '<div class="step">';
    echo '<h3>1. Flushing Rewrite Rules</h3>';
    flush_rewrite_rules(true);
    echo '<p class="success">✅ Rewrite rules flushed</p>';
    echo '</div>';

    echo '<div class="step">';
    echo '<h3>2. Clearing Transients</h3>';
    global $wpdb;
    $deleted = $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_%'");
    echo '<p class="success">✅ Deleted ' . $deleted . ' transient options</p>';
    $deleted2 = $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_site_transient_%'");
    echo '<p class="success">✅ Deleted ' . $deleted2 . ' site transients</p>';
    echo '</div>';

    echo '<div class="step">';
    echo '<h3>3. Clearing WooCommerce Caches</h3>';
    if (class_exists('WC_Cache_Helper')) {
        WC_Cache_Helper::get_transient_version('shipping', true);
        WC_Cache_Helper::get_transient_version('product', true);
        echo '<p class="success">✅ WooCommerce cache versions reset</p>';
    }
    delete_transient('wc_products_onsale');
    delete_transient('wc_featured_products');
    delete_transient('wc_count_comments');
    delete_transient('wc_term_counts');
    echo '<p class="success">✅ WooCommerce specific transients cleared</p>';
    echo '</div>';

    echo '<div class="step">';
    echo '<h3>4. Clearing Object Cache</h3>';
    wp_cache_flush();
    echo '<p class="success">✅ Object cache flushed</p>';
    echo '</div>';

    echo '<div class="step">';
    echo '<h3>5. Clearing Theme Cache</h3>';
    delete_option('_site_transient_theme_roots');
    delete_transient('wc_template_version');
    echo '<p class="success">✅ Theme cache cleared</p>';
    echo '</div>';

    echo '<div class="step">';
    echo '<h3>6. Syncing Product Variations</h3>';
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    );
    $products = get_posts($args);
    $synced = 0;
    foreach ($products as $post) {
        $product = wc_get_product($post->ID);
        if ($product && $product->is_type('variable')) {
            WC_Product_Variable::sync($post->ID);
            $synced++;
        }
    }
    echo '<p class="success">✅ Synced ' . $synced . ' variable products</p>';
    echo '<p>Total products in database: <strong>' . count($products) . '</strong></p>';
    echo '</div>';

    echo '<div class="step">';
    echo '<h3>7. Checking Shop Page</h3>';
    $shop_page_id = wc_get_page_id('shop');
    if ($shop_page_id > 0) {
        $shop_page = get_post($shop_page_id);
        echo '<p class="success">✅ Shop page exists: <code>' . get_permalink($shop_page_id) . '</code></p>';
        echo '<p>Status: <strong>' . $shop_page->post_status . '</strong></p>';
    } else {
        echo '<p class="error">❌ Shop page NOT set!</p>';
        echo '<p>Go to WooCommerce → Settings → Products → set Shop page</p>';
    }
    echo '</div>';

    echo '<div class="step">';
    echo '<h3>8. Verifying Templates</h3>';
    $templates = array(
        'archive-product.php' => wc_locate_template('archive-product.php'),
        'content-product.php' => wc_locate_template('content-product.php'),
        'single-product.php' => wc_locate_template('single-product.php'),
    );
    foreach ($templates as $name => $path) {
        $is_custom = strpos($path, 'moretti-theme') !== false;
        if ($is_custom) {
            echo '<p class="success">✅ <code>' . $name . '</code> using custom theme version</p>';
        } else {
            echo '<p class="error">❌ <code>' . $name . '</code> using WooCommerce default</p>';
        }
    }
    echo '</div>';
    ?>

    <div class="buttons">
        <a href="<?php echo home_url('/shop'); ?>" class="btn">🛍️ Go to Shop</a>
        <a href="<?php echo admin_url('admin.php?page=wc-settings&tab=products'); ?>" class="btn">⚙️ WooCommerce Settings</a>
    </div>

    <div style="margin-top: 40px; padding: 20px; background: #2d2d2d; border-radius: 6px;">
        <p><strong>⚡ NOW DO THIS:</strong></p>
        <ol style="line-height: 2;">
            <li>Close this tab</li>
            <li>Hard refresh (Cmd+Shift+R) the shop page: <code><?php echo home_url('/shop'); ?></code></li>
            <li>If STILL no products, restart Docker: <code>docker restart moretti-wordpress</code></li>
        </ol>
    </div>
</body>
</html>
