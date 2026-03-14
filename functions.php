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

/**
 * Na stronach "Portfele męskie" i "Portfele damskie" pokazuj też produkty z rodzica "Portfele",
 * dopóki nie przypiszesz produktów do tych podkategorii w WooCommerce.
 * WooCommerce na archiwum kategorii używa filtra tax_query, nie pre_get_posts.
 */
function moretti_include_parent_portfele_in_subcategory_archive($tax_query, $query) {
    if (!class_exists('WooCommerce') || is_admin()) {
        return $tax_query;
    }
    if (!is_product_category()) {
        return $tax_query;
    }
    $term = get_queried_object();
    if (!($term instanceof WP_Term) || $term->taxonomy !== 'product_cat') {
        return $tax_query;
    }
    $cat_slug = $term->slug;
    if (!in_array($cat_slug, array('portfele-meskie', 'portfele-damskie'), true)) {
        return $tax_query;
    }
    $parent = get_term_by('slug', 'portfele', 'product_cat');
    if (!$parent || is_wp_error($parent)) {
        return $tax_query;
    }
    return array(
        'relation' => 'OR',
        array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => array($cat_slug),
        ),
        array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => array('portfele'),
        ),
    );
}
add_filter('woocommerce_product_query_tax_query', 'moretti_include_parent_portfele_in_subcategory_archive', 10, 2);

/**
 * Fallback: na archiwum kategorii WooCommerce czasem modyfikuje główne zapytanie w pre_get_posts.
 * Rozszerzamy tax_query o rodzica "portfele" gdy aktualna kategoria to portfele-meskie lub portfele-damskie.
 */
function moretti_pre_get_posts_include_parent_portfele($query) {
    if (!class_exists('WooCommerce') || !$query->is_main_query() || $query->is_admin()) {
        return;
    }
    $tax_query = $query->get('tax_query');
    if (!is_array($tax_query)) {
        return;
    }
    $cat_slug = null;
    foreach ($tax_query as $clause) {
        if (!is_array($clause) || isset($clause['relation'])) {
            continue;
        }
        if (isset($clause['taxonomy']) && $clause['taxonomy'] === 'product_cat' && !empty($clause['terms'])) {
            $terms = is_array($clause['terms']) ? $clause['terms'] : array($clause['terms']);
            foreach ($terms as $t) {
                if (in_array((string) $t, array('portfele-meskie', 'portfele-damskie'), true)) {
                    $cat_slug = (string) $t;
                    break 2;
                }
            }
        }
    }
    if ($cat_slug === null) {
        return;
    }
    $parent = get_term_by('slug', 'portfele', 'product_cat');
    if (!$parent || is_wp_error($parent)) {
        return;
    }
    $query->set('tax_query', array(
        'relation' => 'OR',
        array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => array($cat_slug),
        ),
        array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => array('portfele'),
        ),
    ));
}
add_action('pre_get_posts', 'moretti_pre_get_posts_include_parent_portfele', 999);

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

/**
 * Resolve active WooCommerce attribute taxonomy with safe fallbacks.
 *
 * @param array  $candidates            Candidate taxonomy slugs (e.g. pa_color, pa_kolor).
 * @param string $preferred_term_slug   Optional selected term slug from URL.
 * @param string $preferred_attr_name   Optional Woo attribute name (without pa_ prefix), e.g. color.
 * @return string
 */
function moretti_resolve_attribute_taxonomy(array $candidates, $preferred_term_slug = '', $preferred_attr_name = '') {
    $resolved_taxonomies = array();

    // Canonical Woo attribute taxonomy (if known) should always be preferred first.
    if (
        $preferred_attr_name !== '' &&
        function_exists('wc_attribute_taxonomy_name')
    ) {
        $canonical_taxonomy = wc_attribute_taxonomy_name(sanitize_title($preferred_attr_name));
        if ($canonical_taxonomy && taxonomy_exists($canonical_taxonomy)) {
            $resolved_taxonomies[] = $canonical_taxonomy;
        }
    }

    foreach ($candidates as $taxonomy) {
        if (taxonomy_exists($taxonomy)) {
            $resolved_taxonomies[] = $taxonomy;
        }
    }

    // Fallback by matching candidate names against Woo attribute registry.
    if (function_exists('wc_get_attribute_taxonomies') && function_exists('wc_attribute_taxonomy_name')) {
        $attribute_taxonomies = wc_get_attribute_taxonomies();
        if (!empty($attribute_taxonomies) && !is_wp_error($attribute_taxonomies)) {
            foreach ($attribute_taxonomies as $attribute_taxonomy) {
                if (empty($attribute_taxonomy->attribute_name)) {
                    continue;
                }
                $attribute_name = sanitize_title($attribute_taxonomy->attribute_name);
                foreach ($candidates as $candidate) {
                    $candidate_name = sanitize_title(str_replace('pa_', '', $candidate));
                    if ($attribute_name === $candidate_name) {
                        $resolved = wc_attribute_taxonomy_name($attribute_taxonomy->attribute_name);
                        if (taxonomy_exists($resolved)) {
                            $resolved_taxonomies[] = $resolved;
                        }
                    }
                }
            }
        }
    }

    $resolved_taxonomies = array_values(array_unique(array_filter($resolved_taxonomies)));
    if (empty($resolved_taxonomies)) {
        return '';
    }

    if ($preferred_term_slug !== '') {
        $matched_taxonomy = '';
        $matched_count = -1;
        foreach ($resolved_taxonomies as $taxonomy) {
            $matched_term = get_term_by('slug', $preferred_term_slug, $taxonomy);
            if ($matched_term && !is_wp_error($matched_term)) {
                $term_count = isset($matched_term->count) ? (int) $matched_term->count : 0;
                if ($term_count > $matched_count) {
                    $matched_count = $term_count;
                    $matched_taxonomy = $taxonomy;
                }
            }
        }
        if ($matched_taxonomy !== '') {
            return $matched_taxonomy;
        }
    }

    return $resolved_taxonomies[0];
}

