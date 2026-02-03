<?php
/**
 * FIX Shop 404 Error
 * Run this: http://localhost:8080/wp-content/themes/moretti-theme/fix-shop-404.php
 */

// Load WordPress
require_once('../../../wp-load.php');

if (!is_admin()) {
    // Become admin
    wp_set_current_user(1);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>🔧 Fix Shop 404</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 700px;
            margin: 50px auto;
            padding: 20px;
            line-height: 1.6;
        }
        .step {
            background: #f0f9ff;
            border-left: 4px solid #0284c7;
            padding: 15px;
            margin: 15px 0;
        }
        .success { color: #059669; }
        .error { color: #dc2626; }
        h1 { color: #1e293b; }
        code { background: #f1f5f9; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔧 Fixing Shop 404 Error...</h1>

    <?php
    // Step 1: Set permalink structure
    echo '<div class="step">';
    echo '<h3>1. Setting Permalink Structure</h3>';
    update_option('permalink_structure', '/%postname%/');
    echo '<p class="success">✅ Permalink structure set to: <code>/%postname%/</code></p>';
    echo '</div>';

    // Step 2: Make sure shop page exists and is set
    echo '<div class="step">';
    echo '<h3>2. Checking Shop Page</h3>';
    $shop_id = wc_get_page_id('shop');
    if ($shop_id <= 0) {
        // Shop page doesn't exist or isn't set - find or create it
        $shop_page = get_page_by_path('sklep');
        if (!$shop_page) {
            $shop_page = get_page_by_title('Sklep');
        }
        
        if ($shop_page) {
            // Found existing shop page - set it
            update_option('woocommerce_shop_page_id', $shop_page->ID);
            echo '<p class="success">✅ Shop page found and set: ID ' . $shop_page->ID . '</p>';
            $shop_id = $shop_page->ID;
        } else {
            echo '<p class="error">❌ Shop page not found! Creating new one...</p>';
            $new_shop_id = wp_insert_post(array(
                'post_title' => 'Sklep',
                'post_name' => 'sklep',
                'post_status' => 'publish',
                'post_type' => 'page',
            ));
            update_option('woocommerce_shop_page_id', $new_shop_id);
            echo '<p class="success">✅ Created new shop page: ID ' . $new_shop_id . '</p>';
            $shop_id = $new_shop_id;
        }
    } else {
        echo '<p class="success">✅ Shop page already set: ID ' . $shop_id . '</p>';
    }
    
    if ($shop_id > 0) {
        echo '<p>Shop URL: <code>' . get_permalink($shop_id) . '</code></p>';
    }
    echo '</div>';

    // Step 3: Flush rewrite rules
    echo '<div class="step">';
    echo '<h3>3. Flushing Rewrite Rules</h3>';
    flush_rewrite_rules(true);
    echo '<p class="success">✅ Rewrite rules flushed</p>';
    echo '</div>';

    // Step 4: Test if shop works
    echo '<div class="step">';
    echo '<h3>4. Testing Shop Page</h3>';
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 1,
        'post_status' => 'publish',
    );
    $test_query = new WP_Query($args);
    if ($test_query->have_posts()) {
        echo '<p class="success">✅ Products query works! Found ' . $test_query->found_posts . ' products</p>';
    } else {
        echo '<p class="error">❌ No products found in query</p>';
    }
    wp_reset_postdata();
    echo '</div>';
    ?>

    <div style="margin-top: 40px; padding: 20px; background: #dcfce7; border-radius: 6px;">
        <h2 style="margin-top: 0; color: #059669;">✅ Fix Complete!</h2>
        <p><strong>Now do this:</strong></p>
        <ol>
            <li>Close this tab</li>
            <li>Go to: <code><?php echo home_url('/shop'); ?></code></li>
            <li>Hard refresh (Cmd+Shift+R)</li>
        </ol>
    </div>

    <p style="text-align: center; margin-top: 30px;">
        <a href="<?php echo home_url('/shop'); ?>" style="display: inline-block; padding: 12px 24px; background: #0284c7; color: white; text-decoration: none; border-radius: 6px; font-weight: bold;">🛍️ Go to Shop</a>
    </p>
</body>
</html>
