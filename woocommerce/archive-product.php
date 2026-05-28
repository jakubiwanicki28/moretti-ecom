<?php
/**
 * Shop Page - Clean Custom Template
 * Zero WooCommerce hooks, full design control
 * 
 * @package Moretti
 */

defined('ABSPATH') || exit;

get_header();

$requested_color_slug = '';
if (isset($_GET['filter_color'])) {
    $requested_color_slug = sanitize_title(wp_unslash($_GET['filter_color']));
} elseif (isset($_GET['filter_kolor'])) {
    $requested_color_slug = sanitize_title(wp_unslash($_GET['filter_kolor']));
}

$color_taxonomy = function_exists('moretti_resolve_attribute_taxonomy')
    ? moretti_resolve_attribute_taxonomy(array('pa_color', 'pa_kolor', 'pa_colour'), $requested_color_slug, 'color')
    : 'pa_color';
$material_taxonomy = function_exists('moretti_resolve_attribute_taxonomy')
    ? moretti_resolve_attribute_taxonomy(array('pa_material', 'pa_materiał', 'pa_materials', 'pa_materiaal'), '', 'material')
    : 'pa_material';
$size_taxonomy = function_exists('moretti_resolve_attribute_taxonomy')
    ? moretti_resolve_attribute_taxonomy(array('pa_wielkosc', 'pa_size', 'pa_rozmiar'), '', 'wielkosc')
    : 'pa_wielkosc';
$kolekcja_taxonomy = function_exists('moretti_resolve_attribute_taxonomy')
    ? moretti_resolve_attribute_taxonomy(array('pa_kolekcja'), '', 'kolekcja')
    : 'pa_kolekcja';

$moretti_get_filter_terms = static function ($taxonomy) {
    if (!$taxonomy || !taxonomy_exists($taxonomy)) {
        return array();
    }

    $terms = get_terms(array(
        'taxonomy' => $taxonomy,
        'hide_empty' => true,
    ));

    if (is_wp_error($terms) || empty($terms)) {
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ));
    }

    return is_wp_error($terms) ? array() : $terms;
};

$selected_color = $requested_color_slug;

$selected_material = isset($_GET['filter_material']) ? sanitize_title(wp_unslash($_GET['filter_material'])) : '';
$selected_size = isset($_GET['filter_size']) ? sanitize_title(wp_unslash($_GET['filter_size'])) : '';
$selected_kolekcja = isset($_GET['filter_kolekcja']) ? sanitize_title(wp_unslash($_GET['filter_kolekcja'])) : '';
$exclude_kolekcja = isset($_GET['exclude_kolekcja']) ? sanitize_title(wp_unslash($_GET['exclude_kolekcja'])) : '';
$is_wishlist_view = isset($_GET['wishlist']) && '1' === sanitize_text_field(wp_unslash($_GET['wishlist']));

$material_filter_taxonomy = $material_taxonomy;
$materials = $moretti_get_filter_terms($material_filter_taxonomy);

// Legacy fallback: in some catalogs material-like values live in product categories.
if (count($materials) <= 1) {
    $legacy_material_terms = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'orderby' => 'name',
        'order' => 'ASC',
    ));

    if (!is_wp_error($legacy_material_terms) && !empty($legacy_material_terms)) {
        $legacy_material_keywords = array('skora', 'wzor', 'wizytownik', 'material');
        $legacy_filtered_terms = array();

        foreach ($legacy_material_terms as $legacy_term) {
            $haystack = sanitize_title(remove_accents($legacy_term->name . ' ' . $legacy_term->slug));
            foreach ($legacy_material_keywords as $keyword) {
                if (strpos($haystack, $keyword) !== false) {
                    $legacy_filtered_terms[] = $legacy_term;
                    break;
                }
            }
        }

        if (count($legacy_filtered_terms) >= 2) {
            $material_filter_taxonomy = 'product_cat';
            $materials = $legacy_filtered_terms;
        }
    }
}

$selected_material_taxonomy = '';
if ($selected_material !== '') {
    $material_lookup_taxonomies = array_values(array_unique(array_filter(array(
        $material_filter_taxonomy,
        $material_taxonomy,
        'pa_material',
        'pa_materiał',
        'pa_materials',
        'pa_materiaal',
        'product_cat',
    ))));

    foreach ($material_lookup_taxonomies as $lookup_taxonomy) {
        if (!taxonomy_exists($lookup_taxonomy)) {
            continue;
        }
        $selected_material_term = get_term_by('slug', $selected_material, $lookup_taxonomy);
        if ($selected_material_term && !is_wp_error($selected_material_term)) {
            $selected_material_taxonomy = $lookup_taxonomy;
            break;
        }
    }
}

$moretti_current_query_args = array();
if (isset($_GET['s']) && $_GET['s'] !== '') {
    $moretti_current_query_args['s'] = sanitize_text_field(wp_unslash($_GET['s']));
}
if ($selected_color !== '') {
    $moretti_current_query_args['filter_color'] = $selected_color;
}
if ($selected_material !== '') {
    $moretti_current_query_args['filter_material'] = $selected_material;
}
if ($selected_size !== '') {
    $moretti_current_query_args['filter_size'] = $selected_size;
}
if ($selected_kolekcja !== '') {
    $moretti_current_query_args['filter_kolekcja'] = $selected_kolekcja;
}
if ($exclude_kolekcja !== '') {
    $moretti_current_query_args['exclude_kolekcja'] = $exclude_kolekcja;
}
if (isset($_GET['min_price']) && $_GET['min_price'] !== '') {
    $moretti_current_query_args['min_price'] = sanitize_text_field(wp_unslash($_GET['min_price']));
}
if (isset($_GET['max_price']) && $_GET['max_price'] !== '') {
    $moretti_current_query_args['max_price'] = sanitize_text_field(wp_unslash($_GET['max_price']));
}
if (isset($_GET['orderby']) && $_GET['orderby'] !== '') {
    $moretti_current_query_args['orderby'] = sanitize_text_field(wp_unslash($_GET['orderby']));
}
if ($is_wishlist_view) {
    $moretti_current_query_args['wishlist'] = '1';
}
if (isset($_GET['post_type']) && $_GET['post_type'] !== '') {
    $moretti_current_query_args['post_type'] = sanitize_text_field(wp_unslash($_GET['post_type']));
}

$moretti_known_query_args = array(
    's',
    'post_type',
    'filter_color',
    'filter_kolor',
    'filter_material',
    'filter_size',
    'filter_kolekcja',
    'exclude_kolekcja',
    'min_price',
    'max_price',
    'orderby',
    'wishlist',
);

$moretti_build_paged_shop_url = static function ($page = 1) use ($moretti_current_query_args) {
    $page = max(1, (int) $page);
    $base_url = get_pagenum_link($page);
    return !empty($moretti_current_query_args) ? add_query_arg($moretti_current_query_args, $base_url) : $base_url;
};

$moretti_build_shop_url = static function ($overrides = array()) use ($moretti_current_query_args, $moretti_known_query_args) {
    $args = array_merge($moretti_current_query_args, $overrides);

    foreach ($args as $key => $value) {
        if ($value === false || $value === null || $value === '') {
            unset($args[$key]);
        }
    }

    $base_url = remove_query_arg($moretti_known_query_args, get_pagenum_link(1));
    return !empty($args) ? add_query_arg($args, $base_url) : $base_url;
};

$selected_color_label = $selected_color;
if ($selected_color !== '') {
    $color_taxonomies_for_lookup = array_values(array_unique(array_filter(array(
        $color_taxonomy,
        'pa_color',
        'pa_kolor',
        'pa_colour',
    ))));

    foreach ($color_taxonomies_for_lookup as $lookup_taxonomy) {
        if (!taxonomy_exists($lookup_taxonomy)) {
            continue;
        }
        $selected_color_term = get_term_by('slug', $selected_color, $lookup_taxonomy);
        if ($selected_color_term && !is_wp_error($selected_color_term) && !empty($selected_color_term->name)) {
            $selected_color_label = $selected_color_term->name;
            break;
        }
    }

    if ($selected_color_label === $selected_color) {
        // Final safety fallback: avoid displaying raw slug in UI.
        $selected_color_label = ucwords(str_replace('-', ' ', $selected_color));
    }
}

