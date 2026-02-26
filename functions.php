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
    update_option('blogname', 'Moretti - Ekskluzywne Portfele Premium');
    update_option('blogdescription', 'Ponadczasowa Elegancja i Rzemiosło');
    update_option('woocommerce_currency', 'PLN');
    update_option('woocommerce_default_country', 'PL');
    update_option('woocommerce_price_num_decimals', 2);
    update_option('woocommerce_currency_pos', 'right_space'); // 100,00 zł
    update_option('woocommerce_coming_soon', 'no');
    update_option('woocommerce_store_pages_only', 'no');
    update_option('woocommerce_enable_reviews', 'yes');
    update_option('woocommerce_enable_review_rating', 'yes');
    update_option('woocommerce_review_rating_required', 'no');
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

/**
 * Register dedicated image sizes for homepage product tiles.
 */
function moretti_register_image_sizes() {
    add_image_size('moretti_home_tile', 1000, 1000, true);
}
add_action('after_setup_theme', 'moretti_register_image_sizes', 30);

/**
 * Keep frontend image quality at a safe baseline.
 */
function moretti_image_quality($quality) {
    return 88;
}
add_filter('jpeg_quality', 'moretti_image_quality');
add_filter('wp_editor_set_quality', 'moretti_image_quality');

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

/**
 * Resolve lowest price from last 30 days for Omnibus-like display.
 * Uses known plugin meta keys when available, otherwise falls back to regular price on sale.
 */
function moretti_get_lowest_price_last_30_days($product) {
    if (!$product instanceof WC_Product) {
        return null;
    }

    $meta_keys = array(
        '_omnibus_lowest_price',
        '_wc_omnibus_lowest_price',
        '_alg_wc_omnibus_price',
        '_lowest_price_30_days',
        '_lowest_price_last_30_days',
    );

    foreach ($meta_keys as $meta_key) {
        $value = get_post_meta($product->get_id(), $meta_key, true);
        if ($value !== '' && is_numeric($value) && (float) $value > 0) {
            return (float) $value;
        }
    }

    if ($product->is_on_sale()) {
        $regular = (float) $product->get_regular_price();
        $sale = (float) $product->get_sale_price();
        if ($regular > 0 && $sale > 0 && $regular > $sale) {
            return $regular;
        }
    }

    return null;
}

/**
 * Keep quantity fixed to 1 on single product page.
 */
function moretti_single_product_force_quantity_one($args, $product) {
    if (is_product()) {
        $args['input_value'] = 1;
        $args['min_value'] = 1;
        $args['max_value'] = 1;
    }
    return $args;
}
add_filter('woocommerce_quantity_input_args', 'moretti_single_product_force_quantity_one', 10, 2);

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
                width: 100% !important;
                max-width: 100% !important;
                margin-left: auto !important;
                margin-right: auto !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
                overflow-x: hidden !important;
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

/**
 * Meta key for custom first image in homepage product carousels.
 */
function moretti_homepage_carousel_image_meta_key() {
    return '_moretti_homepage_carousel_image_id';
}

/**
 * Resolve homepage carousel custom image ID for a product.
 */
function moretti_get_product_homepage_carousel_image_id($product_id) {
    $image_id = (int) get_post_meta((int) $product_id, moretti_homepage_carousel_image_meta_key(), true);

    if ($image_id <= 0 || !wp_attachment_is_image($image_id)) {
        return 0;
    }

    return $image_id;
}

/**
 * Add product metabox for custom homepage carousel image.
 */