// Helper function to get hex color from name
function moretti_get_color_hex($color_name) {
    $normalized = moretti_normalize_color_key($color_name);
    $color_map = moretti_color_swatch_hex_map();

    if ($normalized !== '' && isset($color_map[$normalized])) {
        return $color_map[$normalized];
    }

    foreach ($color_map as $key => $hex) {
        if ($normalized !== '' && strpos($normalized, $key) !== false) {
            return $hex;
        }
    }

    return '#d1d5db'; // Neutral fallback
}

/**
 * Normalize color key from SKU/term name into slug-like value.
 */
function moretti_normalize_color_key($value) {
    $value = is_string($value) ? $value : '';
    $value = trim($value);
    if ($value === '') {
        return '';
    }

    $value = remove_accents($value);
    $value = strtolower($value);
    $value = str_replace('_', '-', $value);
    $value = preg_replace('/\s+/', '-', $value);
    $value = preg_replace('/[^a-z0-9-]+/', '', $value);
    $value = preg_replace('/-+/', '-', $value);

    return trim((string) $value, '-');
}

/**
 * Fixed color swatch map for current known taxonomy values.
 * Mapped by normalized slug/name instead of term IDs for portability.
 */
function moretti_color_swatch_hex_map() {
    static $map = null;

    if (is_array($map)) {
        return $map;
    }

    $map = array(
        'zielony' => '#1f7a3d',
        'fioletowy' => '#6b46c1',
        'bordowy' => '#6f1d36',
        'jasny-braz' => '#b08457',
        'ciemny-braz' => '#5b3a29',
        'czerwony' => '#c81e1e',
        'czarny' => '#111111',
        'jasny-roz' => '#f4a3c3',
        'szary' => '#7a7a7a',
        'zloty' => '#caa23b',
        'granatowy' => '#1e2a52',
        // Compatibility aliases used in existing data.
        'brazowy' => '#8b5e3c',
        'bezowy' => '#e5d7b8',
        'bialy' => '#ffffff',
        'niebieski' => '#3b82f6',
        'kremowy' => '#f5f3ef',
        'taupe' => '#8f8275',
        'black' => '#111111',
        'brown' => '#8b5e3c',
        'beige' => '#e5d7b8',
        'gray' => '#7a7a7a',
        'grey' => '#7a7a7a',
        'white' => '#ffffff',
        'red' => '#c81e1e',
        'blue' => '#3b82f6',
        'navy' => '#1e2a52',
    );

    return $map;
}

/**
 * Parse SKU into model + color.
 * Rule: model is everything before the last "-", color is everything after.
 *
 * @param string $sku
 * @return array{model:string,color_raw:string,color_slug:string,color_label:string}
 */
function moretti_parse_sku_model_and_color($sku) {
    static $cache = array();

    $sku = is_string($sku) ? trim($sku) : '';
    if ($sku === '') {
        return array(
            'model' => '',
            'color_raw' => '',
            'color_slug' => '',
            'color_label' => '',
        );
    }

    if (isset($cache[$sku])) {
        return $cache[$sku];
    }

    $last_dash_pos = strrpos($sku, '-');
    if ($last_dash_pos === false || $last_dash_pos <= 0 || $last_dash_pos >= (strlen($sku) - 1)) {
        $cache[$sku] = array(
            'model' => '',
            'color_raw' => '',
            'color_slug' => '',
            'color_label' => '',
        );
        return $cache[$sku];
    }

    $model = trim(substr($sku, 0, $last_dash_pos));
    $color_raw = trim(substr($sku, $last_dash_pos + 1));
    $color_label = ucwords(str_replace(array('-', '_'), ' ', $color_raw));
    $color_slug = moretti_normalize_color_key($color_raw);

    $cache[$sku] = array(
        'model' => $model,
        'color_raw' => $color_raw,
        'color_slug' => $color_slug,
        'color_label' => $color_label,
    );

    return $cache[$sku];
}

/**
 * Resolve product color from assigned color taxonomy term.
 *
 * @param int $product_id
 * @return array{color_slug:string,color_label:string}
 */
function moretti_get_product_color_from_taxonomy($product_id) {
    static $cache = array();

    $product_id = (int) $product_id;
    if ($product_id <= 0) {
        return array('color_slug' => '', 'color_label' => '');
    }

    if (isset($cache[$product_id])) {
        return $cache[$product_id];
    }

    $color_taxonomy = moretti_resolve_attribute_taxonomy(array('pa_color', 'pa_kolor', 'pa_colour'), '', 'color');
    if ($color_taxonomy === '' || !taxonomy_exists($color_taxonomy)) {
        $cache[$product_id] = array('color_slug' => '', 'color_label' => '');
        return $cache[$product_id];
    }

    $terms = get_the_terms($product_id, $color_taxonomy);
    if (empty($terms) || is_wp_error($terms)) {
        $cache[$product_id] = array('color_slug' => '', 'color_label' => '');
        return $cache[$product_id];
    }

    $primary_term = reset($terms);
    if (!$primary_term || empty($primary_term->name)) {
        $cache[$product_id] = array('color_slug' => '', 'color_label' => '');
        return $cache[$product_id];
    }

    $cache[$product_id] = array(
        'color_slug' => moretti_normalize_color_key((string) $primary_term->slug !== '' ? $primary_term->slug : $primary_term->name),
        'color_label' => (string) $primary_term->name,
    );

    return $cache[$product_id];
}

