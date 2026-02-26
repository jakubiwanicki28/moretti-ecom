<?php
/**
 * Header template - CEIN style minimalist design
 * 
 * @package Moretti
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    
    <!-- FAVICON & METADATA -->
    <link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/images/favicon.png">
    <link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/images/favicon.png">
    <meta name="description" content="Moretti - Ekskluzywne portfele skórzane i akcesoria premium. Ponadczasowa elegancja, najwyższej jakości rzemiosło i dbałość o każdy detal. Darmowa dostawa od 250 zł.">
    
    <!-- SOCIAL MEDIA (OPEN GRAPH) -->
    <meta property="og:title" content="Moretti - Ekskluzywne Portfele Premium">
    <meta property="og:description" content="Odkryj naszą wyselekcjonowaną kolekcję portfeli premium. Wyjątkowe rzemiosło, które towarzyszy Ci każdego dnia.">
    <meta property="og:image" content="<?php echo get_template_directory_uri(); ?>/images/moretti-logo.png">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo home_url(); ?>">

    <!-- TWITTER CARDS -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Moretti - Ekskluzywne Portfele Premium">
    <meta name="twitter:description" content="Odkryj naszą wyselekcjonowaną kolekcję portfeli premium. Wyjątkowe rzemiosło, które towarzyszy Ci każdego dnia.">
    <meta name="twitter:image" content="<?php echo get_template_directory_uri(); ?>/images/moretti-logo.png">

    <?php wp_head(); ?>
    <style>
        /* MORETTI PREMIUM CONTROLS */
        @media (max-width: 767px) {
            html, body {
                width: 100%;
                max-width: 100%;
                overflow-x: hidden;
            }
        }
        
        /* Shop Page Mobile Overrides */
        @media (max-width: 1023px) {
            .shop-top-bar {
                display: flex !important;
                flex-direction: column !important;
                align-items: flex-start !important;
            }
            .shop-title-section {
                margin-bottom: 20px !important;
            }
            .shop-controls {
                display: flex !important;
                width: 100% !important;
                gap: 10px !important;
            }
            .mobile-filter-btn, .sort-dropdown-wrapper {
                flex: 1 !important;
                width: 50% !important;
            }
            .moretti-custom-select {
                width: 100% !important;
            }
        }

        /* Variations table - clean block layout */
        .woocommerce div.product form.cart .variations {
            display: block !important;
            width: 100% !important;
            border: none !important;
            margin-bottom: 24px !important;
        }
        
        .woocommerce div.product form.cart .variations tbody {
            display: block !important;
        }
        
        .woocommerce div.product form.cart .variations tr {
            display: block !important;
            margin-bottom: 20px !important;
            border: none !important;
        }
        
        .woocommerce div.product form.cart .variations td,
        .woocommerce div.product form.cart .variations th {
            display: block !important;
            padding: 0 !important;
            border: none !important;
            width: 100% !important;
        }
        
        .woocommerce div.product form.cart .variations td.label {
            margin-bottom: 8px !important;
        }
        
        .woocommerce div.product form.cart .variations td.value {
            width: 100% !important;
        }
        
        /* Quantity input - consistent 64px */
        .woocommerce div.product form.cart .quantity input.qty {
            height: 64px !important;
            padding: 0 16px !important;
            text-align: center !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            border: 1px solid #e5e7eb !important;
        }
        
        /* Add to cart button - consistent 64px */
        .woocommerce div.product form.cart button.single_add_to_cart_button {
            height: 64px !important;
            padding: 0 40px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        /* EMPTY CART OVERRIDES */
        .cart-empty, .woocommerce-info, .cart-empty-container {
            display: none !important;
        }
        
        #moretti-empty-cart-override {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* New desktop header inspired by Wittchen */
        .moretti-header {
            background: #fff;
            border-bottom: 1px solid #ededed;
        }

        .moretti-header-inner {
            max-width: 1260px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .moretti-header-top {
            height: 60px;
            display: grid;
            grid-template-columns: 180px minmax(0, 1fr) 180px;
            align-items: center;
            gap: 14px;
        }

        .moretti-logo {
            font-size: 28px;
            letter-spacing: 0.18em;
            font-weight: 700;
            text-transform: uppercase;
            text-decoration: none;
            color: #111;
            white-space: nowrap;
        }

        .moretti-logo-wrap .custom-logo-link {
            display: inline-block;
            max-width: 170px;
            line-height: 0;
        }

        .moretti-logo-wrap .custom-logo {
            max-height: 32px;
            width: auto;
        }

        .moretti-header-search-form {
            max-width: 560px;
            width: 100%;
            margin: 0 auto;
        }

        .moretti-header-search-wrap {
            position: relative;
        }

        .moretti-header-search-wrap input[type="search"] {
            width: 100%;
            height: 36px;
            border: 1px solid #e5e5e5;
            padding: 0 40px 0 14px;
            font-size: 12px;
            color: #2a2826;
            background: #fafafa;
        }

        .moretti-header-search-wrap button {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #6b6b6b;
            cursor: pointer;
            padding: 0;
        }

        .moretti-header-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 8px;
        }

        .moretti-header-icon {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #2a2826;
            position: relative;
            text-decoration: none;
        }

        .moretti-header-action-link {
            width: auto;
            gap: 6px;
            padding: 0 2px;
        }

        .moretti-header-action-label {
            display: inline-block;
            font-size: 12px;
            line-height: 1;
            letter-spacing: 0.01em;
            color: #2a2826;
            white-space: nowrap;
        }

        .moretti-header-bottom {
            border-top: 1px solid #f1f1f1;
            border-bottom: 1px solid #f1f1f1;
        }

        .moretti-header-cats {
            min-height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 28px;
            position: relative;
        }

        .moretti-cat-item {
            position: relative;
            height: 36px;
            display: flex;
            align-items: center;
        }

        .moretti-cat-link {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            color: #2a2826;
            text-decoration: none;
            line-height: 1;
            padding: 4px 0;
            transition: color 0.2s ease;
        }

        .moretti-cat-link:hover {
            color: #8f8275;
        }

        .moretti-cat-dropdown {
            position: absolute;
            top: calc(100% + 2px);
            left: 50%;
            transform: translateX(-50%);
            min-width: 280px;
            background: #fff;
            border: 1px solid #ececec;
            box-shadow: 0 18px 36px rgba(0, 0, 0, 0.10);
            padding: 14px 0;
            z-index: 80;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.16s ease, transform 0.16s ease, visibility 0.16s ease;
        }

        .moretti-cat-item:hover .moretti-cat-dropdown,
        .moretti-cat-item:focus-within .moretti-cat-dropdown {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: translateX(-50%) translateY(0);
        }

        .moretti-cat-dropdown-title {
            display: block;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            color: #8f8275;
            font-weight: 700;
            padding: 0 16px;
            margin: 10px 0 6px;
        }

        .moretti-cat-dropdown a {
            display: block;
            font-size: 12px;
            line-height: 1.35;
            color: #2a2826;
            text-decoration: none;
            padding: 7px 16px;
            letter-spacing: 0.01em;
            text-transform: none;
        }

        .moretti-cat-dropdown a:hover {
            background: #f7f7f7;
        }

        .moretti-cat-dropdown .moretti-cat-dropdown-all {
            font-weight: 600;
        }

        @media (max-width: 767px) {
            .moretti-header-inner {
                padding: 0 12px;
            }

            .moretti-header-top {
                grid-template-columns: 1fr auto 1fr;
                height: 56px;
                gap: 0;
            }

            .moretti-header-top .moretti-header-search-form,
            .moretti-header-bottom {
                display: none;
            }

            .moretti-logo {
                display: none;
            }

            .moretti-logo-link {
                gap: 8px !important;
            }

            .moretti-logo-link img {
                height: 30px !important;
            }

            .moretti-logo-link .moretti-logo-text {
                font-size: 20px !important;
            }

            .moretti-header-action-link {
                width: 36px;
                height: 36px;
                gap: 0;
                padding: 0;
            }

            .moretti-header-action-label {
                display: none;
            }
        }
    </style>
    <script>
        // Brutal force fix for empty cart
        document.addEventListener('DOMContentLoaded', function() {
            if (document.body.classList.contains('woocommerce-cart')) {
                const checkEmpty = () => {
                    const content = document.querySelector('.page-content') || document.querySelector('.woocommerce');
                    if (content && (content.innerText.includes('pusty') || content.innerText.includes('empty'))) {
                        // If we don't see our override, but we see the empty message, force it
                        if (!document.getElementById('moretti-empty-cart-override')) {
                            window.location.reload(); // Refresh might help if it's a race condition
                        }
                    }
                };
                setTimeout(checkEmpty, 500);
            }
        });
    </script>
</head>
<body <?php body_class('bg-white text-charcoal'); ?>>
<?php wp_body_open(); ?>

<!-- Top Banner (Hidden by user request) -->
<?php if (false && get_theme_mod('show_top_banner', true)) : ?>
<div class="bg-charcoal text-white text-center py-2 px-4 text-xs md:text-sm">
    <?php echo wp_kses_post(get_theme_mod('top_banner_text', 'Darmowa dostawa przy zamówieniach powyżej 250 zł. <a href="/shop" class="underline">Kup teraz</a>')); ?>
</div>
<?php endif; ?>

<header class="moretti-header sticky top-0 z-50">
    <?php
    $shop_url = class_exists('WooCommerce') ? get_permalink(wc_get_page_id('shop')) : home_url('/');
    $resolve_header_category_link = static function(array $slugs, $fallback) {
        foreach ($slugs as $slug) {
            $term = get_term_by('slug', $slug, 'product_cat');
            if ($term && !is_wp_error($term)) {
                $term_link = get_term_link($term);
                if (!is_wp_error($term_link)) {
                    return $term_link;
                }
            }
        }
        return $fallback;
    };

    $resolve_header_category_term = static function(array $slugs) {
        foreach ($slugs as $slug) {
            $term = get_term_by('slug', $slug, 'product_cat');
            if ($term && !is_wp_error($term)) {
                return $term;
            }
        }
        return null;
    };

    $build_header_panel_data = static function($term) {
        $result = array(
            'categories' => array(),
            'colors' => array(),
        );

        if (!$term || is_wp_error($term)) {
            return $result;
        }

        $children = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
            'parent' => (int) $term->term_id,
            'orderby' => 'name',
            'order' => 'ASC',
        ));
        if (!is_wp_error($children) && !empty($children)) {
            $result['categories'] = $children;
        }

        $product_ids = get_objects_in_term((int) $term->term_id, 'product_cat');
        if (is_wp_error($product_ids) || empty($product_ids)) {
            return $result;
        }

        foreach (array('pa_color', 'pa_kolor', 'pa_colour') as $color_taxonomy) {
            if (!taxonomy_exists($color_taxonomy)) {
                continue;
            }
            $colors = wp_get_object_terms($product_ids, $color_taxonomy, array(
                'hide_empty' => true,
                'orderby' => 'name',
                'order' => 'ASC',
            ));
            if (!is_wp_error($colors) && !empty($colors)) {
                $result['colors'] = array_slice($colors, 0, 10);
                break;
            }
        }

        return $result;
    };

    $header_nav_items = array(
        array(
            'label' => 'Dla niej',
            'term' => $resolve_header_category_term(array('portfele-damskie', 'dzial-damski', 'dla-niej')),
        ),
        array(
            'label' => 'Dla niego',
            'term' => $resolve_header_category_term(array('portfele-meskie', 'dzial-meski', 'dla-niego')),
        ),
        array(
            'label' => 'Nowości',
            'term' => $resolve_header_category_term(array('nowosci', 'nowosci-1', 'new-in')),
        ),
        array(
            'label' => 'Klasyka i hity',
            'term' => $resolve_header_category_term(array('klasyka-i-hity', 'klasyki-i-hity', 'hity')),
        ),
        array(
            'label' => 'Okazje',
            'term' => $resolve_header_category_term(array('okazje', 'promocje', 'sale')),
        ),
    );

    foreach ($header_nav_items as &$header_nav_item) {
        $term = $header_nav_item['term'];
        $header_nav_item['url'] = ($term && !is_wp_error($term)) ? get_term_link($term) : $shop_url;
        if (is_wp_error($header_nav_item['url'])) {
            $header_nav_item['url'] = $shop_url;
        }
        $header_nav_item['panel'] = $build_header_panel_data($term);
    }
    unset($header_nav_item);
    ?>
    <div class="moretti-header-inner">
        <div class="moretti-header-top">
            <div class="flex items-center gap-1 md:hidden">
                <a
                    href="#mobile-nav"
                    id="mobile-menu-link"
                    class="w-10 h-10 flex items-center justify-center text-charcoal hover:text-taupe-600"
                    aria-label="Menu"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </a>
                <a
                    href="#search"
                    id="search-toggle-mobile"
                    class="w-10 h-10 flex items-center justify-center text-charcoal hover:text-taupe-600"
                    aria-label="Szukaj"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </a>
            </div>

            <div>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="moretti-logo-link flex items-center no-underline" style="gap: 10px; display: flex; align-items: center; text-decoration: none;">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/moretti-logo.png" alt="Moretti" style="height: 38px; width: auto; display: block;">
                    <span class="moretti-logo-text" style="font-size: 24px; letter-spacing: 0.15em; font-weight: 700; text-transform: uppercase; color: #111; line-height: 1; white-space: nowrap; font-family: sans-serif;">MORETTI</span>
                </a>
            </div>

            <form role="search" method="get" action="<?php echo esc_url($shop_url); ?>" class="moretti-header-search-form hidden md:block">
                <div class="moretti-header-search-wrap">
                    <input
                        type="search"
                        name="s"
                        placeholder="Szukaj"
                        value="<?php echo esc_attr(get_search_query()); ?>"
                        autocomplete="off"
                    />
                    <input type="hidden" name="post_type" value="product" />
                    <button type="submit" aria-label="Szukaj">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </form>

            <div class="moretti-header-actions">
                <?php if (class_exists('WooCommerce')) : ?>
                    <a href="<?php echo esc_url(add_query_arg('wishlist', '1', $shop_url)); ?>" class="moretti-header-icon moretti-header-action-link wishlist-header-link" aria-label="Schowek">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <span class="moretti-header-action-label">Schowek</span>
                        <span class="wishlist-count-header absolute top-1 right-1 bg-charcoal text-white text-[8px] w-4 h-4 hidden items-center justify-center rounded-full font-bold" data-wishlist-count>0</span>
                    </a>

                    <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="moretti-header-icon moretti-header-action-link" aria-label="Koszyk">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span class="moretti-header-action-label">Koszyk</span>
                        <?php $cart_count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0; ?>
                        <?php if ($cart_count > 0) : ?>
                            <span class="absolute top-1 right-1 bg-charcoal text-white text-[8px] w-4 h-4 flex items-center justify-center rounded-full font-bold"><?php echo (int) $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="moretti-header-bottom hidden md:block">
        <nav class="moretti-header-inner moretti-header-cats" aria-label="Kategorie glowne">
            <?php foreach ($header_nav_items as $item) : ?>
                <div class="moretti-cat-item">
                    <a href="<?php echo esc_url($item['url']); ?>" class="moretti-cat-link"><?php echo esc_html($item['label']); ?></a>
                    <div class="moretti-cat-dropdown">
                        <a href="<?php echo esc_url($item['url']); ?>" class="moretti-cat-dropdown-all">Wszystko</a>
                        <?php if (!empty($item['panel']['categories'])) : ?>
                            <span class="moretti-cat-dropdown-title">Kategorie</span>
                            <?php foreach ($item['panel']['categories'] as $category_term) : ?>
                                <a href="<?php echo esc_url(get_term_link($category_term)); ?>"><?php echo esc_html($category_term->name); ?></a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if (!empty($item['panel']['colors'])) : ?>
                            <span class="moretti-cat-dropdown-title">Kolory</span>
                            <?php foreach ($item['panel']['colors'] as $color_term) : ?>
                                <a href="<?php echo esc_url(add_query_arg('filter_color', $color_term->slug, $item['url'])); ?>"><?php echo esc_html($color_term->name); ?></a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Mobile Search Overlay - Full Screen -->
    <div id="search-overlay-mobile" class="md:hidden fixed inset-0 bg-white z-[250] transition-transform duration-300 translate-y-full">
        <div class="h-full flex flex-col">
            <!-- Search Header -->
            <div class="flex items-center justify-between px-4 py-4 border-b border-gray-200">
                <h3 class="text-sm font-bold uppercase tracking-[0.2em] text-charcoal">Wyszukaj</h3>
                <button 
                    id="search-close-mobile"
                    class="w-10 h-10 flex items-center justify-center text-charcoal hover:text-taupe-600"
                    aria-label="Zamknij wyszukiwanie"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Search Form -->
            <div class="flex-1 px-4 py-6">
                <form role="search" method="get" action="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">
                    <div class="flex flex-col gap-4">
                        <input 
                            type="search" 
                            id="search-input-mobile"
                            name="s" 
                            class="w-full border-2 border-gray-200 focus:outline-none focus:border-charcoal px-4 py-4 text-base"
                            placeholder="Wpisz nazwę produktu..."
                            value="<?php echo get_search_query(); ?>"
                            autocomplete="off"
                        />
                        <input type="hidden" name="post_type" value="product" />
                        <button 
                            type="submit" 
                            class="w-full bg-charcoal text-white hover:bg-taupe-700 transition-colors font-bold uppercase py-4 text-sm tracking-[0.15em]"
                        >
                            Szukaj
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Desktop Search Bar - Hidden by default -->
    <div id="search-bar-desktop" style="display: none;" class="hidden md:block bg-white border-t border-b border-gray-100">
        <div class="container mx-auto px-4" style="padding-top: 2rem; padding-bottom: 2rem;">
            <form role="search" method="get" class="flex items-center gap-2" action="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">
                <input 
                    type="search" 
                    id="search-input-desktop"
                    name="s" 
                    class="flex-1 border border-gray-200 focus:outline-none focus:border-charcoal"
                    style="height: 64px; padding: 0 1.5rem; font-size: 14px;"
                    placeholder="Szukaj produktów..."
                    value="<?php echo get_search_query(); ?>"
                />
                <input type="hidden" name="post_type" value="product" />
                <button 
                    type="submit" 
                    class="bg-charcoal text-white hover:bg-taupe-700 transition-colors font-bold uppercase"
                    style="height: 64px; padding: 0 2.5rem; font-size: 11px; letter-spacing: 0.15em; border: 1px solid #2a2826;"
                >
                    Szukaj
                </button>
                <button 
                    type="button"
                    id="search-close-desktop"
                    class="px-4 py-3 text-charcoal hover:text-taupe-600 text-sm"
                >
                    ✕
                </button>
            </form>
        </div>
    </div>

    <!-- Mobile Menu Dropdown - SLIDE FROM LEFT -->
    <div id="mobile-menu" class="md:hidden bg-white border-r border-gray-200 fixed top-0 left-0 bottom-0 w-[85vw] max-w-[400px] z-[200] overflow-y-auto transition-transform duration-300 -translate-x-full shadow-2xl overscroll-behavior-y-contain" style="-webkit-overflow-scrolling: touch;">
        <nav class="px-6 py-8" style="padding-bottom: 100px;">
            <!-- Close Button -->
            <button id="mobile-menu-close" class="absolute top-4 right-4 w-10 h-10 flex items-center justify-center text-charcoal hover:text-taupe-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <!-- Search Bar -->
            <div class="mb-8 mt-8">
                <form role="search" method="get" action="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">
                    <div style="position: relative;">
                        <input 
                            type="search" 
                            name="s" 
                            placeholder="Wpisz szukaną frazę"
                            style="width: 100%; height: 48px; padding: 0 48px 0 16px; border: 1px solid #e5e7eb; border-radius: 4px; font-size: 14px; color: #2a2826;"
                        />
                        <input type="hidden" name="post_type" value="product" />
                        <button type="submit" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">
                            <svg width="20" height="20" fill="none" stroke="#6b7280" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <ul class="space-y-6">
                <li><a href="<?php echo esc_url(home_url('/#nowosci')); ?>" class="mobile-menu-link block text-base font-medium text-charcoal uppercase tracking-[0.1em]">Nowości</a></li>
                <li><a href="<?php echo esc_url(home_url('/#klasyki')); ?>" class="mobile-menu-link block text-base font-medium text-charcoal uppercase tracking-[0.1em]">Klasyki i Hity</a></li>
                <?php if (class_exists('WooCommerce')) : ?>
                    <li><a href="<?php echo esc_url(add_query_arg('wishlist', '1', get_permalink(wc_get_page_id('shop')))); ?>" class="mobile-menu-link block text-base font-medium text-charcoal uppercase tracking-[0.1em]">Ulubione</a></li>
                <?php endif; ?>
                
                <!-- Divider -->
                <li style="border-top: 1px solid #f3f4f6; margin: 16px 0 !important; padding-top: 16px;"></li>
                
                <!-- WooCommerce Categories -->
                <?php
                if (class_exists('WooCommerce')) {
                    $categories = get_terms(array(
                        'taxonomy' => 'product_cat',
                        'hide_empty' => true,
                        'exclude' => array(get_option('default_product_cat')),
                    ));
                    
                    if (!empty($categories) && !is_wp_error($categories)) {
                        foreach ($categories as $cat) {
                            echo '<li><a href="' . esc_url(get_term_link($cat)) . '" class="mobile-menu-link block text-base font-medium text-charcoal uppercase tracking-[0.1em]">' . esc_html($cat->name) . '</a></li>';
                        }
                    }
                }
                ?>
                
                <!-- Divider -->
                <li style="border-top: 1px solid #f3f4f6; margin: 16px 0 !important; padding-top: 16px;"></li>
                
                <!-- Login/Logout - HIDDEN BY USER REQUEST -->
                <!-- 
                <li>
                    <?php if (is_user_logged_in()) : ?>
                        <a href="<?php echo wp_logout_url(home_url()); ?>" class="block text-base font-medium text-charcoal uppercase tracking-[0.1em]">Wyloguj się</a>
                    <?php else : ?>
                        <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>" class="block text-base font-medium text-charcoal uppercase tracking-[0.1em]">Zaloguj się</a>
                    <?php endif; ?>
                </li>
                -->
                
                <!-- Social Media Icons -->
                <li class="pt-4">
                    <div style="display: flex; gap: 20px; align-items: center;">
                        <a href="#" aria-label="Facebook" style="color: #766a5d; transition: color 0.2s;" onmouseover="this.style.color='#2a2826'" onmouseout="this.style.color='#766a5d'">
                            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                        </a>
                        <a href="#" aria-label="Instagram" style="color: #766a5d; transition: color 0.2s;" onmouseover="this.style.color='#2a2826'" onmouseout="this.style.color='#766a5d'">
                            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.668-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                    </div>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu-overlay" class="fixed inset-0 bg-black/50 z-[190] hidden"></div>
</header>
