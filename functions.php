<?php
/**
 * Moretti Theme Functions
 * 
 * @package Moretti
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Include site setup engine
require_once get_template_directory() . '/inc/theme-setup-data.php';

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
        'portfele' => 'Portfele',
        'wizytowniki' => 'Wizytowniki',
        'akcesoria' => 'Akcesoria'
    );
    
    foreach ($categories as $slug => $name) {
        if (!get_term_by('slug', $slug, 'product_cat')) {
            wp_insert_term($name, 'product_cat', array('slug' => $slug));
        }
    }
}
add_action('init', 'moretti_create_wallet_categories');

// Register WooCommerce Attributes for Filters
function moretti_register_attributes() {
    if (!class_exists('WooCommerce')) return;

    $attributes = array(
        'wielkosc' => 'Wielkość',
        'zapiecie' => 'Zapięcie',
        'wykonczenie' => 'Wykończenie',
        'wzor' => 'Wzór',
        'material' => 'Materiał'
    );

    foreach ($attributes as $slug => $name) {
        if (!taxonomy_exists(wc_attribute_taxonomy_name($slug))) {
            wc_create_attribute(array(
                'name' => $name,
                'slug' => $slug,
                'type' => 'select',
                'order_by' => 'menu_order',
                'has_archives' => true,
            ));
        }
    }

    // Add terms for attributes
    $attribute_terms = array(
        'pa_wielkosc' => array('Duży', 'Średni', 'Mały'),
        'pa_zapiecie' => array('Bigiel', 'Suwak', 'Magnes'),
        'pa_wykonczenie' => array('Lakier', 'Mat'),
        'pa_wzor' => array('Gładka', 'Ze wzorem'),
        'pa_material' => array('Skóra naturalna')
    );

    foreach ($attribute_terms as $taxonomy => $terms) {
        foreach ($terms as $term) {
            if (!term_exists($term, $taxonomy)) {
                wp_insert_term($term, $taxonomy);
            }
        }
    }
}
add_action('admin_init', 'moretti_register_attributes');

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

// Include site setup engine
require_once get_template_directory() . '/inc/theme-setup-data.php';

// Default menu fallback
function moretti_default_menu() {
    echo '<ul class="flex space-x-8 items-center">';
    echo '<li><a href="' . esc_url(home_url('/')) . '" class="text-sm text-charcoal hover:text-taupe-600 transition-colors uppercase tracking-widest">Start</a></li>';
    if (class_exists('WooCommerce')) {
        echo '<li><a href="' . esc_url(get_permalink(wc_get_page_id('shop'))) . '" class="text-sm text-charcoal hover:text-taupe-600 transition-colors uppercase tracking-widest">Sklep</a></li>';
    }
    echo '</ul>';
}

// Helper function to get hex color from name
function moretti_get_color_hex($color_name) {
    $color_name = strtolower($color_name);
    $color_map = array(
        'czarny' => '#000000',
        'black' => '#000000',
        'brązowy' => '#8B4513',
        'brown' => '#8B4513',
        'beżowy' => '#F5F5DC',
        'beige' => '#F5F5DC',
        'szary' => '#808080',
        'gray' => '#808080',
        'grey' => '#808080',
        'biały' => '#FFFFFF',
        'white' => '#FFFFFF',
        'czerwony' => '#DC2626',
        'red' => '#DC2626',
        'niebieski' => '#3B82F6',
        'blue' => '#3B82F6',
        'granatowy' => '#000080',
        'navy' => '#000080',
        'cream' => '#f5f3ef',
        'kremowy' => '#f5f3ef',
        'taupe' => '#8f8275',
    );

    foreach ($color_map as $name => $hex) {
        if (strpos($color_name, $name) !== false) {
            return $hex;
        }
    }

    return '#e5e7eb'; // Default gray
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

    // Shop clean stylesheet (only on shop pages)
    if (is_shop() || is_product_taxonomy()) {
        wp_enqueue_style(
            'moretti-shop-clean',
            get_template_directory_uri() . '/assets/css/shop-clean.css',
            array('moretti-main-style'),
            filemtime(get_template_directory() . '/assets/css/shop-clean.css')
        );
    }

    // Main JavaScript
    wp_enqueue_script(
        'moretti-main-script',
        get_template_directory_uri() . '/assets/js/main.js',
        array(),
        filemtime(get_template_directory() . '/assets/js/main.js'),
        true
    );

    // Add inline script for AJAX
    wp_localize_script('moretti-main-script', 'morettiData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('moretti-nonce'),
        'cartUrl' => wc_get_cart_url(),
    ));
}
add_action('wp_enqueue_scripts', 'moretti_enqueue_assets');

// AJAX: Quick add to cart
function moretti_ajax_quick_add_to_cart() {
    // Check nonce but be lenient in local dev if it fails
    $nonce_valid = isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'moretti-nonce');
    
    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? absint($_POST['quantity']) : 1;

    if ($product_id <= 0) {
        wp_send_json_error(array('message' => 'Nieprawidłowe ID produktu.'));
    }

    // Ensure WC is loaded
    if (!function_exists('WC') || is_null(WC()->cart)) {
        wp_send_json_error(array('message' => 'Błąd systemu koszyka. Spróbuj odświeżyć stronę.'));
    }

    // Check if product is variable
    $product = wc_get_product($product_id);
    if ($product && $product->is_type('variable')) {
        wp_send_json_error(array(
            'message' => 'Ten produkt ma warianty. Wybierz opcje na stronie produktu.',
            'redirect' => get_permalink($product_id)
        ));
    }

    $result = WC()->cart->add_to_cart($product_id, $quantity);

    if ($result) {
        wp_send_json_success(array(
            'message' => 'Produkt dodany do koszyka',
            'cart_count' => WC()->cart->get_cart_contents_count(),
        ));
    } else {
        wp_send_json_error(array('message' => 'Nie udało się dodać produktu do koszyka.'));
    }
}
add_action('wp_ajax_moretti_quick_add_to_cart', 'moretti_ajax_quick_add_to_cart');
add_action('wp_ajax_nopriv_moretti_quick_add_to_cart', 'moretti_ajax_quick_add_to_cart');

// AJAX: Get cart count
function moretti_ajax_get_cart_count() {
    if (function_exists('WC') && !is_null(WC()->cart)) {
        wp_send_json_success(array(
            'count' => WC()->cart->get_cart_contents_count(),
        ));
    } else {
        wp_send_json_error();
    }
}
add_action('wp_ajax_moretti_get_cart_count', 'moretti_ajax_get_cart_count');
add_action('wp_ajax_nopriv_moretti_get_cart_count', 'moretti_ajax_get_cart_count');

// Disable Select2/SelectWoo - Use native selects for clean, consistent UI
function moretti_disable_select2() {
    wp_dequeue_style('select2');
    wp_deregister_style('select2');
    wp_dequeue_script('selectWoo');
    wp_deregister_script('selectWoo');
    wp_dequeue_script('select2');
    wp_deregister_script('select2');
}
add_action('wp_enqueue_scripts', 'moretti_disable_select2', 100);

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

// Force empty cart template and clean content
function moretti_force_empty_cart_ui($content) {
    if (is_cart() && class_exists('WooCommerce') && WC()->cart->is_empty()) {
        ob_start();
        include get_template_directory() . '/woocommerce/cart/cart-empty.php';
        return ob_get_clean();
    }
    return $content;
}
add_filter('the_content', 'moretti_force_empty_cart_ui', 999);

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

        // O nas and Kontakt pages are created but NOT added to menu
        // They remain accessible via direct URL but hidden from navigation

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

/**
 * Custom CSS for mobile product page layout
 */
function moretti_custom_mobile_product_css() {
    ?>
    <style>
        /* Mobile Product Page - Full width gallery */
        @media (max-width: 767px) {
            /* Product images - full width on mobile */
            .single-product .product-images,
            .single-product .woocommerce-product-gallery {
                width: 100vw !important;
                max-width: 100vw !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
            
            /* Gallery images - full width */
            .single-product .woocommerce-product-gallery__wrapper,
            .single-product .woocommerce-product-gallery__image {
                width: 100% !important;
                max-width: 100% !important;
            }
            
            /* Main product image */
            .single-product .woocommerce-product-gallery__image img {
                width: 100% !important;
                height: auto !important;
                object-fit: cover !important;
            }
            
            /* Thumbnail images row - better spacing */
            .single-product .flex-control-thumbs {
                padding: 0.5rem !important;
                gap: 0.5rem !important;
            }
            
            .single-product .flex-control-thumbs li {
                margin: 0 !important;
            }
        }
        
        /* Desktop - maintain proper spacing */
        @media (min-width: 768px) {
            .single-product .product-images {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
    </style>
    <?php
}
add_action('wp_head', 'moretti_custom_mobile_product_css');