$selected_material_label = $selected_material;
if ($selected_material !== '') {
    $material_label_lookup_taxonomies = array_values(array_unique(array_filter(array(
        $selected_material_taxonomy,
        $material_filter_taxonomy,
        $material_taxonomy,
        'product_cat',
    ))));

    foreach ($material_label_lookup_taxonomies as $lookup_taxonomy) {
        if (!taxonomy_exists($lookup_taxonomy)) {
            continue;
        }
        $selected_material_term = get_term_by('slug', $selected_material, $lookup_taxonomy);
        if ($selected_material_term && !is_wp_error($selected_material_term) && !empty($selected_material_term->name)) {
            $selected_material_label = $selected_material_term->name;
            break;
        }
    }

    if ($selected_material_label === $selected_material) {
        $selected_material_label = ucwords(str_replace('-', ' ', $selected_material));
    }
}

// On category archive pages, hide/disable category-level extra filter layer.
if (is_product_category()) {
    $selected_size = '';
}

$wishlist_ids = array();
if ($is_wishlist_view && isset($_COOKIE['moretti_wishlist'])) {
    $raw_cookie = rawurldecode(wp_unslash($_COOKIE['moretti_wishlist']));
    $decoded = json_decode($raw_cookie, true);
    if (is_array($decoded)) {
        $wishlist_ids = array_values(array_unique(array_filter(array_map('absint', $decoded))));
    }
}

// Get all products
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
    'post_type' => 'product',
    'posts_per_page' => 30,
    'paged' => $paged,
    'post_status' => 'publish',
);

// Search: use WP_Query 's' parameter so posts_search filters (SKU, excerpt) can extend it.
$is_search_view = is_search() && !empty(get_search_query()) && !$is_wishlist_view;
if ($is_search_view) {
    $args['s'] = get_search_query();
}

if ($is_wishlist_view) {
    $args['post__in'] = !empty($wishlist_ids) ? $wishlist_ids : array(0);
    $args['orderby'] = 'post__in';
}

// Initialize tax_query
$tax_query = array('relation' => 'AND');

// Virtual subcategory support: on parent category pages (e.g. "dla-niej"),
// include products from all virtual children instead of just the parent slug.
$moretti_virtual_subcategories = function_exists('moretti_get_virtual_subcategories')
    ? moretti_get_virtual_subcategories()
    : array();
$moretti_queried_cat = is_product_category() ? get_queried_object() : null;
$moretti_current_cat_slug = ($moretti_queried_cat instanceof WP_Term) ? $moretti_queried_cat->slug : '';
$moretti_has_virtual_children = isset($moretti_virtual_subcategories[$moretti_current_cat_slug]);
$moretti_virtual_children_terms = array();

if ($moretti_has_virtual_children) {
    foreach ($moretti_virtual_subcategories[$moretti_current_cat_slug] as $child_slug) {
        $child_term = get_term_by('slug', $child_slug, 'product_cat');
        if ($child_term && !is_wp_error($child_term)) {
            $moretti_virtual_children_terms[] = $child_term;
        }
    }
}

// Handle category filter
if (is_product_category()) {
    if ($moretti_has_virtual_children && !empty($moretti_virtual_children_terms)) {
        // Parent category with virtual children: include products from all children.
        $child_slugs = array_map(static function($t) { return $t->slug; }, $moretti_virtual_children_terms);
        $child_slugs[] = $moretti_current_cat_slug; // also include direct parent products
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field' => 'slug',
            'terms' => $child_slugs,
        );
    } else {
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field' => 'slug',
            'terms' => $moretti_current_cat_slug,
        );
    }
}

// Handle attribute filters
if (!$is_wishlist_view && $color_taxonomy && $selected_color !== '') {
    $tax_query[] = array(
        'taxonomy' => $color_taxonomy,
        'field' => 'slug',
        'terms' => $selected_color,
    );
}

if (!$is_wishlist_view && $selected_material !== '') {
    $active_material_taxonomy = $selected_material_taxonomy !== '' ? $selected_material_taxonomy : $material_filter_taxonomy;
    if (!$active_material_taxonomy || !taxonomy_exists($active_material_taxonomy)) {
        $active_material_taxonomy = $material_taxonomy;
    }
    if ($active_material_taxonomy && taxonomy_exists($active_material_taxonomy)) {
        $tax_query[] = array(
            'taxonomy' => $active_material_taxonomy,
            'field' => 'slug',
            'terms' => $selected_material,
        );
    }
}

if (!$is_wishlist_view && $size_taxonomy && $selected_size !== '') {
    $tax_query[] = array(
        'taxonomy' => $size_taxonomy,
        'field' => 'slug',
        'terms' => $selected_size,
    );
}

if (!$is_wishlist_view && $kolekcja_taxonomy && taxonomy_exists($kolekcja_taxonomy) && $selected_kolekcja !== '') {
    $tax_query[] = array(
        'taxonomy' => $kolekcja_taxonomy,
        'field' => 'slug',
        'terms' => $selected_kolekcja,
    );
}

if (!$is_wishlist_view && $kolekcja_taxonomy && taxonomy_exists($kolekcja_taxonomy) && $exclude_kolekcja !== '') {
    $tax_query[] = array(
        'taxonomy' => $kolekcja_taxonomy,
        'field' => 'slug',
        'operator' => 'NOT IN',
        'terms' => array($exclude_kolekcja),
    );
}

if (count($tax_query) > 1) {
    $args['tax_query'] = $tax_query;
}

// Handle price filter
if (!$is_wishlist_view && (!empty($_GET['min_price']) || !empty($_GET['max_price']))) {
    $args['meta_query'] = array('relation' => 'AND');
    
    if (!empty($_GET['min_price'])) {
        $args['meta_query'][] = array(
            'key' => '_price',
            'value' => floatval($_GET['min_price']),
            'compare' => '>=',
            'type' => 'NUMERIC',
        );
    }
    
    if (!empty($_GET['max_price'])) {
        $args['meta_query'][] = array(
            'key' => '_price',
            'value' => floatval($_GET['max_price']),
            'compare' => '<=',
            'type' => 'NUMERIC',
        );
    }
}

// Handle sorting
$orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'menu_order';
switch ($orderby) {
    case 'popularity':
        $args['meta_key'] = 'total_sales';
        $args['orderby'] = 'meta_value_num';
        break;
    case 'date':
        $args['orderby'] = 'date';
        $args['order'] = 'DESC';
        break;
    case 'price':
        $args['meta_key'] = '_price';
        $args['orderby'] = 'meta_value_num';
        $args['order'] = 'ASC';
        break;
    case 'price-desc':
        $args['meta_key'] = '_price';
        $args['orderby'] = 'meta_value_num';
        $args['order'] = 'DESC';
        break;
    default:
        $args['orderby'] = 'menu_order';
        $args['order'] = 'ASC';
}

$products = new WP_Query($args);

// Get categories
$categories = get_terms(array(
    'taxonomy' => 'product_cat',
    'hide_empty' => true,
    'exclude' => array(get_option('default_product_cat')),
));

// Page title
$page_title = 'Sklep';
if (isset($_GET['s']) && $_GET['s'] !== '') {
    $page_title = sprintf('Wyniki dla: "%s"', esc_html(sanitize_text_field(wp_unslash($_GET['s']))));
} elseif (is_product_category()) {
    $page_title = single_cat_title('', false);
} elseif ($is_wishlist_view) {
    $page_title = 'Ulubione';
}

// View logic:
// - Category archive pages should hide category-level filter duplication.
$is_shop_root_view = is_shop() && !is_product_category() && !$is_wishlist_view;
$show_category_filter = $is_shop_root_view;

// Dynamic price bounds for "Cena" slider filter.
$price_floor = 0;
$price_ceil = 2000;
if (isset($GLOBALS['wpdb']) && $GLOBALS['wpdb'] instanceof wpdb) {
    $price_bounds = $GLOBALS['wpdb']->get_row(
        "SELECT
            MIN(CAST(pm.meta_value AS DECIMAL(10,2))) AS min_price,
            MAX(CAST(pm.meta_value AS DECIMAL(10,2))) AS max_price
         FROM {$GLOBALS['wpdb']->postmeta} pm
         INNER JOIN {$GLOBALS['wpdb']->posts} p ON p.ID = pm.post_id
         WHERE pm.meta_key = '_price'
           AND pm.meta_value <> ''
           AND p.post_type = 'product'
           AND p.post_status = 'publish'",
        ARRAY_A
    );

    if (is_array($price_bounds)) {
        $db_min = isset($price_bounds['min_price']) ? (float) $price_bounds['min_price'] : 0.0;
        $db_max = isset($price_bounds['max_price']) ? (float) $price_bounds['max_price'] : 0.0;
        if ($db_max > 0) {
            $price_floor = (int) floor($db_min);
            $price_ceil = (int) ceil($db_max);
        }
    }
}