/**
 * Atrybut "Strona Główna" (pa_strona-glowna): ukryty na froncie (nawigacja, archiwa),
 * ale z włączonym UI w panelu, żeby można było edytować terminy (np. "tak") i przypisywać je produktom.
 */
add_filter('woocommerce_taxonomy_args_pa_strona-glowna', static function($args) {
    if (!is_array($args)) {
        $args = array();
    }

    $args['public'] = false;
    $args['show_ui'] = true;   // umożliwia edycję terminów w WooCommerce → Atrybuty → Konfiguruj taksonomie
    $args['show_in_nav_menus'] = false;
    $args['show_in_quick_edit'] = false;
    $args['meta_box_cb'] = false;

    return $args;
});

/**
 * Build color variants for product cards based on shared model parsed from SKU.
 *
 * @param int|WC_Product $product_or_id
 * @return array<int,array{id:int,url:string,sku:string,model:string,color_slug:string,color_label:string,color_hex:string,is_current:bool}>
 */
function moretti_get_product_color_variants($product_or_id) {
    static $variants_cache = array();
    static $model_product_ids_cache = array();

    if ($product_or_id instanceof WC_Product) {
        $product = $product_or_id;
    } else {
        $product = wc_get_product((int) $product_or_id);
    }

    if (!$product instanceof WC_Product) {
        return array();
    }

    $product_id = (int) $product->get_id();
    if (isset($variants_cache[$product_id])) {
        return $variants_cache[$product_id];
    }

    $sku = (string) $product->get_sku();
    $parsed = moretti_parse_sku_model_and_color($sku);
    if ($parsed['model'] === '') {
        $variants_cache[$product_id] = array();
        return $variants_cache[$product_id];
    }

    $model = $parsed['model'];
    if (!isset($model_product_ids_cache[$model])) {
        $query = new WP_Query(array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'orderby' => array(
                'menu_order' => 'ASC',
                'title' => 'ASC',
            ),
            'meta_query' => array(
                array(
                    'key' => '_sku',
                    'value' => $model . '-',
                    'compare' => 'LIKE',
                ),
            ),
        ));

        $model_product_ids_cache[$model] = !empty($query->posts) ? array_map('absint', $query->posts) : array();
    }

    $color_map = moretti_color_swatch_hex_map();
    $variants = array();
    $seen_color_slugs = array();
    foreach ($model_product_ids_cache[$model] as $candidate_id) {
        $candidate = wc_get_product($candidate_id);
        if (!$candidate instanceof WC_Product) {
            continue;
        }

        if ($candidate->get_catalog_visibility() === 'hidden') {
            continue;
        }

        $candidate_sku = (string) $candidate->get_sku();
        $candidate_parsed = moretti_parse_sku_model_and_color($candidate_sku);
        if ($candidate_parsed['model'] !== $model) {
            continue;
        }

        // Kropki i kolory działają stricte po atrybucie (taksonomia); meczowanie nadal po SKU (model).
        $taxonomy_color = moretti_get_product_color_from_taxonomy((int) $candidate->get_id());
        if (!empty($taxonomy_color['color_slug']) && isset($color_map[$taxonomy_color['color_slug']])) {
            $resolved_color_slug = $taxonomy_color['color_slug'];
            $resolved_color_label = $taxonomy_color['color_label'] !== '' ? $taxonomy_color['color_label'] : ucwords(str_replace('-', ' ', $resolved_color_slug));
        } else {
            $resolved_color_slug = $candidate_parsed['color_slug'];
            $resolved_color_label = $candidate_parsed['color_label'];
            if ($resolved_color_slug !== '' && !isset($color_map[$resolved_color_slug])) {
                $trimmed_slug = preg_replace('/^[0-9]+-+/', '', $resolved_color_slug);
                if (is_string($trimmed_slug) && $trimmed_slug !== '' && isset($color_map[$trimmed_slug])) {
                    $resolved_color_slug = $trimmed_slug;
                    $resolved_color_label = ucwords(str_replace('-', ' ', $trimmed_slug));
                }
            }
        }

        $candidate_is_current = (int) $candidate->get_id() === $product_id;
        if (isset($seen_color_slugs[$resolved_color_slug]) && !$candidate_is_current) {
            continue;
        }
        $seen_color_slugs[$resolved_color_slug] = true;

        // Pierwsze zdjęcie z karuzeli (indeks 0), nie featured – ta sama kolejność co w gridzie: galeria, potem main
        $gallery_ids = $candidate->get_gallery_image_ids();
        $main_id = $candidate->get_image_id();
        $carousel_order = array();
        if (!empty($gallery_ids)) {
            $carousel_order = array_map('absint', $gallery_ids);
            if ($main_id && !in_array((int) $main_id, $carousel_order)) {
                $carousel_order[] = (int) $main_id;
            }
        } else {
            if ($main_id) {
                $carousel_order[] = (int) $main_id;
            }
        }
        $first_image_id = isset($carousel_order[0]) ? (int) $carousel_order[0] : 0;
        $first_image_url = $first_image_id ? wp_get_attachment_image_url($first_image_id, 'large') : '';
        $first_image_debug = '';
        if (empty($first_image_url)) {
            $gallery_count = is_array($gallery_ids) ? count($gallery_ids) : 0;
            $first_image_debug = 'galeria=' . $gallery_count . ' main_id=' . (int) $main_id . ' first_id=' . $first_image_id;
        }

        $variants[] = array(
            'id' => (int) $candidate->get_id(),
            'url' => (string) get_permalink($candidate->get_id()),
            'sku' => $candidate_sku,
            'model' => $model,
            'color_slug' => $resolved_color_slug,
            'color_label' => $resolved_color_label,
            'color_hex' => moretti_get_color_hex($resolved_color_slug),
            'is_current' => $candidate_is_current,
            'first_image_url' => $first_image_url ? (string) $first_image_url : '',
            'first_image_debug' => $first_image_debug,
        );
    }

    usort($variants, static function ($a, $b) {
        if ($a['is_current'] !== $b['is_current']) {
            return $a['is_current'] ? -1 : 1;
        }
        return strnatcasecmp($a['color_label'], $b['color_label']);
    });

    $variants_cache[$product_id] = $variants;
    return $variants_cache[$product_id];
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

    // Hero slider (Wittchen-style, izolowany .mh2) – tylko na stronie głównej
    if (is_front_page()) {
        wp_enqueue_style(
            'moretti-hero-slider',
            get_template_directory_uri() . '/assets/css/hero-slider.css',
            array('moretti-main-style'),
            filemtime(get_template_directory() . '/assets/css/hero-slider.css')
        );
    }

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

    $wc_ajax_url = '';
    if (class_exists('WC_AJAX')) {
        $wc_ajax_url = WC_AJAX::get_endpoint('%%endpoint%%');
    }

    // Add inline script config
    wp_localize_script('moretti-main-script', 'morettiData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('moretti-nonce'),
        'cartUrl' => wc_get_cart_url(),
        'wcAjaxUrl' => $wc_ajax_url,
    ));
}
add_action('wp_enqueue_scripts', 'moretti_enqueue_assets');

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
 * Ensure legal/footer pages exist for storefront links.
 * Runs once and only creates/publishes missing pages.
 */
