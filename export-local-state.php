<?php
/**
 * Exporter to dump current local store state to a JSON file.
 * Run this locally to update the setup script data.
 */
define('WP_USE_THEMES', false);
require_once(__DIR__ . '/../../../wp-load.php');

if (!class_exists('WooCommerce')) die('WC not active');

$data = [
    'categories' => [],
    'attributes' => [],
    'products' => []
];

// 1. Get Categories
$cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
foreach ($cats as $cat) {
    if ($cat->slug !== 'uncategorized') {
        $data['categories'][] = $cat->name;
    }
}

// 2. Get Attributes
$attrs = wc_get_attribute_taxonomies();
foreach ($attrs as $attr) {
    $taxonomy = wc_attribute_taxonomy_name($attr->attribute_name);
    $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);
    $data['attributes'][] = [
        'name' => $attr->attribute_label,
        'slug' => $attr->attribute_name,
        'terms' => wp_list_pluck($terms, 'name')
    ];
}

// 3. Get Products
$products = str_contains(site_url(), 'localhost') ? wc_get_products(['limit' => -1]) : [];
foreach ($products as $product) {
    $p_data = [
        'title' => $product->get_name(),
        'type' => $product->get_type(),
        'price' => $product->get_regular_price(),
        'categories' => wp_get_post_terms($product->get_id(), 'product_cat', ['fields' => 'names']),
        'description' => $product->get_description(),
        'image' => basename(wp_get_attachment_url($product->get_image_id())),
        'gallery' => []
    ];

    foreach ($product->get_gallery_image_ids() as $id) {
        $p_data['gallery'][] = basename(wp_get_attachment_url($id));
    }

    if ($product->is_type('variable')) {
        $p_data['variations'] = [];
        foreach ($product->get_available_variations() as $variation_data) {
            $variation = wc_get_product($variation_data['variation_id']);
            $p_data['variations'][] = [
                'attributes' => $variation->get_attributes(),
                'price' => $variation->get_regular_price(),
                'image' => basename(wp_get_attachment_url($variation->get_image_id()))
            ];
        }
    }

    $data['products'][] = $p_data;
}

echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
