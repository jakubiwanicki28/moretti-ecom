<?php
/**
 * One-time setup script
 * Access at: http://localhost:8080/wp-content/themes/moretti-theme/setup-run.php
 * 
 * This will create pages and products automatically.
 * Delete this file after running!
 */

// Load WordPress
require_once('../../../wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    die('Access denied. Please log in as admin first.');
}

echo '<html><head><title>Moretti Setup</title><style>body{font-family:sans-serif;max-width:800px;margin:50px auto;padding:20px;background:#f5f3ef;}h1{color:#2a2826;}pre{background:#fff;padding:15px;border-left:4px solid #2a2826;overflow-x:auto;}.success{color:green;}.error{color:red;}.button{display:inline-block;padding:10px 20px;background:#2a2826;color:#fff;text-decoration:none;margin:10px 0;}</style></head><body>';

echo '<h1>🚀 Moretti Theme Setup</h1>';

echo '<p><strong>This script will:</strong></p>';
echo '<ul>';
echo '<li>Create About and Contact pages</li>';
echo '<li>Create 12 sample products with variations</li>';
echo '<li>Create product categories</li>';
echo '<li>Create primary navigation menu</li>';
echo '</ul>';

if (isset($_GET['run']) && $_GET['run'] === 'yes') {
    echo '<h2>Running setup...</h2>';
    
    // Create pages
    echo '<p>Creating pages...</p>';
    if (function_exists('moretti_create_default_pages')) {
        moretti_create_default_pages();
        echo '<p class="success">✓ Pages created!</p>';
    } else {
        echo '<p class="error">✗ Function moretti_create_default_pages not found</p>';
    }
    
    // Create products
    echo '<p>Creating products (this may take 10-20 seconds)...</p>';
    flush();
    
    if (function_exists('moretti_create_sample_products')) {
        moretti_create_sample_products();
        echo '<p class="success">✓ Products created!</p>';
    } else {
        echo '<p class="error">✗ Function moretti_create_sample_products not found</p>';
    }
    
    echo '<h2 class="success">✅ Setup Complete!</h2>';
    echo '<p><a href="' . home_url('/shop') . '" class="button">View Shop</a></p>';
    echo '<p><a href="' . home_url() . '" class="button">View Homepage</a></p>';
    echo '<p style="color:#666;font-size:14px;">You can now delete this file: setup-run.php</p>';
    
} else {
    echo '<p><a href="?run=yes" class="button">▶ Run Setup Now</a></p>';
    echo '<p style="color:#666;font-size:14px;">Note: Make sure you are logged in as WordPress admin</p>';
}

echo '</body></html>';