function moretti_ensure_legal_pages_exist() {
    if (get_option('moretti_legal_pages_seeded_v5')) {
        return;
    }

    $pages = array(
        'regulamin-sklepu' => array(
            'title' => 'Regulamin sklepu',
            'content' => '
            <div class="moretti-legal-doc">
                <h2>Postanowienia ogólne</h2>
                <p>Ogłoszenia, reklamy, cenniki i inne informacje o produktach podane na stronie Hurtowni internetowej www.portfelland.pl, w szczególności ich opisy, parametry techniczne i użytkowe oraz ceny, stanowią zaproszenie do zawarcia umowy, w rozumieniu art. 71 Kodeksu Cywilnego.</p>
                <p>Hurtownia LIDA NIP 5261119292 REGON 015161906 oferuje sprzedaż hurtową towarów za pośrednictwem strony dla kontrahentów prowadzących działalność gospodarczą.</p>
                <p><strong>Adres firmy:</strong><br>LIDA DARIUSZ CAŁA<br>Nadrzeczna 14<br>GD Hala 5, Box A-07<br>05-552 Wólka Kosowska</p>
                <p>Prezentacja towaru oraz jego ceny za pośrednictwem strony internetowej Hurtowni www.portfelland.pl nie oznacza, że dany towar jest dostępny lub istnieje możliwość realizacji zamówienia i nie może stanowić podstawy do roszczeń względem www.portfelland.pl. Kupujący składając zamówienie za pomocą mechanizmów dostępnych na stronach internetowych Hurtowni www.portfelland.pl, składa ofertę kupna określonego produktu na warunkach podanych w opisie produktu.</p>
                <ol start="4">
                    <li>Minimalna wartość zamówienia hurtowego wynosi 1200 zł netto.</li>
                    <li>W przypadku zakupu detalicznego konsument, który zawarł umowę na odległość lub poza lokalem przedsiębiorstwa, może w terminie 14 dni odstąpić od niej bez podawania przyczyny.</li>
                </ol>

                <h2>Dane osobowe</h2>
                <p>Podane przez Klientów dane osobowe Sprzedawca przetwarza zgodnie z obowiązującymi przepisami prawa, w tym zgodnie z Rozporządzeniem Parlamentu Europejskiego i Rady (UE) 2016/679 z dnia 27 kwietnia 2016 r. w sprawie ochrony osób fizycznych w związku z przetwarzaniem danych osobowych i w sprawie swobodnego przepływu takich danych oraz uchylenia dyrektywy 95/46/WE (ogólne rozporządzenie o ochronie danych) (Dz.U. L 119 z 4.5.2016, dalej: „Rozporządzenie”). W szczególności:</p>
                <p>Sprzedawca zapewnia, aby dane te były:</p>
                <ol type="a">
                    <li>przetwarzane zgodnie z prawem, rzetelnie i w sposób przejrzysty dla Klientów i innych osób, których dane dotyczą;</li>
                    <li>zbierane w konkretnych, wyraźnych i prawnie uzasadnionych celach i nieprzetwarzane dalej w sposób niezgodny z tymi celami;</li>
                    <li>adekwatne, stosowne oraz ograniczone do tego, co niezbędne do celów, w których są przetwarzane;</li>
                    <li>prawidłowe i w razie potrzeby uaktualniane;</li>
                    <li>przechowywane w formie umożliwiającej identyfikację osoby, której dane dotyczą, przez okres nie dłuższy, niż jest to niezbędne do celów, w których dane te są przetwarzane;</li>
                    <li>przetwarzane w sposób zapewniający odpowiednie bezpieczeństwo danych osobowych, w tym ochronę przed niedozwolonym lub niezgodnym z prawem przetwarzaniem oraz przypadkową utratą, zniszczeniem lub uszkodzeniem, za pomocą odpowiednich środków technicznych lub organizacyjnych.</li>
                </ol>
                <p>Sprzedawca stosuje odpowiednie środki techniczne i organizacyjne, zapewniające ochronę przetwarzanych danych osobowych odpowiednią do charakteru, zakresu, kontekstu i celów przetwarzania oraz ryzyka naruszenia praw lub wolności osób fizycznych.</p>
                <p>Sprzedawca zapewnia dostęp do danych osobowych i korzystanie z innych praw Klientom i innym osobom, których dane dotyczą, zgodnie z obowiązującymi w tym zakresie przepisami prawa.</p>
                <p>Podstawą przetwarzania danych osobowych jest zgoda Klientów lub wystąpienie innej przesłanki uprawniającej do przetwarzania danych osobowych według Rozporządzenia.</p>
                <p>Sprzedawca gwarantuje realizację uprawnień osób, których dane osobowe są przetwarzane na zasadach wynikających z odpowiednich przepisów, w tym osobom tym przysługuje:</p>
                <ol>
                    <li>prawo wycofania zgody w sprawie przetwarzania danych osobowych;</li>
                    <li>prawo do informacji dotyczących ich danych osobowych;</li>
                    <li>prawo do kontroli przetwarzania danych, w tym ich uzupełniania, uaktualniania, prostowania, usuwania;</li>
                    <li>prawo do sprzeciwu wobec przetwarzania lub do ograniczenia przetwarzania;</li>
                    <li>prawo do skargi do organu nadzoru i korzystania z innych środków prawnych celem ochrony swoich praw.</li>
                </ol>
                <p>Osoba mająca dostęp do danych osobowych przetwarza je wyłącznie na podstawie upoważnienia Sprzedawcy lub umowy powierzenia przetwarzania danych osobowych i wyłącznie na polecenie Sprzedawcy.</p>
                <p>Sprzedawca zapewnia, że nie udostępniania danych osobowych innym podmiotom aniżeli upoważnionym na podstawie właściwych przepisów prawa, chyba że wymaga tego prawo Unii Europejskiej lub prawo polskie.</p>

                <h2>Własność intelektualna</h2>
                <p>Prawa do Serwisu oraz treści w nim zawartych należą do Sprzedawcy.</p>
                <p>Adres strony, pod którym jest dostępny Sklep, a także zawartość strony internetowej https://portfelland.pl/ stanowią przedmiot prawa autorskiego i są chronione przez prawo autorskie oraz prawo własności intelektualnej.</p>
                <p>Wszystkie logotypy, nazwy własne, projekty graficzne, filmy, teksty, formularze, skrypty, kody źródłowe, hasła, znaki towarowe, znaki serwisowe itp. są znakami zastrzeżonymi i należą do Sprzedawcy, producenta lub dystrybutora Towaru. Pobieranie, kopiowanie, modyfikowanie, reprodukowanie, przesyłanie lub dystrybuowanie jakichkolwiek treści ze strony https://jagar.com.pl/ bez zgody właściciela jest zabronione.</p>

                <h2>Postanowienia końcowe</h2>
                <p>W sprawach nieuregulowanych Regulaminem w stosunkach prawnych z Klientami zastosowanie mają odpowiednie przepisy powszechnie obowiązującego prawa.</p>
                <p>Sprzedawca zastrzega sobie prawo wprowadzania zmian do Regulaminu z zastrzeżeniem, iż do umów zawartych przed zmianą Regulaminu stosuje się wersję Regulaminu obowiązującą w chwili złożenia Zamówienia. W przypadku Przedsiębiorcy uprzywilejowanego na prawach konsumenta konieczna jest uprzednie powiadomienie o zmianie Regulaminu z wyprzedzeniem 14 dni i umożliwienie rozwiązania Umowy w razie braku akceptacji zmian.</p>
                <p>Wszelkie odstępstwa od Regulaminu wymagają formy pisemnej pod rygorem nieważności.</p>
                <ol start="4">
                    <li>Sądem właściwym do rozstrzygnięcia sporu między stronami będzie sąd właściwy według siedziby Sprzedawcy.</li>
                    <li>Sprzedawca LIDA DARIUSZ CAŁA NIP 5261119292 REGON 015161906.</li>
                </ol>
            </div>',
        ),
        'polityka-prywatnosci' => array(
            'title' => 'Polityka prywatności',
            'content' => '
            <div class="moretti-legal-doc">
                <h2>Pliki cookies i podobne technologie</h2>
                <p>Strony internetowe PORTFELLAND wykorzystują tzw. pliki cookies oraz inne technologie działające w analogiczny sposób. Pliki Cookies są plikami testowymi zapisującymi dane poprzez Państwa przeglądarkę na tzw. urządzeniu końcowym (laptopie, smartfonie itp.). Pliki cookies są niezbędne do prawidłowego funkcjonowania serwisów PORTFELLAND, a także zwiększają ich wydajność poprzez zapisywanie informacji o wykorzystywaniu serwisu przez jego użytkowników.</p>

                <h2>Pliki typu Cookies umożliwiają</h2>
                <ul>
                    <li>utrzymanie sesji Klienta (po zalogowaniu), dzięki której Klient nie musi na każdej podstronie serwisu ponownie wpisywać Loginu i Hasła,</li>
                    <li>dostosowanie i optymalizację serwisu do potrzeb Klientów oraz innych osób korzystających z serwisu,</li>
                    <li>tworzenie statystyk oglądalności podstron serwisu,</li>
                    <li>personalizacji przekazów marketingowych,</li>
                    <li>zapewnienie bezpieczeństwa i niezawodności działania serwisu.</li>
                </ul>

                <h2>Rodzaje cookies</h2>
                <p>W serwisie wykorzystywane są Cookies Sesyjne (pliki tymczasowe przechowywane od momentu wejścia do serwisu WITTCHEN do momentu zamknięcia przeglądarki, bądź jej sesji) oraz Cookies Trwałe/Stałe (pliki przechowywane przez dłuższy czas, które ułatwiają korzystanie z serwisu).</p>
                <p>Sklep Internetowy PORTFELLAND.PL znajdujący się pod adresem www.portfelland.com wykorzystuje następujące pliki cookies (zgodnie z poniższym zestawieniem):</p>

                <p>Strony internetowe PORTFELLAND wykorzystują tzw. pliki cookies oraz inne technologie działające w analogiczny sposób. Pliki Cookies są plikami testowymi zapisującymi dane poprzez Państwa przeglądarkę na tzw. urządzeniu końcowym (laptopie, smartfonie itp.). Pliki cookies są niezbędne do prawidłowego funkcjonowania serwisów PORTFELLAND, a także zwiększają ich wydajność poprzez zapisywanie informacji o wykorzystywaniu serwisu przez jego użytkowników.</p>

                <h2>Pliki typu Cookies umożliwiają</h2>
                <ul>
                    <li>utrzymanie sesji Klienta (po zalogowaniu), dzięki której Klient nie musi na każdej podstronie serwisu ponownie wpisywać Loginu i Hasła,</li>
                    <li>dostosowanie i optymalizację serwisu do potrzeb Klientów oraz innych osób korzystających z serwisu,</li>
                    <li>tworzenie statystyk oglądalności podstron serwisu,</li>
                    <li>personalizacji przekazów marketingowych,</li>
                    <li>zapewnienie bezpieczeństwa i niezawodności działania serwisu.</li>
                </ul>

                <p>W serwisie wykorzystywane są Cookies Sesyjne (pliki tymczasowe przechowywane od momentu wejścia do serwisu POERTFELLAND do momentu zamknięcia przeglądarki, bądź jej sesji) oraz Cookies Trwałe/Stałe (pliki przechowywane przez dłuższy czas, które ułatwiają korzystanie z serwisu).</p>
                <p>Sklep Internetowy PORTFELLAND znajdujący się pod adresem www.portfelland.com wykorzystuje następujące pliki cookies (zgodnie z poniższym zestawieniem):</p>

                <h2>Okres przechowywania danych</h2>
                <p>Dane są usuwane w momencie zamknięcia przeglądarki (sesja) i wyszukiwaniach produktów nie póżniej jak 360 dni po czym są usuniete trwale.</p>

                <h2>Aktualizacje dokumentu</h2>
                <p>vvInformujemy, iż w celu zapewnienia bezpieczeństwa Państwa danych osobowych oraz aktualnych i przejrzystych procedur i polityk w LIDA DARIUSZ CAŁA niniejszy dokument będzie regularnie analizowany i zmieniany w związku ze zmianami w powszechnie obowiązujących przepisach prawa oraz wszelkimi działaniami podejmowanymi w celu zapewnienia należytej ochrony Państwa Danych Osobowych.Informujemy, iż w celu zapewnienia bezpieczeństwa Państwa danych osobowych oraz aktualnych i przejrzystych procedur i polityk w LIDA DARIUSZ CAŁA niniejszy dokument będzie regularnie analizowany i zmieniany w związku ze zmianami w powszechnie obowiązujących przepisach prawa oraz wszelkimi działaniami podejmowanymi w celu zapewnienia należytej ochrony Państwa Danych Osobowych..</p>
            </div>',
        ),
        'dostawa-i-platnosci' => array(
            'title' => 'Dostawa i płatności',
            'content' => '
            <p>Wszystkie ceny Towarów podawane w Sklepie są podawane w wartościach netto i brutto w złotych polskich (ceny zawierają podatek VAT). Cena Towaru nie uwzględnia kosztów, o których mowa w punkcie 2 poniżej. Cena Towaru podana w chwili złożenia przez Klienta Zamówienia jest wiążąca dla obu stron.</p>
            <p>Koszty związane z dostawą Towaru (np. transport, dostarczenie, usługi pocztowe) i ewentualne inne koszty ponosi Klient. Wysokość tych kosztów może zależeć od wyboru Klienta co do sposobu dostawy Towaru. Informacja o wysokości tych kosztów jest przekazywana na etapie składania Zamówienia.</p>
            <p>Klient może wybrać formę płatności:</p>
            <ol>
                <li>zapłata przed wysyłką Towaru (przedpłata). Po złożeniu Zamówienia Klient powinien wpłacić/przelać należność na rachunek bankowy Sklepu. Realizacja Zamówienia następuje po zaksięgowaniu wpłaty Klienta na rachunku bankowym Sklepu;</li>
                <li>zapłata przy odbiorze Towaru (za pobraniem) – Klient uiszcza należność bezpośrednio przy odbiorze Towaru. Realizacja zamówienia następuje po przyjęciu Zamówienia.</li>
                <li>zapłata przy osobistym odbiorze Towaru (gotówka lub płatność kartą) – Klient uiszcza należność bezpośrednio przy osobistym odbiorze Towaru w sklepie stacjonarnym Sprzedawcy. Realizacja zamówienia następuje po przyjęciu Zamówienia.</li>
            </ol>
            <p>Na każdy sprzedany Produkt Sklep wystawia dowód zakupu i doręcza go Klientowi.</p>
            <p>Klient zobowiązany jest do zapłaty w terminie 7 dni od dnia zawarcia umowy sprzedaży, o ile wybrany sposób zapłaty nie wymaga zachowania innego terminu.</p>',
        ),
        'pielegnacja-portfela' => array(
            'title' => 'Pielęgnacja portfela',
            'content' => '
            <div class="moretti-legal-doc">
                <h1>Jak dbać o portfel ze skóry naturalnej?</h1>
                <p>Portfel ze skóry naturalnej to dodatek, który przy odpowiedniej pielęgnacji może zachować świetny wygląd przez lata. Naturalna skóra jest trwała, ale wymaga regularnej troski: delikatnego czyszczenia, ochrony przed wilgocią oraz właściwego przechowywania.</p>
                <p>Poniżej znajdziesz praktyczne zasady, które pomogą utrzymać portfel w bardzo dobrej kondycji na co dzień.</p>

                <h2>1. Codzienne użytkowanie</h2>
                <p>Skóra naturalna jest odporna, ale nie jest niezniszczalna. Najczęściej uszkodzenia powstają przez kontakt z twardymi przedmiotami (klucze, metalowe elementy) oraz nadmierne wypychanie portfela.</p>
                <ul>
                    <li>noś portfel oddzielnie od ostrych przedmiotów,</li>
                    <li>nie przeładowuj przegród kartami i paragonami,</li>
                    <li>regularnie usuwaj zbędne rzeczy ze środka, aby uniknąć trwałych odkształceń.</li>
                </ul>

                <h2>2. Usuwanie zabrudzeń</h2>
                <p>Do bieżącego czyszczenia zawsze używaj miękkiej, lekko wilgotnej ściereczki. Silne środki domowe mogą naruszyć strukturę skóry i pozostawić plamy.</p>
                <ul>
                    <li>najpierw usuń kurz i drobny brud suchą mikrofibrą,</li>
                    <li>przy trudniejszych zabrudzeniach zastosuj preparat przeznaczony do konkretnego rodzaju skóry,</li>
                    <li>unikaj mocnego tarcia oraz nadmiaru wody.</li>
                </ul>

                <h2>3. Konserwacja i odżywienie skóry</h2>
                <p>Po oczyszczeniu warto wykonać konserwację, która zabezpieczy powierzchnię i ograniczy przesuszanie materiału.</p>
                <ul>
                    <li>stosuj dedykowane kremy i balsamy do wyrobów skórzanych,</li>
                    <li>nakładaj małą ilość preparatu i poleruj miękką ściereczką,</li>
                    <li>impregnację wykonuj regularnie, najlepiej raz na kilka miesięcy.</li>
                </ul>

                <h2>4. Pielęgnacja zależnie od typu wykończenia</h2>
                <p>Różne rodzaje skór wymagają nieco innego podejścia.</p>
                <ul>
                    <li><strong>Skóra lakierowana:</strong> czyść delikatnie i używaj środków do skór lakierowanych; zwykle nie wymaga intensywnego natłuszczania.</li>
                    <li><strong>Skóra fakturowana (np. saffiano):</strong> zazwyczaj wystarczy przetarcie wilgotną mikrofibrą i okresowe odświeżenie odpowiednim preparatem.</li>
                    <li><strong>Skóra licowa:</strong> dobrze reaguje na regularne kremowanie i lekką impregnację.</li>
                    <li><strong>Skóry o tłoczonej strukturze:</strong> pielęgnuj preparatami dedykowanymi do tego typu materiału, bez agresywnego szorowania.</li>
                </ul>

                <h2>5. Czego unikać</h2>
                <ul>
                    <li>długiego kontaktu z deszczem, śniegiem i silnym słońcem,</li>
                    <li>suszenia na kaloryferze lub przy źródłach ciepła,</li>
                    <li>przypadkowych detergentów i rozpuszczalników.</li>
                </ul>

                <h2>Podsumowanie</h2>
                <p>Najlepsze efekty daje regularna, spokojna pielęgnacja: czyszczenie, odżywienie i ochrona. Dzięki temu portfel ze skóry naturalnej dłużej zachowuje kształt, kolor i elegancki wygląd.</p>
            </div>',
        ),
        'zwroty' => array(
            'title' => 'Zwroty',
            'content' => '<h1>Zwroty</h1><p>Klient będący konsumentem może odstąpić od umowy zawartej na odległość na zasadach wynikających z obowiązujących przepisów prawa.</p>',
        ),
        'reklamacje' => array(
            'title' => 'Reklamacje',
            'content' => '<h1>Reklamacje</h1><p>Reklamacje można zgłaszać drogą mailową na adres kontaktowy sklepu wraz z opisem problemu i danymi zamówienia.</p>',
        ),
    );

    foreach ($pages as $slug => $data) {
        $existing_page = get_page_by_path($slug, OBJECT, 'page');
        if ($existing_page instanceof WP_Post) {
            wp_update_post(array(
                'ID' => $existing_page->ID,
                'post_title' => $data['title'],
                'post_name' => $slug,
                'post_content' => $data['content'],
                'post_status' => 'publish',
            ));
        } else {
            wp_insert_post(array(
                'post_title' => $data['title'],
                'post_name' => $slug,
                'post_content' => $data['content'],
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_author' => 1,
            ));
        }
    }

    // Backward-compatible alias page used by older footer links.
    $legacy_delivery = get_page_by_path('koszty-dostawy', OBJECT, 'page');
    if (!$legacy_delivery instanceof WP_Post) {
        wp_insert_post(array(
            'post_title' => 'Koszty dostawy i metody płatności',
            'post_name' => 'koszty-dostawy',
            'post_content' => '<p>Ta strona została przeniesiona. Aktualna treść: <a href="' . esc_url(home_url('/dostawa-i-platnosci/')) . '">Dostawa i płatności</a>.</p>',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => 1,
        ));
    }

    $cookies_page = get_page_by_path('polityka-plikow-cookies', OBJECT, 'page');
    if ($cookies_page instanceof WP_Post) {
        wp_update_post(array(
            'ID' => $cookies_page->ID,
            'post_status' => 'draft',
        ));
    }

    update_option('moretti_legal_pages_seeded_v5', 1, false);
}
add_action('init', 'moretti_ensure_legal_pages_exist', 25);

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