if ($price_ceil <= $price_floor) {
    $price_ceil = $price_floor + 100;
}

$selected_min_price = isset($_GET['min_price']) && $_GET['min_price'] !== ''
    ? (int) floor((float) wp_unslash($_GET['min_price']))
    : $price_floor;
$selected_max_price = isset($_GET['max_price']) && $_GET['max_price'] !== ''
    ? (int) ceil((float) wp_unslash($_GET['max_price']))
    : $price_ceil;

$selected_min_price = max($price_floor, min($selected_min_price, $price_ceil));
$selected_max_price = max($price_floor, min($selected_max_price, $price_ceil));
if ($selected_min_price > $selected_max_price) {
    $tmp_price = $selected_min_price;
    $selected_min_price = $selected_max_price;
    $selected_max_price = $tmp_price;
}

$price_summary_label = 'Cena';
if (isset($_GET['min_price']) || isset($_GET['max_price'])) {
    $price_summary_label = sprintf(
        'Cena: %s-%s zł',
        number_format_i18n($selected_min_price, 0),
        number_format_i18n($selected_max_price, 0)
    );
}

?>

<div class="shop-page shop-page-wittchen">
    <div class="shop-container">
        
        <!-- Sidebar -->
        <aside class="shop-sidebar" id="shop-sidebar">
            
            <!-- Mobile Header with Close Button -->
            <div class="sidebar-mobile-header">
                <h2 class="sidebar-mobile-title">Filtry</h2>
                <button id="sidebar-close" class="sidebar-close-btn" aria-label="Zamknij filtry">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="shop-sidebar-content">
                <!-- Categories -->
            <div class="sidebar-block">
                <h3 class="sidebar-heading">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    Kategorie
                </h3>
                <nav class="sidebar-nav">
                    <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" 
                       class="cat-pill <?php echo !is_product_category() ? 'active' : ''; ?>">
                        Wszystko
                        <span class="cat-count"><?php echo wp_count_posts('product')->publish; ?></span>
                    </a>
                    <?php foreach ($categories as $cat) : ?>
                        <a href="<?php echo esc_url(get_term_link($cat)); ?>" 
                           class="cat-pill <?php echo is_product_category($cat->slug) ? 'active' : ''; ?>">
                            <?php echo esc_html($cat->name); ?>
                            <span class="cat-count"><?php echo $cat->count; ?></span>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>

            <!-- Color Filter -->
            <?php
            $colors = $moretti_get_filter_terms($color_taxonomy);
            if (!empty($colors) && !is_wp_error($colors)) :
            ?>
            <div class="sidebar-block">
                <h3 class="sidebar-heading">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                    </svg>
                    Kolor
                </h3>
                <div class="filter-options">
                    <?php foreach ($colors as $color) : 
                        $is_active = ($selected_color === $color->slug);
                    ?>
                        <a href="<?php echo $is_active ? esc_url($moretti_build_shop_url(array('filter_color' => false, 'filter_kolor' => false))) : esc_url($moretti_build_shop_url(array('filter_color' => $color->slug, 'filter_kolor' => false))); ?>" 
                           class="filter-option <?php echo $is_active ? 'active' : ''; ?>">
                            <?php echo esc_html($color->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Material Filter -->
            <?php
            if (!empty($materials) && !is_wp_error($materials)) :
            ?>
            <div class="sidebar-block">
                <h3 class="sidebar-heading">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                    Materiał
                </h3>
                <div class="filter-options">
                    <?php foreach ($materials as $material) :
                        $is_active = ($selected_material === $material->slug);
                    ?>
                        <a href="<?php echo $is_active ? esc_url($moretti_build_shop_url(array('filter_material' => false))) : esc_url($moretti_build_shop_url(array('filter_material' => $material->slug))); ?>" 
                           class="filter-option <?php echo $is_active ? 'active' : ''; ?>">
                            <?php echo esc_html($material->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Size Filter -->
            <?php
            $sizes = $moretti_get_filter_terms($size_taxonomy);
            if (!empty($sizes) && !is_wp_error($sizes)) :
            ?>
            <div class="sidebar-block">
                <h3 class="sidebar-heading">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                    </svg>
                    Wielkość
                </h3>
                <div class="filter-options filter-sizes">
                    <?php foreach ($sizes as $size) :
                        $is_active = ($selected_size === $size->slug);
                    ?>
                        <a href="<?php echo $is_active ? esc_url($moretti_build_shop_url(array('filter_size' => false))) : esc_url($moretti_build_shop_url(array('filter_size' => $size->slug))); ?>" 
                           class="size-option <?php echo $is_active ? 'active' : ''; ?>">
                            <?php echo esc_html($size->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Price Filter -->
            <div class="sidebar-block">
                <h3 class="sidebar-heading">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Cena (PLN)
                </h3>
                <form method="get" action="<?php echo esc_url(get_pagenum_link(1)); ?>" class="price-form">
                    <?php if ($selected_color !== '') : ?>
                        <input type="hidden" name="filter_color" value="<?php echo esc_attr($selected_color); ?>">
                    <?php endif; ?>
                    <?php if ($selected_material !== '') : ?>
                        <input type="hidden" name="filter_material" value="<?php echo esc_attr($selected_material); ?>">
                    <?php endif; ?>
                    <?php if ($selected_size !== '') : ?>
                        <input type="hidden" name="filter_size" value="<?php echo esc_attr($selected_size); ?>">
                    <?php endif; ?>
                    <?php if ($selected_kolekcja !== '') : ?>
                        <input type="hidden" name="filter_kolekcja" value="<?php echo esc_attr($selected_kolekcja); ?>">
                    <?php endif; ?>
                    <?php if ($exclude_kolekcja !== '') : ?>
                        <input type="hidden" name="exclude_kolekcja" value="<?php echo esc_attr($exclude_kolekcja); ?>">
                    <?php endif; ?>
                    <?php if (!empty($orderby)) : ?>
                        <input type="hidden" name="orderby" value="<?php echo esc_attr($orderby); ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['s']) && $_GET['s'] !== '') : ?>
                        <input type="hidden" name="s" value="<?php echo esc_attr(sanitize_text_field(wp_unslash($_GET['s']))); ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['post_type']) && $_GET['post_type'] !== '') : ?>
                        <input type="hidden" name="post_type" value="<?php echo esc_attr(sanitize_text_field(wp_unslash($_GET['post_type']))); ?>">
                    <?php endif; ?>
                    <div class="price-range">
                        <input type="number" name="min_price" placeholder="Od" class="price-input"
                               value="<?php echo isset($_GET['min_price']) ? esc_attr($_GET['min_price']) : ''; ?>">
                        <span class="price-dash">—</span>
                        <input type="number" name="max_price" placeholder="Do" class="price-input"
                               value="<?php echo isset($_GET['max_price']) ? esc_attr($_GET['max_price']) : ''; ?>">
                    </div>
                    <button type="submit" class="filter-btn">Zastosuj</button>
                </form>
            </div>

            <!-- Clear Filters -->
            <?php if ($selected_color !== '' || $selected_material !== '' || $selected_size !== '' || $selected_kolekcja !== '' || $exclude_kolekcja !== '' || !empty($_GET['min_price']) || !empty($_GET['max_price'])) : ?>
            <div class="sidebar-block">
                <a href="<?php echo esc_url($moretti_build_shop_url(array('filter_color' => false, 'filter_kolor' => false, 'filter_material' => false, 'filter_size' => false, 'filter_kolekcja' => false, 'exclude_kolekcja' => false, 'min_price' => false, 'max_price' => false))); ?>" class="clear-all-btn">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Wyczyść
                </a>
            </div>
            <?php endif; ?>

            </div> <!-- End shop-sidebar-content -->
        </aside>

        <!-- Main Content -->
        <main class="shop-main">
            
            <!-- Breadcrumbs -->
            <nav class="shop-breadcrumbs">
                <a href="<?php echo esc_url(home_url('/')); ?>">Start</a>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <?php if (is_product_category()) : ?>
                    <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">Sklep</a>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="current"><?php echo esc_html($page_title); ?></span>
                <?php elseif ($is_wishlist_view) : ?>
                    <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">Sklep</a>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="current">Ulubione</span>
                <?php else : ?>
                    <span class="current">Sklep</span>
                <?php endif; ?>
            </nav>
            
            <!-- Top Bar -->
            <?php
            $sort_options = array(
                'menu_order' => 'Sortuj',
                'popularity' => 'Popularność',
                'date' => 'Nowości',
                'price' => 'Cena: rosnąco',
                'price-desc' => 'Cena: malejąco',
            );
            ?>
            <div class="shop-topbar">
                <div class="shop-title-wrap">
                    <h1 class="shop-title"><?php echo esc_html($page_title); ?></h1>
                </div>
            </div>

            <?php if ($moretti_has_virtual_children && !empty($moretti_virtual_children_terms)) : ?>
            <div class="moretti-subcategory-grid" data-count="<?php echo count($moretti_virtual_children_terms); ?>">
                <?php foreach ($moretti_virtual_children_terms as $sub_term) :
                    $sub_link = get_term_link($sub_term);
                    if (is_wp_error($sub_link)) continue;

                    // Fetch up to 3 random product images from this category for slideshow.
                    $slide_images = array();
                    $slide_products = get_posts(array(
                        'post_type'      => 'product',
                        'posts_per_page' => 3,
                        'post_status'    => 'publish',
                        'orderby'        => 'rand',
                        'fields'         => 'ids',
                        'tax_query'      => array(array(
                            'taxonomy' => 'product_cat',
                            'field'    => 'term_id',
                            'terms'    => $sub_term->term_id,
                        )),
                    ));
                    foreach ($slide_products as $slide_pid) {
                        $img_id = get_post_thumbnail_id($slide_pid);
                        if ($img_id) {
                            $img_url = wp_get_attachment_image_url($img_id, 'medium_large');
                            if ($img_url) {
                                $slide_images[] = $img_url;
                            }
                        }
                    }

                    // Fallback: category thumbnail → placeholder.
                    if (empty($slide_images)) {
                        $thumb_id = get_term_meta($sub_term->term_id, 'thumbnail_id', true);
                        if ($thumb_id) {
                            $thumb_url = wp_get_attachment_image_url($thumb_id, 'medium_large');
                            if ($thumb_url) {
                                $slide_images[] = $thumb_url;
                            }
                        }
                    }
                ?>
                    <a href="<?php echo esc_url($sub_link); ?>" class="moretti-subcategory-tile">
                        <div class="moretti-subcategory-tile-image"<?php echo count($slide_images) > 1 ? ' data-slideshow' : ''; ?>>
                            <?php if (!empty($slide_images)) : ?>
                                <?php foreach ($slide_images as $i => $slide_url) : ?>
                                    <img
                                        class="moretti-subcategory-slide<?php echo $i === 0 ? ' is-active' : ''; ?>"
                                        src="<?php echo esc_url($slide_url); ?>"
                                        alt="<?php echo esc_attr($sub_term->name); ?>"
                                        <?php echo $i === 0 ? '' : 'loading="lazy"'; ?>
                                    />
                                <?php endforeach; ?>
                            <?php else : ?>
                                <div class="moretti-subcategory-tile-placeholder">
                                    <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        <span class="moretti-subcategory-tile-label"><?php echo esc_html($sub_term->name); ?></span>
                        <?php if ($sub_term->count > 0) : ?>
                            <span class="moretti-subcategory-tile-count"><?php
                                $n = (int) $sub_term->count;
                                echo $n . ' ';
                                if ($n === 1) {
                                    echo 'produkt';
                                } elseif ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 12 || $n % 100 > 14)) {
                                    echo 'produkty';
                                } else {
                                    echo 'produktów';
                                }
                            ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!$moretti_has_virtual_children) : ?>

            <?php if (!$is_search_view) : ?>
            <div class="shop-hero-banner">
                <div class="shop-hero-banner-content">
                    <h2 class="shop-hero-title">Torba prezentowa gratis</h2>
                    <p class="shop-hero-subtitle">Do każdego zamówienia dołączamy elegancką torbę prezentową.</p>
                    <p class="shop-hero-copy">Bez warunków i limitów - Twój zakup jest zawsze gotowy do podarowania.</p>
                </div>
                <div class="shop-hero-banner-visual" aria-hidden="true">
                    <svg class="shop-hero-ribbon" viewBox="0 0 100 115" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- bag handle -->
                        <path d="M32 44 Q30 18 50 14 Q70 18 68 44" fill="none" stroke="rgba(255,255,255,0.28)" stroke-width="2.5" stroke-linecap="round"/>
                        <!-- bag body -->
                        <rect x="8" y="44" width="84" height="63" rx="4" fill="rgba(255,255,255,0.10)" stroke="rgba(255,255,255,0.22)" stroke-width="1.5"/>
                        <!-- vertical ribbon -->
                        <rect x="43" y="44" width="14" height="63" fill="rgba(255,255,255,0.07)"/>
                        <!-- horizontal ribbon -->
                        <rect x="8" y="66" width="84" height="12" fill="rgba(255,255,255,0.07)"/>
                        <!-- bow left loop -->
                        <path d="M38 44 C18 26 12 38 24 44 C30 47 38 44 38 44Z" fill="rgba(255,255,255,0.16)" stroke="rgba(255,255,255,0.32)" stroke-width="1.2"/>
                        <!-- bow right loop -->
                        <path d="M62 44 C82 26 88 38 76 44 C70 47 62 44 62 44Z" fill="rgba(255,255,255,0.16)" stroke="rgba(255,255,255,0.32)" stroke-width="1.2"/>
                        <!-- bow center knot -->
                        <ellipse cx="50" cy="44" rx="8" ry="6" fill="rgba(255,255,255,0.22)" stroke="rgba(255,255,255,0.45)" stroke-width="1.2"/>
                    </svg>
                </div>
            </div>
            <?php endif; ?>

            <div class="shop-filters-divider" aria-hidden="true"></div>

            <div class="wittchen-filters-row">
                <details class="wittchen-filter">
                    <summary>Sortuj</summary>
                    <div class="wittchen-filter-menu">
                        <?php foreach ($sort_options as $key => $label) : ?>
                            <a href="<?php echo esc_url($moretti_build_shop_url(array('orderby' => $key))); ?>" class="wittchen-filter-item <?php echo $orderby === $key ? 'is-active' : ''; ?>">
                                <?php echo esc_html($label); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </details>

                <?php
                $colors = $moretti_get_filter_terms($color_taxonomy);
                if (!empty($colors)) :
                ?>
                    <details class="wittchen-filter">
                        <summary>Kolor<?php echo $selected_color !== '' ? ': ' . esc_html($selected_color_label) : ''; ?></summary>
                        <div class="wittchen-filter-menu">
                            <?php foreach ($colors as $color) : ?>
                                <?php $is_active = ($selected_color === $color->slug); ?>
                                <a href="<?php echo $is_active ? esc_url($moretti_build_shop_url(array('filter_color' => false, 'filter_kolor' => false))) : esc_url($moretti_build_shop_url(array('filter_color' => $color->slug, 'filter_kolor' => false))); ?>" class="wittchen-filter-item <?php echo $is_active ? 'is-active' : ''; ?>">
                                    <?php echo esc_html($color->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </details>
                <?php endif; ?>

                <?php
                if (!empty($materials)) :
                ?>
                    <details class="wittchen-filter">
                        <summary>Materiał<?php echo $selected_material !== '' ? ': ' . esc_html($selected_material_label) : ''; ?></summary>
                        <div class="wittchen-filter-menu">
                            <?php foreach ($materials as $material) : ?>
                                <?php $is_active = ($selected_material === $material->slug); ?>
                                <a href="<?php echo $is_active ? esc_url($moretti_build_shop_url(array('filter_material' => false))) : esc_url($moretti_build_shop_url(array('filter_material' => $material->slug))); ?>" class="wittchen-filter-item <?php echo $is_active ? 'is-active' : ''; ?>">
                                    <?php echo esc_html($material->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </details>
                <?php endif; ?>

                <?php
                if ($show_category_filter) :
                    $sizes = $moretti_get_filter_terms($size_taxonomy);
                    if (!empty($sizes)) :
                ?>
                        <details class="wittchen-filter">
                            <summary>Kategoria</summary>
                            <div class="wittchen-filter-menu">
                                <?php foreach ($sizes as $size) : ?>
                                    <?php $is_active = ($selected_size === $size->slug); ?>
                                    <a href="<?php echo $is_active ? esc_url($moretti_build_shop_url(array('filter_size' => false))) : esc_url($moretti_build_shop_url(array('filter_size' => $size->slug))); ?>" class="wittchen-filter-item <?php echo $is_active ? 'is-active' : ''; ?>">
                                        <?php echo esc_html($size->name); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </details>
                <?php
                    endif;
                endif;
                ?>

                <details class="wittchen-filter">
                    <summary><?php echo esc_html($price_summary_label); ?></summary>
                    <div class="wittchen-filter-menu">
                        <form method="get" action="<?php echo esc_url(get_pagenum_link(1)); ?>" class="wittchen-price-form" data-role="price-filter-form">
                            <?php if ($selected_color !== '') : ?>
                                <input type="hidden" name="filter_color" value="<?php echo esc_attr($selected_color); ?>">
                            <?php endif; ?>
                            <?php if ($selected_material !== '') : ?>
                                <input type="hidden" name="filter_material" value="<?php echo esc_attr($selected_material); ?>">
                            <?php endif; ?>
                            <?php if ($selected_size !== '') : ?>
                                <input type="hidden" name="filter_size" value="<?php echo esc_attr($selected_size); ?>">
                            <?php endif; ?>
                            <?php if ($selected_kolekcja !== '') : ?>
                                <input type="hidden" name="filter_kolekcja" value="<?php echo esc_attr($selected_kolekcja); ?>">
                            <?php endif; ?>
                            <?php if ($exclude_kolekcja !== '') : ?>
                                <input type="hidden" name="exclude_kolekcja" value="<?php echo esc_attr($exclude_kolekcja); ?>">
                            <?php endif; ?>
                            <?php if (isset($_GET['s']) && $_GET['s'] !== '') : ?>
                                <input type="hidden" name="s" value="<?php echo esc_attr(sanitize_text_field(wp_unslash($_GET['s']))); ?>">
                            <?php endif; ?>
                            <?php if (isset($_GET['post_type']) && $_GET['post_type'] !== '') : ?>
                                <input type="hidden" name="post_type" value="<?php echo esc_attr(sanitize_text_field(wp_unslash($_GET['post_type']))); ?>">
                            <?php endif; ?>
                            <?php if (!empty($orderby)) : ?>
                                <input type="hidden" name="orderby" value="<?php echo esc_attr($orderby); ?>">
                            <?php endif; ?>
                            <?php if ($is_wishlist_view) : ?>
                                <input type="hidden" name="wishlist" value="1">
                            <?php endif; ?>

                            <div class="wittchen-price-values">
                                <span data-role="min-value"><?php echo esc_html(number_format_i18n($selected_min_price, 0)); ?> zł</span>
                                <span class="wittchen-price-separator">-</span>
                                <span data-role="max-value"><?php echo esc_html(number_format_i18n($selected_max_price, 0)); ?> zł</span>
                            </div>

                            <div class="wittchen-price-range" data-role="range-wrap">
                                <input
                                    type="range"
                                    min="<?php echo esc_attr($price_floor); ?>"
                                    max="<?php echo esc_attr($price_ceil); ?>"
                                    step="1"
                                    value="<?php echo esc_attr($selected_min_price); ?>"
                                    class="wittchen-price-range-input is-min"
                                    data-role="min-range"
                                >
                                <input
                                    type="range"
                                    min="<?php echo esc_attr($price_floor); ?>"
                                    max="<?php echo esc_attr($price_ceil); ?>"
                                    step="1"
                                    value="<?php echo esc_attr($selected_max_price); ?>"
                                    class="wittchen-price-range-input is-max"
                                    data-role="max-range"
                                >
                            </div>

                            <input type="hidden" name="min_price" value="<?php echo esc_attr($selected_min_price); ?>" data-role="min-hidden">
                            <input type="hidden" name="max_price" value="<?php echo esc_attr($selected_max_price); ?>" data-role="max-hidden">

                            <div class="wittchen-price-actions">
                                <button type="submit" class="wittchen-price-submit">Zastosuj</button>
                                <a href="<?php echo esc_url($moretti_build_shop_url(array('min_price' => false, 'max_price' => false))); ?>" class="wittchen-price-clear">Wyczyść</a>
                            </div>
                        </form>
                    </div>
                </details>

                <?php if ($selected_color !== '' || $selected_material !== '' || $selected_size !== '' || $selected_kolekcja !== '' || $exclude_kolekcja !== '' || !empty($_GET['min_price']) || !empty($_GET['max_price'])) : ?>
                    <a class="wittchen-reset" href="<?php echo esc_url($moretti_build_shop_url(array('filter_color' => false, 'filter_kolor' => false, 'filter_material' => false, 'filter_size' => false, 'filter_kolekcja' => false, 'exclude_kolekcja' => false, 'min_price' => false, 'max_price' => false))); ?>">
                        Wyczyść filtry
                    </a>
                <?php endif; ?>
            </div>

            <div class="shop-count-row">
                <span class="shop-count">Liczba produktów: <?php echo $products->found_posts; ?></span>
            </div>

            <!-- Products Grid -->
            <?php if ($products->have_posts()) : ?>
                <div class="products-grid" id="products-grid">
                    <?php while ($products->have_posts()) : $products->the_post(); 
                        global $product;
                                $gallery_ids = $product->get_gallery_image_ids();
                                $has_gallery = !empty($gallery_ids);
                                $main_id = has_post_thumbnail() ? get_post_thumbnail_id() : 0;
                                $all_images = array();
                                if ($has_gallery) {
                                    $all_images = array_map('absint', $gallery_ids);
                                    if ($main_id && !in_array($main_id, $all_images)) {
                                        $all_images[] = $main_id;
                                    }
                                } else {
                                    if ($main_id) {
                                        $all_images[] = $main_id;
                                    }
                                }
                                $image_count = count($all_images);
                                $color_variants = function_exists('moretti_get_product_color_variants')
                                    ? moretti_get_product_color_variants($product)
                                    : array();
                                ?>
                        <article class="product-card <?php echo $image_count > 1 ? 'has-hover-second-image' : ''; ?>" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
                            <div class="product-image-wrapper">
                                    <div class="product-image <?php echo $has_gallery ? 'has-gallery' : ''; ?>" style="aspect-ratio: 3 / 4;">
                                        <div class="product-image-interior-hover-zone">
                                                <a href="<?php echo esc_url(get_permalink()); ?>" class="product-image-interior-link" aria-hidden="true"></a>
                                                <?php if ($image_count > 1) : ?>
                                                    <button type="button" class="image-nav image-prev" aria-label="Poprzednie zdjęcie">
                                                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                                        </svg>
                                                    </button>
                                                    <button type="button" class="image-nav image-next" aria-label="Następne zdjęcie">
                                                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg>
                                                    </button>
                                                    <div class="image-dots">
                                                        <?php for ($i = 0; $i < $image_count; $i++) : ?>
                                                            <span class="image-dot <?php echo $i === 0 ? 'active' : ''; ?>" data-index="<?php echo $i; ?>"></span>
                                                        <?php endfor; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php if ($image_count > 0) : ?>
                                            <?php foreach ($all_images as $index => $image_id) : ?>
                                                <div class="product-image-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>">
                                                    <a href="<?php echo esc_url(get_permalink()); ?>">
                                                        <?php echo wp_get_attachment_image($image_id, 'large'); ?>
                                                    </a>
                                                </div>
                                            <?php endforeach; ?>
                                            <button
                                                type="button"
                                                class="wishlist-toggle image-wishlist product-heart"
                                                data-product-id="<?php echo esc_attr($product->get_id()); ?>"
                                                aria-label="Dodaj do ulubionych"
                                                aria-pressed="false"
                                            >
                                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                </svg>
                                            </button>

                                            <?php if (!empty($color_variants)) : ?>
                                                <div class="sku-color-variants" aria-label="Dostępne warianty kolorystyczne">
                                                    <?php foreach ($color_variants as $variant) : ?>
                                                        <a
                                                            class="sku-color-dot <?php echo !empty($variant['is_current']) ? 'is-current' : ''; ?>"
                                                            href="<?php echo esc_url($variant['url']); ?>"
                                                            style="background-color: <?php echo esc_attr($variant['color_hex']); ?>;"
                                                            aria-label="<?php echo esc_attr($variant['color_label']); ?>"
                                                            title="<?php echo esc_attr($variant['color_label']); ?>"
                                                            <?php if (!empty($variant['first_image_url'])) : ?>data-first-image-url="<?php echo esc_url($variant['first_image_url']); ?>"<?php endif; ?>
                                                        >
                                                            <span class="screen-reader-text"><?php echo esc_html($variant['color_label']); ?></span>
                                                        </a>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php else : ?>
                                            <a href="<?php echo esc_url(get_permalink()); ?>">
                                                <img src="<?php echo esc_url(wc_placeholder_img_src()); ?>" alt="Placeholder">
                                            </a>
                                        <?php endif; ?>
                                    </div>
                            </div>
                            <div class="product-info">
                                <h3 class="product-name">
                                    <a href="<?php echo esc_url(get_permalink()); ?>">
                                        <?php echo esc_html(get_the_title()); ?>
                                    </a>
                                </h3>
                                <div class="product-price">
                                    <?php echo $product->get_price_html(); ?>
                                </div>
                            </div>
                        </article>
                        
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <?php if ($products->max_num_pages > 1) : ?>
                    <div class="shop-pagination">
                        <?php
                        echo paginate_links(array(
                            'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                            'format' => '?paged=%#%',
                            'current' => max(1, $paged),
                            'total' => $products->max_num_pages,
                            'prev_text' => '←',
                            'next_text' => '→',
                        ));
                        ?>
                    </div>
                <?php endif; ?>

            <?php else : ?>
                <div class="shop-empty">
                    <p>Nie znaleziono produktów.</p>
                    <?php
                    $empty_attr_filter = ($selected_kolekcja !== '' || $selected_material !== '') && current_user_can('manage_options');
                    if ($empty_attr_filter) :
                    ?>
                    <p class="shop-empty-hint" style="margin-top:0.75rem;font-size:0.9em;color:#666;">
                        Filtrujesz po atrybucie (Kolekcja/Materiał). Żaden produkt nie ma przypisanego tego termu. Przypisz atrybut w <strong>WooCommerce → Produkty → Edytuj produkt → Atrybuty</strong> (Kolekcja / Materiał).
                    </p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>

            <?php endif; /* !$moretti_has_virtual_children */ ?>

        </main>
        
    </div>
</div>

<!-- Sidebar Overlay (Mobile) -->
<div class="sidebar-overlay" id="sidebar-overlay"></div>

<style>
    /* --- Subcategory grid tiles (parent category pages) --- */
    .moretti-subcategory-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-bottom: 24px;
        justify-content: center;
        max-width: 960px;
        margin-left: auto;
        margin-right: auto;
    }

    /* 3+ items: 3 columns on desktop */
    @media (min-width: 768px) {
        .moretti-subcategory-grid:not([data-count="2"]) {
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
            max-width: none;
        }
    }

    .moretti-subcategory-tile {
        display: flex;
        flex-direction: column;
        text-decoration: none;
        color: #2a2826;
        border: 1px solid #ececec;
        overflow: hidden;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .moretti-subcategory-tile:hover {
        border-color: #c4b8ab;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
    }

    .moretti-subcategory-tile-image {
        position: relative;
        aspect-ratio: 3 / 4;
        overflow: hidden;
        background: #f7f6f4;
    }

    .moretti-subcategory-slide {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0;
        transition: opacity 0.8s ease, transform 0.35s ease;
    }

    .moretti-subcategory-slide.is-active {
        opacity: 1;
    }

    /* Single image — still absolute so object-fit: cover fills the aspect-ratio box */

    .moretti-subcategory-tile:hover .moretti-subcategory-slide.is-active {
        transform: scale(1.04);
    }

    .moretti-subcategory-tile-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #c4b8ab;
    }

    .moretti-subcategory-tile-label {
        display: block;
        padding: 14px 16px 4px;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        line-height: 1.3;
    }

    .moretti-subcategory-tile-count {
        display: block;
        padding: 0 16px 14px;
        font-size: 11px;
        color: #8f8275;
        letter-spacing: 0.01em;
    }

    .shop-page.shop-page-wittchen {
        padding-top: 0;
        padding-bottom: 18px;
    }

    .shop-page-wittchen .shop-container {
        display: block;
        max-width: 1260px;
        margin: 0 auto;
        padding-left: 16px;
        padding-right: 16px;
    }

    .shop-page-wittchen .shop-sidebar,
    .shop-page-wittchen .sidebar-overlay {
        display: none !important;
    }

    .shop-page-wittchen .shop-main {
        width: 100%;
        max-width: 1180px;
        margin: 0 auto;
        gap: 0;
    }

    .shop-page-wittchen .shop-breadcrumbs {
        margin-top: 30px;
        margin-bottom: 15px;
        font-size: 11px;
        gap: 6px;
    }

    .shop-page-wittchen .shop-topbar {
        border: none;
        padding: 0;
        margin-bottom: 15px;
    }

    .shop-page-wittchen .shop-title-wrap {
        gap: 0;
    }

    .shop-page-wittchen .shop-title {
        font-size: clamp(1.5rem, 2vw, 1.95rem);
        font-weight: 500;
        letter-spacing: 0.01em;
        text-transform: none;
    }

    .shop-page-wittchen .shop-count {
        font-size: 12px;
        text-transform: none;
        letter-spacing: 0;
        color: #5f554b;
    }

    .shop-page-wittchen .shop-hero-banner {
        margin: 0 0 15px;
        border: 1px solid #d6a1ab;
        background: linear-gradient(95deg, #a4001a 0%, #b1001d 52%, #840015 100%);
        overflow: hidden;
        position: relative;
        min-height: 175px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .shop-page-wittchen .shop-hero-banner::after {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(90deg, rgba(255, 255, 255, 0.06), rgba(255, 255, 255, 0)),
            repeating-linear-gradient(
                90deg,
                rgba(255, 255, 255, 0.02) 0,
                rgba(255, 255, 255, 0.02) 1px,
                transparent 1px,
                transparent 120px
            );
        pointer-events: none;
    }

    .shop-page-wittchen .shop-hero-banner-content {
        position: relative;
        z-index: 1;
        padding: 22px 26px;
        max-width: 760px;
        color: #fff;
    }

    .shop-page-wittchen .shop-hero-eyebrow {
        margin: 0 0 6px;
        font-size: 11px;
        line-height: 1;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        font-weight: 700;
        color: #ffd5dc;
    }

    .shop-page-wittchen .shop-hero-title {
        margin: 0 0 7px;
        font-size: clamp(1.55rem, 2.8vw, 2.6rem);
        line-height: 1.05;
        font-weight: 800;
        letter-spacing: 0.01em;
        color: #fff;
    }

    .shop-page-wittchen .shop-hero-subtitle {
        margin: 0 0 6px;
        font-size: clamp(1.02rem, 1.6vw, 1.42rem);
        line-height: 1.2;
        font-weight: 600;
        color: #ffe8ec;
    }

    .shop-page-wittchen .shop-hero-copy {
        margin: 0 0 12px;
        font-size: 14px;
        line-height: 1.35;
        color: #ffd8df;
        max-width: 620px;
    }

    .shop-page-wittchen .shop-hero-cta {
        display: inline-block;
        padding: 10px 18px;
        border: 1px solid rgba(255, 255, 255, 0.8);
        color: #fff;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .shop-page-wittchen .shop-hero-cta:hover {
        background: #fff;
        color: #930018;
    }

    .shop-page-wittchen .shop-hero-banner-visual {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px 28px 16px 0;
        pointer-events: none;
    }

    .shop-page-wittchen .shop-hero-ribbon {
        width: 118px;
        height: 118px;
        filter: drop-shadow(0 3px 10px rgba(0, 0, 0, 0.18));
        opacity: 0.82;
    }

    .shop-page-wittchen .shop-filters-divider {
        border-top: 1px solid #e7e7e7;
        margin-bottom: 15px;
    }

    .shop-page-wittchen .wittchen-filters-row {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 15px;
        flex-wrap: wrap;
        padding: 0;
    }

    .shop-page-wittchen .shop-count-row {
        margin-bottom: 4px;
        font-size: 11px;
    }

    .shop-page-wittchen .wittchen-filter {
        position: relative;
    }

    .shop-page-wittchen .wittchen-filter > summary {
        list-style: none;
        border: 1px solid #e1e1e1;
        background: #fff;
        padding: 9px 30px 9px 12px;
        font-size: 11px;
        cursor: pointer;
        min-width: 106px;
        position: relative;
        line-height: 1;
        color: #2a2826;
    }

    .shop-page-wittchen .wittchen-filter > summary::after {
        content: "";
        position: absolute;
        right: 10px;
        top: 50%;
        width: 7px;
        height: 7px;
        border-right: 1px solid #595959;
        border-bottom: 1px solid #595959;
        transform: translateY(-65%) rotate(45deg);
        transition: transform 0.18s ease;
    }

    .shop-page-wittchen .wittchen-filter[open] > summary {
        border-color: #c9c9c9;
        background: #fbfbfb;
    }

    .shop-page-wittchen .wittchen-filter[open] > summary::after {
        transform: translateY(-35%) rotate(225deg);
    }

    .shop-page-wittchen .wittchen-filter > summary::-webkit-details-marker {
        display: none;
    }

    .shop-page-wittchen .wittchen-filter-menu {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        min-width: 210px;
        max-height: 320px;
        overflow: auto;
        border: 1px solid #e1e1e1;
        background: #fff;
        z-index: 60;
        box-shadow: 0 14px 30px rgba(0, 0, 0, 0.12);
        padding: 5px 0;
    }

    .shop-page-wittchen .wittchen-filter:not([open]) .wittchen-filter-menu {
        display: none;
    }

    .shop-page-wittchen .wittchen-filter-item {
        display: block;
        color: #2a2826;
        text-decoration: none;
        font-size: 11px;
        padding: 9px 12px;
        white-space: nowrap;
        border-bottom: 1px solid #f5f5f5;
    }

    .shop-page-wittchen .wittchen-filter-item:last-child {
        border-bottom: none;
    }

    .shop-page-wittchen .wittchen-filter-item:hover,
    .shop-page-wittchen .wittchen-filter-item.is-active {
        background: #f3f3f3;
    }

    .shop-page-wittchen .wittchen-price-form {
        padding: 12px;
        min-width: 260px;
        display: grid;
        gap: 12px;
    }

    .shop-page-wittchen .wittchen-price-values {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        font-size: 11px;
        color: #2a2826;
        white-space: nowrap;
    }

    .shop-page-wittchen .wittchen-price-separator {
        color: #8a8a8a;
    }

    .shop-page-wittchen .wittchen-price-range {
        position: relative;
        height: 20px;
        background: linear-gradient(to right, #e5e7eb 0%, #2a2826 0%, #2a2826 100%, #e5e7eb 100%);
        border-radius: 999px;
    }

    .shop-page-wittchen .wittchen-price-range-input {
        position: absolute;
        inset: 0;
        width: 100%;
        margin: 0;
        pointer-events: none;
        -webkit-appearance: none;
        appearance: none;
        background: transparent;
    }

    .shop-page-wittchen .wittchen-price-range-input::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 1px solid #2a2826;
        background: #fff;
        pointer-events: auto;
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.28);
    }

    .shop-page-wittchen .wittchen-price-range-input::-moz-range-thumb {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 1px solid #2a2826;
        background: #fff;
        pointer-events: auto;
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.28);
    }

    .shop-page-wittchen .wittchen-price-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .shop-page-wittchen .wittchen-price-submit {
        border: 1px solid #2a2826;
        background: #2a2826;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 8px 12px;
        cursor: pointer;
    }

    .shop-page-wittchen .wittchen-price-submit:hover {
        background: #1f1d1c;
    }

    .shop-page-wittchen .wittchen-price-clear {
        font-size: 11px;
        color: #2a2826;
        text-decoration: underline;
    }

    .shop-page-wittchen .wittchen-reset {
        color: #2a2826;
        font-size: 11px;
        text-decoration: underline;
        margin-left: 6px;
    }

    .shop-page-wittchen .products-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px 14px;
    }

    .shop-page-wittchen .product-card {
        border: none;
        background: transparent;
        box-shadow: none !important;
        gap: 4px;
        border-radius: 0;
        overflow: visible;
        display: flex !important;
        flex-direction: column !important;
        align-items: flex-start !important;
        height: auto !important;
    }

    .shop-page-wittchen .product-image-wrapper {
        width: 100% !important;
        flex-shrink: 0 !important;
    }

    .shop-page-wittchen .product-card:hover {
        box-shadow: none !important;
    }

    .shop-page-wittchen .product-image {
        position: relative !important;
        aspect-ratio: 3 / 4 !important;
        background: #F2F2F2 !important;
        width: 100% !important;
        overflow: hidden !important;
    }

    .shop-page-wittchen .product-image img {
        width: 100% !important;
        height: 100% !important;
        object-fit: contain !important;
        object-position: center center !important;
    }

    .shop-page-wittchen .sku-color-variants {
        position: absolute;
        left: 10px;
        bottom: 10px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        z-index: 5;
        max-width: calc(100% - 52px);
    }

    .shop-page-wittchen .sku-color-dot {
        width: 14px;
        height: 14px;
        border-radius: 999px;
        border: 1px solid rgba(31, 29, 28, 0.3);
        box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.9);
        display: inline-block;
        text-decoration: none;
    }

    .shop-page-wittchen .sku-color-dot:hover {
        transform: scale(1.08);
    }

    .shop-page-wittchen .sku-color-dot:focus-visible {
        outline: 2px solid #111;
        outline-offset: 2px;
    }

    .shop-page-wittchen .sku-color-dot.is-current {
        width: 16px;
        height: 16px;
        border-width: 2px;
        border-color: #111;
        box-shadow: 0 0 0 2px #ffffff;
    }

    .shop-page-wittchen .product-info {
        text-align: left;
        align-items: flex-start;
        gap: 0px;
        padding: 10px 8px 12px;
        background: transparent;
    }

    .shop-page-wittchen .product-name {
        margin: 0 0 10px !important;
        min-height: auto;
        font-size: 12px !important;
        line-height: 1.1;
        font-weight: 700 !important;
        text-transform: none;
    }

    .shop-page-wittchen .product-price {
        margin: 0 !important;
        font-size: 20px !important;
        line-height: 1.0;
        font-weight: 600;
    }

    .shop-page-wittchen .product-price del {
        font-size: 12px;
        color: #8f8275;
        margin-right: 6px;
        font-weight: 400;
    }

    .shop-page-wittchen .product-price ins {
        text-decoration: none;
    }

    @media (max-width: 1023px) {
        .shop-page-wittchen .shop-container {
            max-width: 100%;
            padding-left: 10px;
            padding-right: 10px;
        }

        .shop-page-wittchen .shop-main {
            max-width: 100%;
        }

        .shop-page-wittchen .products-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 767px) {
        .shop-page.shop-page-wittchen {
            padding-top: 10px;
        }

        .shop-page-wittchen .shop-breadcrumbs {
            margin-top: 12px;
            margin-bottom: 10px;
        }

        .shop-page-wittchen .shop-topbar {
            margin-bottom: 10px;
        }

        .shop-page-wittchen .shop-hero-banner {
            margin-bottom: 10px;
            min-height: 0;
        }

        .shop-page-wittchen .shop-hero-banner-visual {
            display: none;
        }

        .shop-page-wittchen .shop-hero-banner-content {
            padding: 16px 14px;
        }

        .shop-page-wittchen .shop-hero-eyebrow {
            font-size: 9px;
            margin-bottom: 4px;
        }

        .shop-page-wittchen .shop-hero-title {
            font-size: 1.35rem;
            margin-bottom: 4px;
        }

        .shop-page-wittchen .shop-hero-subtitle {
            font-size: 0.92rem;
            margin-bottom: 4px;
        }

        .shop-page-wittchen .shop-hero-copy {
            display: none;
        }

        .shop-page-wittchen .shop-hero-cta {
            font-size: 11px;
            padding: 8px 12px;
        }

        .shop-page-wittchen .wittchen-filters-row {
            gap: 6px;
            margin-bottom: 6px;
            padding: 4px 0;
        }

        .shop-page-wittchen .wittchen-filter > summary {
            min-width: 102px;
            padding: 9px 24px 9px 10px;
        }

        .shop-page-wittchen .wittchen-filter-menu {
            min-width: 180px;
            max-width: 86vw;
        }

        .shop-page-wittchen .products-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px 10px;
        }

        .shop-page-wittchen .product-price {
            font-size: 16px;
        }

        .shop-page-wittchen .sku-color-variants {
            left: 8px;
            bottom: 8px;
            gap: 5px;
            max-width: calc(100% - 44px);
        }

        .shop-page-wittchen .sku-color-dot {
            width: 13px;
            height: 13px;
        }

        .shop-page-wittchen .sku-color-dot.is-current {
            width: 15px;
            height: 15px;
        }

        .shop-page-wittchen .product-heart {
            opacity: 1;
        }

        .shop-page-wittchen .image-nav {
            display: none !important;
        }
    }

    /* Grid: hide arrows and dots by default; show them when hovering the interior zone (zone contains link + nav + dots to avoid flicker) */
    .shop-page-wittchen .products-grid .image-nav,
    .shop-page-wittchen .products-grid .image-dots {
        display: none !important;
    }
    .shop-page-wittchen .products-grid .product-image .product-image-interior-hover-zone:hover .image-nav,
    .shop-page-wittchen .products-grid .product-image .product-image-interior-hover-zone:hover .image-dots {
        display: flex !important;
    }

    .shop-page-wittchen .products-grid .product-image-interior-hover-zone {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 82%;
        z-index: 2;
        pointer-events: auto;
        display: block;
    }
    .shop-page-wittchen .products-grid .product-image-interior-link {
        position: absolute;
        inset: 0;
        display: block;
        z-index: 0;
        text-decoration: none;
        color: transparent;
    }
    .shop-page-wittchen .products-grid .product-image-interior-hover-zone .image-nav,
    .shop-page-wittchen .products-grid .product-image-interior-hover-zone .image-dots {
        z-index: 1;
    }

    /* Jedna karuzela: widoczność tylko z .active; hover = przejście na slajd 1 w JS */
    .shop-page-wittchen .products-grid .product-card.has-hover-second-image .product-image-slide {
        opacity: 0;
    }
    .shop-page-wittchen .products-grid .product-card.has-hover-second-image .product-image-slide.active {
        opacity: 1;
    }
    .shop-page-wittchen .products-grid .product-card.has-hover-second-image.is-showing-variant-preview .product-image-slide {
        opacity: 0 !important;
    }
    .shop-page-wittchen .products-grid .product-card.has-hover-second-image.is-showing-variant-preview .product-image-slide[data-index="0"] {
        opacity: 1 !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.documentElement.classList.add('shop-infinite-ready');

    // Filter Toggle (Mobile)
    const filterToggle = document.getElementById('filter-toggle');
    const sidebar = document.getElementById('shop-sidebar');
    const sidebarClose = document.getElementById('sidebar-close');
    const overlay = document.getElementById('sidebar-overlay');
    
    // Open sidebar
    if (filterToggle && sidebar && overlay) {
        filterToggle.addEventListener('click', function() {
            sidebar.classList.add('open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent body scroll
        });
        
        // Close sidebar function
        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            document.body.style.overflow = ''; // Restore scroll
        }
        
        // Close button
        if (sidebarClose) {
            sidebarClose.addEventListener('click', closeSidebar);
        }
        
        // Overlay click
        overlay.addEventListener('click', closeSidebar);
    }
    
    // Wittchen-like filter behavior: only one open at a time
    const filterDetails = Array.from(document.querySelectorAll('.wittchen-filter'));
    const closeOtherFilters = (current = null) => {
        filterDetails.forEach((item) => {
            if (item !== current) {
                item.removeAttribute('open');
            }
        });
    };

    filterDetails.forEach((detail) => {
        detail.addEventListener('toggle', () => {
            if (detail.open) {
                closeOtherFilters(detail);
            }
        });
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.wittchen-filter')) {
            closeOtherFilters();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeOtherFilters();
        }
    });

    const formatPrice = (value) => `${new Intl.NumberFormat('pl-PL').format(value)} zł`;
    const updateRangeTrack = (wrap, minValue, maxValue, minLimit, maxLimit) => {
        const spread = Math.max(1, maxLimit - minLimit);
        const minPct = ((minValue - minLimit) / spread) * 100;
        const maxPct = ((maxValue - minLimit) / spread) * 100;
        wrap.style.background = `linear-gradient(to right, #e5e7eb 0%, #e5e7eb ${minPct}%, #2a2826 ${minPct}%, #2a2826 ${maxPct}%, #e5e7eb ${maxPct}%, #e5e7eb 100%)`;
    };

    document.querySelectorAll('[data-role="price-filter-form"]').forEach((form) => {
        const minRange = form.querySelector('[data-role="min-range"]');
        const maxRange = form.querySelector('[data-role="max-range"]');
        const minHidden = form.querySelector('[data-role="min-hidden"]');
        const maxHidden = form.querySelector('[data-role="max-hidden"]');
        const minValueText = form.querySelector('[data-role="min-value"]');
        const maxValueText = form.querySelector('[data-role="max-value"]');
        const rangeWrap = form.querySelector('[data-role="range-wrap"]');
        if (!minRange || !maxRange || !minHidden || !maxHidden || !minValueText || !maxValueText || !rangeWrap) {
            return;
        }

        const minLimit = parseInt(minRange.min || '0', 10);
        const maxLimit = parseInt(minRange.max || '0', 10);

        const sync = (source) => {
            let minValue = parseInt(minRange.value || String(minLimit), 10);
            let maxValue = parseInt(maxRange.value || String(maxLimit), 10);

            if (minValue > maxValue) {
                if (source === 'min') {
                    minValue = maxValue;
                    minRange.value = String(minValue);
                } else {
                    maxValue = minValue;
                    maxRange.value = String(maxValue);
                }
            }

            minHidden.value = String(minValue);
            maxHidden.value = String(maxValue);
            minValueText.textContent = formatPrice(minValue);
            maxValueText.textContent = formatPrice(maxValue);
            updateRangeTrack(rangeWrap, minValue, maxValue, minLimit, maxLimit);
        };

        minRange.addEventListener('input', () => sync('min'));
        maxRange.addEventListener('input', () => sync('max'));
        sync();
    });
    
    const initProductCard = (card) => {
        if (!card || card.dataset.morettiInit === '1') {
            return;
        }
        card.dataset.morettiInit = '1';

        const slides = card.querySelectorAll('.product-image-slide');
        const prevBtn = card.querySelector('.image-prev');
        const nextBtn = card.querySelector('.image-next');
        const dots = card.querySelectorAll('.image-dot');
        
        if (slides.length > 1) {
            let currentIndex = 0;
            
            function showSlide(index) {
                if (index < 0 || index >= slides.length) return;
                slides.forEach(slide => slide.classList.remove('active'));
                dots.forEach(dot => dot.classList.remove('active'));
                slides[index].classList.add('active');
                if (dots[index]) dots[index].classList.add('active');
                currentIndex = index;
            }

            const zone = card.querySelector('.product-image-interior-hover-zone');
            if (zone) {
                zone.addEventListener('mouseenter', function() {
                    if (currentIndex === 0) showSlide(1);
                });
            }
            card.addEventListener('mouseleave', function() {
                showSlide(0);
            });

            // Touch/Swipe Support
            let touchStartX = 0;
            let touchEndX = 0;
            
            card.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });
            
            card.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                const swipeThreshold = 50;
                if (touchEndX < touchStartX - swipeThreshold) {
                    // Swipe Left -> Next
                    const newIndex = currentIndex < slides.length - 1 ? currentIndex + 1 : 0;
                    showSlide(newIndex);
                }
                if (touchEndX > touchStartX + swipeThreshold) {
                    // Swipe Right -> Prev
                    const newIndex = currentIndex > 0 ? currentIndex - 1 : slides.length - 1;
                    showSlide(newIndex);
                }
            }, { passive: true });
            
            if (prevBtn) {
                prevBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const newIndex = currentIndex > 0 ? currentIndex - 1 : slides.length - 1;
                    showSlide(newIndex);
                });
            }
            
            if (nextBtn) {
                nextBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const newIndex = currentIndex < slides.length - 1 ? currentIndex + 1 : 0;
                    showSlide(newIndex);
                });
            }
            
            dots.forEach(dot => {
                dot.addEventListener('click', (e) => {
                    e.preventDefault();
                    showSlide(parseInt(dot.dataset.index, 10));
                });
            });
        }

        const quickAddButton = card.querySelector('.quick-add');
        if (quickAddButton && quickAddButton.dataset.morettiInit !== '1') {
            quickAddButton.dataset.morettiInit = '1';
            quickAddButton.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = parseInt(this.dataset.productId || '0', 10);
                if (!Number.isInteger(productId) || productId <= 0) {
                    return;
                }

                if (typeof morettiQuickAddToCart === 'function') {
                    morettiQuickAddToCart(productId, this);
                }
            });
        }
    };

    document.querySelectorAll('.product-card').forEach(initProductCard);

});
</script>

<script>
/* Subcategory tile image slideshow — crossfade every 3s */
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.moretti-subcategory-tile-image[data-slideshow]').forEach(function(container) {
        var slides = container.querySelectorAll('.moretti-subcategory-slide');
        if (slides.length <= 1) return;
        var current = 0;
        setInterval(function() {
            slides[current].classList.remove('is-active');
            current = (current + 1) % slides.length;
            slides[current].classList.add('is-active');
        }, 3000);
    });
});
</script>

<?php get_footer(); ?>
