<?php
/**
 * Moretti Theme Functions
 * 
 * @package Moretti
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Polish WooCommerce Defaults
function moretti_wallet_setup() {
    update_option('woocommerce_currency', 'PLN');
    update_option('woocommerce_default_country', 'PL');
    update_option('woocommerce_price_num_decimals', 2);
    update_option('woocommerce_currency_pos', 'right_space'); // 100,00 zł
    update_option('woocommerce_coming_soon', 'no');
    update_option('woocommerce_store_pages_only', 'no');
}
add_action('after_setup_theme', 'moretti_wallet_setup', 20);

// Professional Wallet Categories
function moretti_create_wallet_categories() {
    if (!class_exists('WooCommerce')) return;
    
    $categories = array(
        'portfele-meskie' => 'Portfele Męskie',
        'portfele-damskie' => 'Portfele Damskie',
        'slim-wallets' => 'Portfele Slim',
        'etui-na-karty' => 'Etui na karty',
        'akcesoria' => 'Akcesoria skórzane'
    );
    
    foreach ($categories as $slug => $name) {
        if (!get_term_by('slug', $slug, 'product_cat')) {
            wp_insert_term($name, 'product_cat', array('slug' => $slug));
        }
    }
}
add_action('init', 'moretti_create_wallet_categories');

// Register navigation menus
function moretti_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    register_nav_menus(array(
        'primary' => __('Menu Główne', 'moretti-theme'),
        'footer' => __('Menu Stopki', 'moretti-theme'),
    ));
}
add_action('after_setup_theme', 'moretti_theme_setup');

// Default menu fallback
function moretti_default_menu() {
    echo '<ul class="flex space-x-8 items-center">';
    echo '<li><a href="' . esc_url(home_url('/')) . '" class="text-sm text-charcoal hover:text-taupe-600 transition-colors uppercase tracking-widest">Start</a></li>';
    if (class_exists('WooCommerce')) {
        echo '<li><a href="' . esc_url(get_permalink(wc_get_page_id('shop'))) . '" class="text-sm text-charcoal hover:text-taupe-600 transition-colors uppercase tracking-widest">Sklep</a></li>';
    }
    echo '<li><a href="' . esc_url(home_url('/o-nas')) . '" class="text-sm text-charcoal hover:text-taupe-600 transition-colors uppercase tracking-widest">O nas</a></li>';
    echo '<li><a href="' . esc_url(home_url('/kontakt')) . '" class="text-sm text-charcoal hover:text-taupe-600 transition-colors uppercase tracking-widest">Kontakt</a></li>';
    echo '</ul>';
}

// Enqueue styles and scripts
function moretti_enqueue_assets() {
    // Main stylesheet (compiled Tailwind CSS)
    wp_enqueue_style(
        'moretti-main-style',
        get_template_directory_uri() . '/assets/css/main.css',
        array(),
        filemtime(get_template_directory() . '/assets/css/main.css')
    );

    // Main JavaScript
    wp_enqueue_script(
        'moretti-main-script',
        get_template_directory_uri() . '/assets/js/main.js',
        array(),
        filemtime(get_template_directory() . '/assets/js/main.js'),
        true
    );

    // Add inline script for Alpine.js or custom JS
    wp_localize_script('moretti-main-script', 'morettiData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('moretti-nonce'),
        'cartUrl' => wc_get_cart_url(),
    ));
}
add_action('wp_enqueue_scripts', 'moretti_enqueue_assets');

// WooCommerce customizations
function moretti_woocommerce_support() {
    // Remove default WooCommerce styles (we'll use Tailwind)
    add_filter('woocommerce_enqueue_styles', '__return_empty_array');
}
add_action('after_setup_theme', 'moretti_woocommerce_support');

// Custom WooCommerce template path
function moretti_woocommerce_template_path($template, $template_name, $template_path) {
    $custom_template = get_template_directory() . '/woocommerce/' . $template_name;
    
    if (file_exists($custom_template)) {
        return $custom_template;
    }
    
    return $template;
}
add_filter('woocommerce_locate_template', 'moretti_woocommerce_template_path', 10, 3);

// Add custom body classes
function moretti_body_classes($classes) {
    if (is_woocommerce()) {
        $classes[] = 'woocommerce-page';
    }
    return $classes;
}
add_filter('body_class', 'moretti_body_classes');

// Widget areas
function moretti_widgets_init() {
    register_sidebar(array(
        'name' => __('Sidebar', 'moretti-theme'),
        'id' => 'sidebar-1',
        'description' => __('Add widgets here.', 'moretti-theme'),
        'before_widget' => '<div id="%1$s" class="widget %2$s mb-6">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title text-lg font-bold mb-4">',
        'after_title' => '</h3>',
    ));

    register_sidebar(array(
        'name' => __('Footer', 'moretti-theme'),
        'id' => 'footer-1',
        'description' => __('Add footer widgets here.', 'moretti-theme'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widget-title text-sm font-semibold mb-3">',
        'after_title' => '</h4>',
    ));
}
add_action('widgets_init', 'moretti_widgets_init');

// Customize theme options
function moretti_customize_register($wp_customize) {
    // Top Banner Section
    $wp_customize->add_section('moretti_top_banner', array(
        'title' => __('Top Banner', 'moretti-theme'),
        'priority' => 30,
    ));

    $wp_customize->add_setting('show_top_banner', array(
        'default' => true,
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('show_top_banner', array(
        'label' => __('Show Top Banner', 'moretti-theme'),
        'section' => 'moretti_top_banner',
        'type' => 'checkbox',
    ));

    $wp_customize->add_setting('top_banner_text', array(
        'default' => 'Darmowa dostawa przy zamówieniach powyżej 250 zł. <a href="/shop" class="underline">Kup teraz</a>',
        'transport' => 'refresh',
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('top_banner_text', array(
        'label' => __('Banner Text', 'moretti-theme'),
        'section' => 'moretti_top_banner',
        'type' => 'textarea',
    ));
}
add_action('customize_register', 'moretti_customize_register');

// AJAX: Quick add to cart
function moretti_ajax_quick_add_to_cart() {
    check_ajax_referer('moretti-nonce', 'nonce');

    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? absint($_POST['quantity']) : 1;

    if ($product_id <= 0) {
        wp_send_json_error(array('message' => 'Invalid product ID'));
    }

    $result = WC()->cart->add_to_cart($product_id, $quantity);

    if ($result) {
        wp_send_json_success(array(
            'message' => 'Product added to cart',
            'cart_count' => WC()->cart->get_cart_contents_count(),
        ));
    } else {
        wp_send_json_error(array('message' => 'Failed to add product to cart'));
    }
}
add_action('wp_ajax_moretti_quick_add_to_cart', 'moretti_ajax_quick_add_to_cart');
add_action('wp_ajax_nopriv_moretti_quick_add_to_cart', 'moretti_ajax_quick_add_to_cart');

// AJAX: Get cart count
function moretti_ajax_get_cart_count() {
    wp_send_json_success(array(
        'count' => WC()->cart->get_cart_contents_count(),
    ));
}
add_action('wp_ajax_moretti_get_cart_count', 'moretti_ajax_get_cart_count');
add_action('wp_ajax_nopriv_moretti_get_cart_count', 'moretti_ajax_get_cart_count');

// Auto-create pages on theme activation
function moretti_create_default_pages() {
    // Check if pages already exist
    if (get_option('moretti_pages_created')) {
        return;
    }

    // Create About page
    $about_page = array(
        'post_title'    => 'O nas',
        'post_content'  => '',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'post_author'   => 1,
        'page_template' => 'page-about.php'
    );
    
    $about_id = wp_insert_post($about_page);
    
    // Create Contact page
    $contact_page = array(
        'post_title'    => 'Kontakt',
        'post_content'  => '',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'post_author'   => 1,
        'page_template' => 'page-contact.php'
    );
    
    $contact_id = wp_insert_post($contact_page);

    // Set page templates
    if ($about_id) {
        update_post_meta($about_id, '_wp_page_template', 'page-about.php');
    }
    if ($contact_id) {
        update_post_meta($contact_id, '_wp_page_template', 'page-contact.php');
    }

    // Create or update menu
    $menu_name = 'Primary Menu';
    $menu_exists = wp_get_nav_menu_object($menu_name);
    
    if (!$menu_exists) {
        $menu_id = wp_create_nav_menu($menu_name);
    } else {
        $menu_id = $menu_exists->term_id;
        // Delete existing items to recreate with Polish titles
        $items = wp_get_nav_menu_items($menu_id);
        if ($items) {
            foreach ($items as $item) {
                wp_delete_post($item->ID, true);
            }
        }
    }

    // Add pages to menu
    if ($menu_id) {
        // Add Home link
        wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-title' => 'Start',
            'menu-item-url' => home_url('/'),
            'menu-item-type' => 'custom',
            'menu-item-status' => 'publish',
            'menu-item-position' => 1
        ));

        // Add Shop page if WooCommerce is active
        if (class_exists('WooCommerce')) {
            $shop_page_id = wc_get_page_id('shop');
            if ($shop_page_id) {
                wp_update_nav_menu_item($menu_id, 0, array(
                    'menu-item-title' => 'Sklep',
                    'menu-item-object-id' => $shop_page_id,
                    'menu-item-object' => 'page',
                    'menu-item-type' => 'post_type',
                    'menu-item-status' => 'publish',
                    'menu-item-position' => 2
                ));
            }
        }

        // Add About page
        if ($about_id) {
            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => 'O nas',
                'menu-item-object-id' => $about_id,
                'menu-item-object' => 'page',
                'menu-item-type' => 'post_type',
                'menu-item-status' => 'publish',
                'menu-item-position' => 3
            ));
        }

        // Add Contact page
        if ($contact_id) {
            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => 'Kontakt',
                'menu-item-object-id' => $contact_id,
                'menu-item-object' => 'page',
                'menu-item-type' => 'post_type',
                'menu-item-status' => 'publish',
                'menu-item-position' => 4
            ));
        }

        // Assign menu to primary location
        $locations = get_theme_mod('nav_menu_locations');
        $locations['primary'] = $menu_id;
        set_theme_mod('nav_menu_locations', $locations);
    }

    // Mark as created
    update_option('moretti_pages_created', true);
}

// FORCE RESET PAGES FLAG (run once to translate)
// delete_option('moretti_pages_created');

add_action('after_switch_theme', 'moretti_create_default_pages');

// Auto-create sample products for WooCommerce
function moretti_create_sample_products() {
    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return;
    }

    // Check if products already created
    if (get_option('moretti_sample_products_created')) {
        return;
    }

    // Create product categories
    $categories = array(
        'Outerwear' => 'Coats, jackets, and blazers',
        'Dresses' => 'Elegant and casual dresses',
        'Skirts' => 'Versatile skirts for any occasion',
        'Pants & Leggings' => 'Comfortable pants and leggings',
        'Tops' => 'Shirts, blouses, and sweaters',
        'Lounge' => 'Comfortable loungewear',
    );

    $category_ids = array();
    foreach ($categories as $cat_name => $cat_desc) {
        $cat = wp_insert_term($cat_name, 'product_cat', array('description' => $cat_desc));
        if (!is_wp_error($cat)) {
            $category_ids[$cat_name] = $cat['term_id'];
        }
    }

    // Sample products data
    $products = array(
        array(
            'name' => 'Relaxed Blazer',
            'price' => 348,
            'category' => 'Outerwear',
            'description' => 'A timeless blazer crafted from premium wool blend. Features a relaxed fit with a self-tie belt.',
            'colors' => array('Beige', 'Black', 'Cream'),
        ),
        array(
            'name' => 'Alpaca Wool Cropped Cardigan',
            'price' => 248,
            'category' => 'Tops',
            'description' => 'Luxuriously soft alpaca wool cardigan with a cropped silhouette. Perfect for layering.',
            'colors' => array('Cream', 'Camel', 'Gray'),
        ),
        array(
            'name' => 'Silk Wide-Leg Pant',
            'price' => 198,
            'category' => 'Pants & Leggings',
            'description' => 'Elegant wide-leg pants in flowing silk. High-waisted with a comfortable elastic waistband.',
            'colors' => array('Black', 'Cream', 'Navy'),
        ),
        array(
            'name' => 'Classic Pant',
            'price' => 158,
            'category' => 'Pants & Leggings',
            'description' => 'Versatile straight-leg pants in stretch cotton. A wardrobe essential.',
            'colors' => array('Black', 'Gray', 'Beige'),
        ),
        array(
            'name' => 'Midi Skirt',
            'price' => 168,
            'category' => 'Skirts',
            'description' => 'A-line midi skirt with a flattering silhouette. Made from sustainable materials.',
            'colors' => array('Black', 'Cream'),
        ),
        array(
            'name' => 'Cashmere Cropped Sweater',
            'price' => 298,
            'category' => 'Tops',
            'description' => 'Ultra-soft cashmere sweater with a modern cropped fit. Timeless and cozy.',
            'colors' => array('Cream', 'Beige', 'Brown'),
        ),
        array(
            'name' => 'Poplin Oversized Shirt',
            'price' => 128,
            'category' => 'Tops',
            'description' => 'Classic white shirt in crisp poplin cotton. Oversized for effortless style.',
            'colors' => array('White', 'Cream'),
        ),
        array(
            'name' => 'Cashmere Funnel Neck Sweater',
            'price' => 278,
            'category' => 'Tops',
            'description' => 'Cozy funnel neck sweater in 100% cashmere. Perfect for cold days.',
            'colors' => array('Beige', 'Gray', 'Black'),
        ),
        array(
            'name' => 'Cashmere Cardigan',
            'price' => 348,
            'category' => 'Tops',
            'description' => 'Elegant button-front cardigan in luxurious cashmere.',
            'colors' => array('Cream', 'Beige'),
        ),
        array(
            'name' => 'Slim Black Slit Turtleneck Tee',
            'price' => 98,
            'category' => 'Tops',
            'description' => 'Sleek turtleneck with side slits. A modern wardrobe staple.',
            'colors' => array('Black'),
        ),
        array(
            'name' => 'Silk Draped Skirt',
            'price' => 228,
            'category' => 'Skirts',
            'description' => 'Flowing silk skirt with beautiful draping. Elegant and versatile.',
            'colors' => array('Cream', 'Black'),
        ),
        array(
            'name' => 'Charmeuse Shorts',
            'price' => 148,
            'category' => 'Lounge',
            'description' => 'Luxurious silk charmeuse shorts. Perfect for lounging in style.',
            'colors' => array('Cream', 'Black', 'Taupe'),
        ),
    );

    // Create color attribute
    $color_attribute = wc_create_attribute(array(
        'name' => 'Color',
        'slug' => 'pa_color',
        'type' => 'select',
        'order_by' => 'menu_order',
        'has_archives' => true,
    ));

    // Create products
    $created_product_ids = array();
    foreach ($products as $index => $product_data) {
        // Create simple or variable product
        $product = new WC_Product_Variable();
        
        $product->set_name($product_data['name']);
        $product->set_status('publish');
        $product->set_catalog_visibility('visible');
        $product->set_description($product_data['description']);
        $product->set_short_description(substr($product_data['description'], 0, 100) . '...');
        
        // Set category
        if (isset($category_ids[$product_data['category']])) {
            $product->set_category_ids(array($category_ids[$product_data['category']]));
        }

        // Save product to get ID
        $product_id = $product->save();
        $created_product_ids[] = $product_id;

        // Set as featured for first 4 products
        if ($index < 4) {
            update_post_meta($product_id, '_featured', 'yes');
        }

        // Create color attribute and variations
        $colors = $product_data['colors'];
        $attributes = array();
        
        $attribute = new WC_Product_Attribute();
        $attribute->set_id(wc_attribute_taxonomy_id_by_name('pa_color'));
        $attribute->set_name('pa_color');
        $attribute->set_options($colors);
        $attribute->set_visible(true);
        $attribute->set_variation(true);
        $attributes[] = $attribute;
        
        $product->set_attributes($attributes);
        $product->save();

        // Create variations for each color
        foreach ($colors as $color) {
            $variation = new WC_Product_Variation();
            $variation->set_parent_id($product_id);
            $variation->set_attributes(array('pa_color' => sanitize_title($color)));
            $variation->set_regular_price($product_data['price']);
            $variation->set_price($product_data['price']);
            $variation->set_stock_status('instock');
            $variation->set_manage_stock(true);
            $variation->set_stock_quantity(50);
            $variation->save();
        }

        // Add color terms to taxonomy
        foreach ($colors as $color) {
            wp_set_object_terms($product_id, sanitize_title($color), 'pa_color', true);
        }
    }

    // Mark as created
    update_option('moretti_sample_products_created', true);
    update_option('moretti_sample_product_ids', $created_product_ids);
}

// Run after theme activation (with delay to ensure WooCommerce is ready)
function moretti_delayed_product_creation() {
    if (class_exists('WooCommerce') && !get_option('moretti_sample_products_created')) {
        moretti_create_sample_products();
    }
}
add_action('woocommerce_init', 'moretti_delayed_product_creation');

// Force products to show on shop page
function moretti_force_shop_query($query) {
    if (!is_admin() && $query->is_main_query() && is_shop()) {
        $query->set('post_type', 'product');
        $query->set('post_status', 'publish');
    }
}
add_action('pre_get_posts', 'moretti_force_shop_query', 999);

// WooCommerce: Custom product loop structure
remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);

// Change products per page
function moretti_products_per_page() {
    return 12; // 3 rows x 4 columns
}
add_filter('loop_shop_per_page', 'moretti_products_per_page', 20);

// Helper function to get color hex from color name
function moretti_get_color_hex($color_name) {
    $color_name = strtolower(trim($color_name));
    
    $color_map = array(
        'black' => '#000000',
        'white' => '#FFFFFF',
        'beige' => '#ddd7cb',
        'cream' => '#f5f3ef',
        'taupe' => '#c4bcb2',
        'gray' => '#9ca3af',
        'grey' => '#9ca3af',
        'brown' => '#92725a',
        'camel' => '#c19a6b',
        'navy' => '#1e3a8a',
        'blue' => '#3b82f6',
        'red' => '#ef4444',
        'pink' => '#ec4899',
        'green' => '#10b981',
        'yellow' => '#fbbf24',
        'orange' => '#f97316',
        'purple' => '#a855f7',
    );
    
    return isset($color_map[$color_name]) ? $color_map[$color_name] : '#d1d5db';
}

// Remove default WooCommerce result count and ordering
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

// Add custom orderby options
function moretti_custom_orderby($orderby_options) {
    return array(
        'menu_order' => 'Domyślne sortowanie',
        'popularity' => 'Sortuj wg popularności',
        'rating'     => 'Sortuj wg średniej oceny',
        'date'       => 'Sortuj od najnowszych',
        'price'      => 'Cena: od najniższej',
        'price-desc' => 'Cena: od najwyższej',
    );
}
add_filter('woocommerce_catalog_orderby', 'moretti_custom_orderby');
add_filter('woocommerce_default_catalog_orderby_options', 'moretti_custom_orderby');