/**
 * Jednorazowa naprawa widoczności produktów po imporcie CSV (gdy importer ustawił status na Szkic).
 * Wejdź na stronę (np. stronę główną) będąc zalogowanym jako administrator z parametrem:
 *   ?moretti_fix_visibility=1
 * Po wykonaniu nastąpi przekierowanie i komunikat.
 */
add_action('template_redirect', function () {
    if (!isset($_GET['moretti_fix_visibility']) || $_GET['moretti_fix_visibility'] !== '1') {
        return;
    }
    if (!current_user_can('manage_woocommerce')) {
        return;
    }
    if (!class_exists('WooCommerce')) {
        return;
    }

    $ids = get_posts(array(
        'post_type'      => 'product',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ));

    $updated = 0;
    foreach ($ids as $product_id) {
        $post = get_post($product_id);
        $product = wc_get_product($product_id);
        $changed = false;

        if ($post->post_status !== 'publish') {
            wp_update_post(array('ID' => $product_id, 'post_status' => 'publish'));
            $changed = true;
        }

        if ($product && $product->get_catalog_visibility() !== 'visible') {
            $product->set_catalog_visibility('visible');
            $product->save();
            $changed = true;
        }

        if ($changed) {
            $updated++;
        }
    }

    wp_safe_redirect(add_query_arg('moretti_fix_done', $updated, remove_query_arg(array('moretti_fix_visibility', 'moretti_fix_done'))));
    exit;
}, 5);

