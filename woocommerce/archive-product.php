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
    ? moretti_resolve_attribute_taxonomy(array('pa_material', 'pa_materials', 'pa_materiaal'), '', 'material')
    : 'pa_material';
$size_taxonomy = function_exists('moretti_resolve_attribute_taxonomy')
    ? moretti_resolve_attribute_taxonomy(array('pa_wielkosc', 'pa_size', 'pa_rozmiar'), '', 'wielkosc')
    : 'pa_wielkosc';

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
$is_wishlist_view = isset($_GET['wishlist']) && '1' === sanitize_text_field(wp_unslash($_GET['wishlist']));

$moretti_current_query_args = array();
if ($selected_color !== '') {
    $moretti_current_query_args['filter_color'] = $selected_color;
}
if ($selected_material !== '') {
    $moretti_current_query_args['filter_material'] = $selected_material;
}
if ($selected_size !== '') {
    $moretti_current_query_args['filter_size'] = $selected_size;
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

$moretti_build_shop_url = static function ($overrides = array()) use ($moretti_current_query_args) {
    $args = array_merge($moretti_current_query_args, $overrides);

    foreach ($args as $key => $value) {
        if ($value === false || $value === null || $value === '') {
            unset($args[$key]);
        }
    }

    $base_url = remove_query_arg(array_keys($moretti_current_query_args), get_pagenum_link(1));
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
    'posts_per_page' => 12,
    'paged' => $paged,
    'post_status' => 'publish',
);

if ($is_wishlist_view) {
    $args['post__in'] = !empty($wishlist_ids) ? $wishlist_ids : array(0);
    $args['orderby'] = 'post__in';
}

// Initialize tax_query
$tax_query = array('relation' => 'AND');

// Handle category filter
if (is_product_category()) {
    $tax_query[] = array(
        'taxonomy' => 'product_cat',
        'field' => 'slug',
        'terms' => get_queried_object()->slug,
    );
}

// Handle attribute filters
if (!$is_wishlist_view && $color_taxonomy && $selected_color !== '') {
    $tax_query[] = array(
        'taxonomy' => $color_taxonomy,
        'field' => 'slug',
        'terms' => $selected_color,
    );
}

if (!$is_wishlist_view && $material_taxonomy && $selected_material !== '') {
    $tax_query[] = array(
        'taxonomy' => $material_taxonomy,
        'field' => 'slug',
        'terms' => $selected_material,
    );
}

if (!$is_wishlist_view && $size_taxonomy && $selected_size !== '') {
    $tax_query[] = array(
        'taxonomy' => $size_taxonomy,
        'field' => 'slug',
        'terms' => $selected_size,
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
if (is_product_category()) {
    $page_title = single_cat_title('', false);
} elseif ($is_wishlist_view) {
    $page_title = 'Ulubione';
}

// View logic:
// - Category archive pages should hide category-level filter duplication.
$is_shop_root_view = is_shop() && !is_product_category() && !$is_wishlist_view;
$show_category_filter = $is_shop_root_view;

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
            $materials = $moretti_get_filter_terms($material_taxonomy);
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
                    <?php if (!empty($orderby)) : ?>
                        <input type="hidden" name="orderby" value="<?php echo esc_attr($orderby); ?>">
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
            <?php if ($selected_color !== '' || $selected_material !== '' || $selected_size !== '' || !empty($_GET['min_price']) || !empty($_GET['max_price'])) : ?>
            <div class="sidebar-block">
                <a href="<?php echo esc_url($moretti_build_shop_url(array('filter_color' => false, 'filter_kolor' => false, 'filter_material' => false, 'filter_size' => false, 'min_price' => false, 'max_price' => false))); ?>" class="clear-all-btn">
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

            <div class="shop-hero-banner">
                <div class="shop-hero-banner-content">
                    <p class="shop-hero-eyebrow">Limitowana oferta</p>
                    <h2 class="shop-hero-title">Okazje na Dzień Kobiet</h2>
                    <p class="shop-hero-subtitle">-30% na wybrane modele portfeli</p>
                    <p class="shop-hero-copy">Oferta limitowana czasowo. Wybierz styl, który zostaje z Tobą na lata.</p>
                    <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="shop-hero-cta">Zobacz kolekcję</a>
                </div>
            </div>

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
                $materials = $moretti_get_filter_terms($material_taxonomy);
                if (!empty($materials)) :
                ?>
                    <details class="wittchen-filter">
                        <summary>Materiał<?php echo $selected_material !== '' ? ': ' . esc_html($selected_material) : ''; ?></summary>
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
                    <summary>Cena</summary>
                    <div class="wittchen-filter-menu">
                        <a href="<?php echo esc_url($moretti_build_shop_url(array('max_price' => 200, 'min_price' => false))); ?>" class="wittchen-filter-item">Do 200 zł</a>
                        <a href="<?php echo esc_url($moretti_build_shop_url(array('min_price' => 200, 'max_price' => 500))); ?>" class="wittchen-filter-item">200-500 zł</a>
                        <a href="<?php echo esc_url($moretti_build_shop_url(array('min_price' => 500, 'max_price' => false))); ?>" class="wittchen-filter-item">Powyżej 500 zł</a>
                    </div>
                </details>

                <?php if ($selected_color !== '' || $selected_material !== '' || $selected_size !== '' || !empty($_GET['min_price']) || !empty($_GET['max_price'])) : ?>
                    <a class="wittchen-reset" href="<?php echo esc_url($moretti_build_shop_url(array('filter_color' => false, 'filter_kolor' => false, 'filter_material' => false, 'filter_size' => false, 'min_price' => false, 'max_price' => false))); ?>">
                        Wyczyść filtry
                    </a>
                <?php endif; ?>
            </div>

            <div class="shop-count-row">
                <span class="shop-count">Liczba produktów: <?php echo $products->found_posts; ?></span>
            </div>

            <!-- Products Grid -->
            <?php if ($products->have_posts()) : ?>
                <div class="products-grid">
                    <?php while ($products->have_posts()) : $products->the_post(); 
                        global $product; ?>
                        
                        <article class="product-card" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
                            <div class="product-image-wrapper">
                                <?php
                                $gallery_ids = $product->get_gallery_image_ids();
                                $has_gallery = !empty($gallery_ids);
                                $all_images = array();
                                
                                if (has_post_thumbnail()) {
                                    $all_images[] = get_post_thumbnail_id();
                                }
                                if ($has_gallery) {
                                    $all_images = array_merge($all_images, $gallery_ids);
                                }
                                $image_count = count($all_images);
                                ?>
                                
                                    <div class="product-image <?php echo $has_gallery ? 'has-gallery' : ''; ?>" style="aspect-ratio: 3 / 4;">
                                        <?php if ($image_count > 0) : ?>
                                            <?php foreach ($all_images as $index => $image_id) : ?>
                                                <div class="product-image-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>">
                                                    <a href="<?php echo esc_url(get_permalink()); ?>">
                                                        <?php echo wp_get_attachment_image($image_id, 'large'); ?>
                                                    </a>
                                                </div>
                                            <?php endforeach; ?>
                                            
                                            <?php if ($image_count > 1) : ?>
                                                <button class="image-nav image-prev" aria-label="Poprzednie zdjęcie">
                                                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                                    </svg>
                                                </button>
                                                <button class="image-nav image-next" aria-label="Następne zdjęcie">
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
                </div>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>

        </main>
        
    </div>
</div>

<!-- Sidebar Overlay (Mobile) -->
<div class="sidebar-overlay" id="sidebar-overlay"></div>

<style>
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
        background: #f7f5f2 !important;
        width: 100% !important;
        overflow: hidden !important;
    }

    .shop-page-wittchen .product-image img {
        width: 100% !important;
        height: 100% !important;
        object-fit: contain !important;
        object-position: center center !important;
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
            min-height: 136px;
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
            font-size: 12px;
            margin-bottom: 8px;
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

        .shop-page-wittchen .product-heart {
            opacity: 1;
        }

        .shop-page-wittchen .image-nav {
            display: none !important;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
    
    // Image Gallery Navigation
    document.querySelectorAll('.product-card').forEach(card => {
        const slides = card.querySelectorAll('.product-image-slide');
        const prevBtn = card.querySelector('.image-prev');
        const nextBtn = card.querySelector('.image-next');
        const dots = card.querySelectorAll('.image-dot');
        
        if (slides.length <= 1) return;
        
        let currentIndex = 0;
        
        function showSlide(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            slides[index].classList.add('active');
            dots[index].classList.add('active');
            currentIndex = index;
        }
        
        // Touch/Swipe Support
        let touchStartX = 0;
        let touchEndX = 0;
        
        card.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        
        card.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, { passive: true });
        
        function handleSwipe() {
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
        }
        
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
                showSlide(parseInt(dot.dataset.index));
            });
        });
    });
    
    // Quick Add to Cart
    const addButtons = document.querySelectorAll('.quick-add');
    addButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = parseInt(this.dataset.productId || '0', 10);
            if (!Number.isInteger(productId) || productId <= 0) {
                return;
            }

            if (typeof morettiQuickAddToCart === 'function') {
                morettiQuickAddToCart(productId, this);
            }
        });
    });
});
</script>

<?php get_footer(); ?>
