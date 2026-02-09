<?php
/**
 * Add Gallery Images to Products (for testing slider)
 * Run this ONCE: http://localhost:8080/wp-content/themes/moretti-theme/add-gallery-images.php
 */

// Load WordPress
require_once('../../../wp-load.php');

if (!class_exists('WooCommerce')) {
    die('WooCommerce is not active!');
}

echo '<h1>📸 Adding Gallery Images to Products...</h1>';
echo '<pre>';

// Get first 4 products
$products = wc_get_products(array('limit' => 4, 'status' => 'publish'));

foreach ($products as $product) {
    echo "Processing: {$product->get_name()}\n";
    
    // Get current image
    $main_image_id = $product->get_image_id();
    
    if ($main_image_id) {
        // Duplicate the main image 2 more times to gallery (fake multiple images)
        // In real life, you'd upload different images via WP Admin
        $gallery_ids = array($main_image_id, $main_image_id);
        $product->set_gallery_image_ids($gallery_ids);
        $product->save();
        
        echo "  ✅ Added 2 gallery images (duplicated main image for demo)\n";
    } else {
        echo "  ⚠️  No main image, skipping\n";
    }
    
    echo "\n";
}

echo "✅ Done!\n";
echo '</pre>';

echo '<p><strong>NOTE:</strong> In production, you should upload DIFFERENT images to the gallery via WP Admin → Products → Edit Product → Product gallery.</p>';
echo '<p>This script just duplicates the main image for demo purposes.</p>';

echo '<p style="margin-top: 30px;">';
echo '<a href="' . home_url('/shop') . '" style="padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 4px;">🛍️ Go to Shop</a>';
echo '</p>';