/**
 * Lista slugów dla CTA banerów hero (atrybuty + kategorie).
 * Jako administrator wejdź na dowolną stronę: ?moretti_hero_slugs=1
 * Skopiuj wyświetlone slugi do hero-banners-config.php.
 */
add_action('template_redirect', function () {
    if (!isset($_GET['moretti_hero_slugs']) || $_GET['moretti_hero_slugs'] !== '1' || !current_user_can('manage_options')) {
        return;
    }
    $path = get_template_directory() . '/scripts/hero-banner-cta-slugs.php';
    if (!is_readable($path)) {
        return;
    }
    header('Content-Type: text/plain; charset=utf-8');
    include $path;
    exit;
}, 5);

/**
 * Przypisanie Kolekcja + Materiał z CSV (bez importera).
 * 1. Skopiuj moretti-migracja-kolekcja-material.csv do theme/scripts/
 * 2. Wejdź: ?moretti_assign_kolekcja_material=1 (jako admin)
 */
add_action('template_redirect', function () {
    if (!isset($_GET['moretti_assign_kolekcja_material']) || $_GET['moretti_assign_kolekcja_material'] !== '1' || !current_user_can('manage_woocommerce')) {
        return;
    }
    $path = get_template_directory() . '/scripts/assign-kolekcja-material-from-csv.php';
    if (!is_readable($path)) {
        return;
    }
    include $path;
    exit;
}, 5);

