<?php
/**
 * Debug WooCommerce Template Usage
 * Run this: http://localhost:8080/wp-content/themes/moretti-theme/debug-templates.php
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
    <title>WooCommerce Template Debug</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, monospace;
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            line-height: 1.6;
            font-size: 13px;
        }
        h1 { font-size: 20px; margin-bottom: 20px; }
        h2 { font-size: 16px; margin-top: 30px; margin-bottom: 10px; background: #f0f0f0; padding: 8px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        th { background: #333; color: white; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
        pre { background: #f4f4f4; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 WooCommerce Template Debug</h1>
    
    <h2>Theme Information</h2>
    <table>
        <tr>
            <th>Property</th>
            <th>Value</th>
        </tr>
        <tr>
            <td>Active Theme</td>
            <td><?php echo wp_get_theme()->get('Name'); ?></td>
        </tr>
        <tr>
            <td>Theme Directory</td>
            <td><code><?php echo get_template_directory(); ?></code></td>
        </tr>
        <tr>
            <td>WooCommerce Version</td>
            <td><?php echo WC()->version; ?></td>
        </tr>
    </table>
    
    <h2>Custom Template Files in Theme</h2>
    <?php
    $theme_wc_dir = get_template_directory() . '/woocommerce/';
    $files = array(
        'archive-product.php',
        'content-product.php',
        'single-product.php',
        'loop/loop-start.php',
        'loop/loop-end.php',
        'single-product/product-image.php',
    );
    ?>
    <table>
        <tr>
            <th>Template File</th>
            <th>Exists</th>
            <th>Size</th>
            <th>Modified</th>
        </tr>
        <?php foreach ($files as $file) : 
            $full_path = $theme_wc_dir . $file;
            $exists = file_exists($full_path);
        ?>
        <tr>
            <td><code><?php echo $file; ?></code></td>
            <td class="<?php echo $exists ? 'success' : 'error'; ?>">
                <?php echo $exists ? '✅ YES' : '❌ NO'; ?>
            </td>
            <td><?php echo $exists ? number_format(filesize($full_path)) . ' bytes' : '-'; ?></td>
            <td><?php echo $exists ? date('Y-m-d H:i:s', filemtime($full_path)) : '-'; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <h2>WooCommerce Template Override Check</h2>
    <?php
    // Test if WooCommerce can locate our templates
    $test_templates = array(
        'archive-product.php' => wc_locate_template('archive-product.php'),
        'content-product.php' => wc_locate_template('content-product.php'),
        'single-product.php' => wc_locate_template('single-product.php'),
    );
    ?>
    <table>
        <tr>
            <th>Template</th>
            <th>WooCommerce Locates To</th>
            <th>Using Custom?</th>
        </tr>
        <?php foreach ($test_templates as $name => $located_path) :
            $is_custom = strpos($located_path, 'moretti-theme') !== false;
        ?>
        <tr>
            <td><code><?php echo $name; ?></code></td>
            <td><code><?php echo str_replace(ABSPATH, '', $located_path); ?></code></td>
            <td class="<?php echo $is_custom ? 'success' : 'error'; ?>">
                <?php echo $is_custom ? '✅ YES (Custom)' : '❌ NO (Default)'; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <h2>Active Hooks & Filters</h2>
    <?php
    global $wp_filter;
    $hooks_to_check = array(
        'woocommerce_locate_template',
        'woocommerce_before_shop_loop_item',
        'woocommerce_after_shop_loop_item',
        'woocommerce_before_template_part',
    );
    ?>
    <table>
        <tr>
            <th>Hook Name</th>
            <th>Priority</th>
            <th>Function</th>
        </tr>
        <?php 
        foreach ($hooks_to_check as $hook) :
            if (isset($wp_filter[$hook])) :
                foreach ($wp_filter[$hook]->callbacks as $priority => $callbacks) :
                    foreach ($callbacks as $callback) :
        ?>
        <tr>
            <td><code><?php echo $hook; ?></code></td>
            <td><?php echo $priority; ?></td>
            <td><?php 
                if (is_string($callback['function'])) {
                    echo $callback['function'];
                } elseif (is_array($callback['function'])) {
                    echo is_object($callback['function'][0]) ? get_class($callback['function'][0]) : $callback['function'][0];
                    echo '::' . $callback['function'][1];
                } else {
                    echo 'Closure';
                }
            ?></td>
        </tr>
        <?php 
                    endforeach;
                endforeach;
            endif;
        endforeach;
        ?>
    </table>
    
    <h2>Action Required</h2>
    <?php
    $all_custom = true;
    foreach ($test_templates as $name => $located_path) {
        if (strpos($located_path, 'moretti-theme') === false) {
            $all_custom = false;
            break;
        }
    }
    ?>
    <?php if ($all_custom) : ?>
        <p class="success">✅ All templates are using custom theme versions!</p>
    <?php else : ?>
        <p class="error">❌ Some templates are still using WooCommerce defaults!</p>
        <p><strong>Try:</strong></p>
        <ol>
            <li>Run the <a href="flush-cache.php">Cache Flush</a></li>
            <li>Go to WP Admin → WooCommerce → Status → Tools → "Delete all WooCommerce transients"</li>
            <li>Restart WordPress container: <code>docker restart moretti-wordpress</code></li>
        </ol>
    <?php endif; ?>
    
    <p style="margin-top: 40px;">
        <a href="<?php echo home_url('/shop'); ?>" style="padding: 10px 20px; background: #333; color: white; text-decoration: none; border-radius: 4px;">← Back to Shop</a>
        <a href="flush-cache.php" style="padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin-left: 10px;">🗑️ Flush Cache</a>
    </p>
</body>
</html>