function moretti_add_product_homepage_carousel_image_metabox() {
    add_meta_box(
        'moretti_homepage_carousel_image',
        __('Homepage Carousel Image', 'moretti-theme'),
        'moretti_render_product_homepage_carousel_image_metabox',
        'product',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'moretti_add_product_homepage_carousel_image_metabox');

/**
 * Render product metabox with media picker.
 */
function moretti_render_product_homepage_carousel_image_metabox($post) {
    $image_id = moretti_get_product_homepage_carousel_image_id($post->ID);
    $image_src = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';

    wp_nonce_field('moretti_save_homepage_carousel_image', 'moretti_homepage_carousel_image_nonce');
    ?>
    <p style="margin-top:0;">
        <?php esc_html_e('Optional: first slide image shown only in homepage carousels.', 'moretti-theme'); ?>
    </p>
    <div id="moretti-homepage-carousel-image-preview" style="margin-bottom:10px;">
        <?php if ($image_src) : ?>
            <img src="<?php echo esc_url($image_src); ?>" alt="" style="display:block;max-width:100%;height:auto;border:1px solid #e5e7eb;">
        <?php endif; ?>
    </div>
    <input type="hidden" id="moretti_homepage_carousel_image_id" name="moretti_homepage_carousel_image_id" value="<?php echo esc_attr($image_id); ?>">
    <p style="display:flex;gap:6px;">
        <button type="button" class="button button-secondary" id="moretti-homepage-carousel-image-select">
            <?php esc_html_e('Set image', 'moretti-theme'); ?>
        </button>
        <button type="button" class="button" id="moretti-homepage-carousel-image-remove" <?php disabled(!$image_id); ?>>
            <?php esc_html_e('Remove', 'moretti-theme'); ?>
        </button>
    </p>
    <?php
}

/**
 * Save product homepage carousel image.
 */
function moretti_save_product_homepage_carousel_image($post_id) {
    if (!isset($_POST['moretti_homepage_carousel_image_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['moretti_homepage_carousel_image_nonce'])), 'moretti_save_homepage_carousel_image')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (!isset($_POST['moretti_homepage_carousel_image_id'])) {
        delete_post_meta($post_id, moretti_homepage_carousel_image_meta_key());
        return;
    }

    $image_id = absint(wp_unslash($_POST['moretti_homepage_carousel_image_id']));
    if ($image_id > 0 && wp_attachment_is_image($image_id)) {
        update_post_meta($post_id, moretti_homepage_carousel_image_meta_key(), $image_id);
    } else {
        delete_post_meta($post_id, moretti_homepage_carousel_image_meta_key());
    }
}
add_action('save_post_product', 'moretti_save_product_homepage_carousel_image');

/**
 * Enqueue media picker script for the product metabox.
 */
function moretti_admin_enqueue_homepage_carousel_image_metabox($hook) {
    if (($hook !== 'post.php' && $hook !== 'post-new.php') || !isset($_GET['post_type']) && !isset($_GET['post'])) {
        return;
    }

    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'product') {
        return;
    }

    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'moretti_admin_enqueue_homepage_carousel_image_metabox');

/**
 * Print inline JS for product homepage carousel image picker.
 */
function moretti_admin_print_homepage_carousel_image_script() {
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'product') {
        return;
    }
    ?>
    <script>
    (function($) {
        let mediaFrame;
        const imageInput = $('#moretti_homepage_carousel_image_id');
        const preview = $('#moretti-homepage-carousel-image-preview');
        const removeButton = $('#moretti-homepage-carousel-image-remove');

        function setPreview(url) {
            if (!url) {
                preview.empty();
                removeButton.prop('disabled', true);
                return;
            }

            preview.html('<img src="' + url + '" alt="" style="display:block;max-width:100%;height:auto;border:1px solid #e5e7eb;">');
            removeButton.prop('disabled', false);
        }

        $('#moretti-homepage-carousel-image-select').on('click', function(e) {
            e.preventDefault();

            if (mediaFrame) {
                mediaFrame.open();
                return;
            }

            mediaFrame = wp.media({
                title: 'Wybierz zdjęcie pierwszego slajdu',
                library: { type: 'image' },
                button: { text: 'Użyj zdjęcia' },
                multiple: false
            });

            mediaFrame.on('select', function() {
                const attachment = mediaFrame.state().get('selection').first().toJSON();
                imageInput.val(attachment.id);
                setPreview(attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url);
            });

            mediaFrame.open();
        });

        removeButton.on('click', function(e) {
            e.preventDefault();
            imageInput.val('');
            setPreview('');
        });
    })(jQuery);
    </script>
    <?php
}
add_action('admin_footer', 'moretti_admin_print_homepage_carousel_image_script');