/**
 * Przypisanie Kolor z CSV (bez importera).
 * 1. Plik import-id-kolor.csv w katalogu głównym motywu.
 * 2. Wejdź: ?moretti_assign_color=1 (jako admin)
 */
add_action('template_redirect', function () {
    if (!isset($_GET['moretti_assign_color']) || $_GET['moretti_assign_color'] !== '1' || !current_user_can('manage_woocommerce')) {
        return;
    }
    $path = get_template_directory() . '/scripts/assign-color-from-csv.php';
    if (!is_readable($path)) {
        return;
    }
    include $path;
    exit;
}, 5);

/**
 * Przywrócenie kategorii Portfele męskie dla produktów 997–1007.
 * Wejdź: ?moretti_restore_portfele_meskie=1 (jako admin)
 */
add_action('template_redirect', function () {
    if (!isset($_GET['moretti_restore_portfele_meskie']) || $_GET['moretti_restore_portfele_meskie'] !== '1' || !current_user_can('manage_woocommerce')) {
        return;
    }
    $path = get_template_directory() . '/scripts/restore-portfele-meskie.php';
    if (!is_readable($path)) {
        return;
    }
    include $path;
    exit;
}, 5);

add_action('wp_footer', function () {
    if (!current_user_can('manage_woocommerce')) {
        return;
    }
    if (isset($_GET['moretti_fix_done'])) {
        $n = (int) $_GET['moretti_fix_done'];
        echo '<script>alert("Naprawa widoczności: zaktualizowano ' . $n . ' produktów. Odśwież sklep.");</script>';
    }
    if (isset($_GET['moretti_assign_done'])) {
        $n = (int) $_GET['moretti_assign_done'];
        $e = (int) ($_GET['moretti_assign_errors'] ?? 0);
        $msg = "Kolekcja/Materiał z CSV: zaktualizowano {$n} produktów.";
        if ($e > 0) {
            $msg .= " Błędów: {$e} (sprawdź konsolę lub logi).";
        }
        $msg .= " Odśwież stronę atrybutów (Liczba) i sklep.";
        echo '<script>alert("' . esc_js($msg) . '");</script>';
    }
    if (isset($_GET['moretti_restore_meskie_done'])) {
        $n = (int) $_GET['moretti_restore_meskie_done'];
        echo '<script>alert("Portfele męskie: przypisano kategorię do ' . $n . ' produktów. Odśwież stronę kategorii.");</script>';
    }
    if (isset($_GET['moretti_assign_color_done'])) {
        $n = (int) $_GET['moretti_assign_color_done'];
        $e = (int) ($_GET['moretti_assign_color_errors'] ?? 0);
        $msg = "Kolor z CSV: zaktualizowano {$n} produktów.";
        if ($e > 0) {
            $msg .= " Błędów: {$e}.";
        }
        echo '<script>alert("' . esc_js($msg) . '");</script>';
    }
}, 20);
