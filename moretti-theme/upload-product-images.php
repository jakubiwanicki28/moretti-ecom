<?php
/**
 * Upload Product Images and Assign to All Products
 * Run this: http://localhost:8080/wp-content/themes/moretti-theme/upload-product-images.php
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
    <title>📸 Upload Product Images</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 900px;
            margin: 30px auto;
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
        code { background: #f1f5f9; padding: 2px 6px; border-radius: 3px; font-size: 12px; }
        img { max-width: 150px; height: auto; margin: 5px; border: 2px solid #e2e8f0; }
    </style>
</head>
<body>
    <h1>📸 Uploading Product Images...</h1>

<?php
// Source images from theme's images folder (accessible inside Docker)
$theme_dir = get_template_directory();
$source_images = array(
    $theme_dir . '/images/photo1.jpg',
    $theme_dir . '/images/photo2.jpg',
    $theme_dir . '/images/photo3.png',
);

echo '<div class="step">';
echo '<h3>1. Uploading Images to WordPress Media Library</h3>';

$uploaded_image_ids = array();

foreach ($source_images as $index => $source_path) {
    if (!file_exists($source_path)) {
        echo "<p class='error'>❌ File not found: " . basename($source_path) . "</p>";
        continue;
    }
    
    // Get file info
    $ext = pathinfo($source_path, PATHINFO_EXTENSION);
    $filename = 'product-image-' . ($index + 1) . '.' . $ext;
    $filetype = wp_check_filetype($filename, null);
    
    // Prepare upload
    $upload_dir = wp_upload_dir();
    $upload_path = $upload_dir['path'] . '/' . $filename;
    
    // Copy file to uploads
    if (copy($source_path, $upload_path)) {
        // Create attachment
        $attachment = array(
            'guid'           => $upload_dir['url'] . '/' . $filename,
            'post_mime_type' => $filetype['type'],
            'post_title'     => 'Product Image ' . ($index + 1),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );
        
        // Insert attachment
        $attach_id = wp_insert_attachment($attachment, $upload_path);
        
        // Generate metadata
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $upload_path);
        wp_update_attachment_metadata($attach_id, $attach_data);
        
        $uploaded_image_ids[] = $attach_id;
        
        echo "<p class='success'>✅ Uploaded: <strong>$filename</strong> (ID: $attach_id)</p>";
        echo "<img src='" . wp_get_attachment_url($attach_id) . "' alt='Product $index'>";
    } else {
        echo "<p class='error'>❌ Failed to copy: " . basename($source_path) . "</p>";
    }
}

echo '</div>';

if (empty($uploaded_image_ids)) {
    echo '<div class="step"><p class="error">❌ No images were uploaded!</p></div>';
    exit;
}

// Step 2: Assign to all products
echo '<div class="step">';
echo '<h3>2. Assigning Images to All Products</h3>';

// Get ALL products (unlimited)
$products = wc_get_products(array('limit' => -1, 'status' => 'any'));
$updated_count = 0;

foreach ($products as $product) {
    // Set main image (first uploaded image)
    $product->set_image_id($uploaded_image_ids[0]);
    
    // Set gallery images (remaining images)
    $gallery_ids = array_slice($uploaded_image_ids, 1);
    $product->set_gallery_image_ids($gallery_ids);
    
    // Set status to publish if it was draft
    if ($product->get_status() !== 'publish') {
        $product->set_status('publish');
    }
    
    $product->save();
    $updated_count++;
    
    if ($updated_count <= 10) {
        echo "<p class='success'>✅ Updated: <strong>{$product->get_name()}</strong> (ID: {$product->get_id()})</p>";
    }
}

echo "<p><strong>Total products updated: $updated_count</strong></p>";
echo '</div>';

// Step 3: Summary
echo '<div class="step">';
echo '<h3>3. Summary</h3>';
echo '<ul>';
echo '<li class="success">✅ Uploaded ' . count($uploaded_image_ids) . ' images</li>';
echo '<li class="success">✅ Updated ' . $updated_count . ' products</li>';
echo '<li class="success">✅ Each product now has:</li>';
echo '<ul style="margin-left: 20px;">';
echo '<li>1 main image (product-image-1.png)</li>';
echo '<li>2 gallery images (product-image-2.png, product-image-3.png)</li>';
echo '</ul>';
echo '</ul>';
echo '</div>';
?>

    <div style="margin-top: 40px; padding: 20px; background: #dcfce7; border-radius: 6px;">
        <h2 style="margin-top: 0; color: #059669;">🎉 Success!</h2>
        <p><strong>Now:</strong></p>
        <ol>
            <li>Go to: <code><?php echo home_url('/shop'); ?></code></li>
            <li>Hard refresh (Cmd+Shift+R)</li>
            <li>Hover over any product - you'll see arrows ← →</li>
            <li>Click arrows to see the slider working!</li>
        </ol>
    </div>

    <p style="text-align: center; margin-top: 30px;">
        <a href="<?php echo home_url('/shop'); ?>" style="display: inline-block; padding: 12px 24px; background: #0284c7; color: white; text-decoration: none; border-radius: 6px; font-weight: bold;">🛍️ Go to Shop</a>
    </p>
</body>
</html>
