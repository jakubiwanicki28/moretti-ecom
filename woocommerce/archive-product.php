<?php
/**
 * The Template for displaying product archives, including the main shop page
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

get_header(); ?>

    <!-- ARCHIVE-PRODUCT.PHP LOADED -->
    <div class="woocommerce-shop-wrapper bg-white">
        
        <?php if (have_posts()) : ?>

            <div class="container mx-auto px-4 py-8 md:py-12">
                
                <!-- Page Title -->
                <?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
                    <h1 class="text-3xl md:text-4xl font-light text-charcoal mb-8 md:mb-12 text-center tracking-[0.2em] uppercase">
                        <?php 
                        $title = woocommerce_page_title(false);
                        if ($title === 'Sklep' || $title === 'Products') {
                            echo 'SKLEP';
                        } else {
                            echo esc_html(strtoupper($title));
                        }
                        ?>
                    </h1>
                <?php endif; ?>

                <!-- Search & Filter Bar -->
                <div class="flex flex-col md:flex-row items-center justify-between gap-6 mb-8 md:mb-12 pb-6 border-b border-gray-100">
                    
                <!-- Search Bar -->
                <form role="search" method="get" class="w-full md:max-w-xs" action="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">
                    <div class="relative group">
                        <input 
                            type="search" 
                            name="s" 
                            placeholder="Szukaj produktów..."
                            value="<?php echo get_search_query(); ?>"
                            class="w-full bg-transparent border-b border-gray-300 focus:border-charcoal focus:outline-none py-2 pr-10 text-sm transition-colors"
                        >
                        <input type="hidden" name="post_type" value="product" />
                        <button type="submit" class="absolute right-0 top-1/2 transform -translate-y-1/2 text-taupe-600 hover:text-charcoal transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
                    
                    <!-- Sort Dropdown -->
                    <div class="flex items-center gap-8">
                        <?php
                        $catalog_orderby_options = apply_filters('woocommerce_catalog_orderby', array(
                            'menu_order' => 'Domyślne sortowanie',
                            'popularity' => 'Sortuj wg popularności',
                            'rating'     => 'Sortuj wg ocen',
                            'date'       => 'Sortuj od najnowszych',
                            'price'      => 'Cena: od najniższej',
                            'price-desc' => 'Cena: od najwyższej',
                        ));
                        $orderby = isset($_GET['orderby']) ? wc_clean($_GET['orderby']) : apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby', 'menu_order'));
                        ?>
                        <form method="get" class="woocommerce-ordering border-none m-0 p-0">
                            <select name="orderby" class="orderby text-xs uppercase tracking-widest bg-transparent border-none text-charcoal cursor-pointer hover:text-taupe-600 focus:outline-none p-0" onchange="this.form.submit()">
                                <?php foreach ($catalog_orderby_options as $id => $name) : ?>
                                    <option value="<?php echo esc_attr($id); ?>" <?php selected($orderby, $id); ?>><?php echo esc_html($name); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php wc_query_string_form_fields(null, array('orderby', 'submit', 'paged', 'product-page')); ?>
                        </form>

                        <!-- Filter Toggle Button -->
                        <button 
                            id="filter-toggle" 
                            class="text-xs uppercase tracking-widest text-charcoal hover:text-taupe-600 transition-colors flex items-center gap-2 group"
                        >
                            <span>Filtry</span>
                            <svg class="w-4 h-4 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Filters Sidebar (Collapsible) -->
                <div id="filters-panel" class="hidden mb-12 bg-sand-50/50 border border-gray-100 p-8 animate-fade-in">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                        
                        <!-- Price Filter -->
                        <div>
                            <h3 class="text-xs font-semibold text-charcoal mb-4 uppercase tracking-widest">Zakres ceny</h3>
                            <form method="get" action="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">
                                <?php
                                foreach ($_GET as $key => $value) {
                                    if ($key !== 'min_price' && $key !== 'max_price') {
                                        echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '">';
                                    }
                                }
                                ?>
                                <div class="space-y-4">
                                    <div class="flex items-center gap-3">
                                        <input 
                                            type="number" 
                                            name="min_price" 
                                            placeholder="Min" 
                                            value="<?php echo isset($_GET['min_price']) ? esc_attr($_GET['min_price']) : ''; ?>"
                                            class="w-full bg-white border-none text-xs p-3 focus:ring-1 focus:ring-charcoal"
                                        >
                                        <span class="text-taupe-400">—</span>
                                        <input 
                                            type="number" 
                                            name="max_price" 
                                            placeholder="Max" 
                                            value="<?php echo isset($_GET['max_price']) ? esc_attr($_GET['max_price']) : ''; ?>"
                                            class="w-full bg-white border-none text-xs p-3 focus:ring-1 focus:ring-charcoal"
                                        >
                                    </div>
                                    <button type="submit" class="w-full py-3 bg-charcoal text-white text-[10px] uppercase tracking-[0.2em] hover:bg-taupe-800 transition-colors">
                                        Zastosuj
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Color Filter -->
                        <div>
                            <h3 class="text-xs font-semibold text-charcoal mb-4 uppercase tracking-widest">Kolory</h3>
                            <?php
                            $colors = get_terms(array('taxonomy' => 'pa_color', 'hide_empty' => true));
                            if (!empty($colors) && !is_wp_error($colors)) : ?>
                                <div class="flex flex-wrap gap-3">
                                    <?php foreach ($colors as $color) : 
                                        $color_hex = moretti_get_color_hex($color->name);
                                        $is_active = isset($_GET['filter_color']) && $_GET['filter_color'] === $color->slug;
                                    ?>
                                        <a 
                                            href="?filter_color=<?php echo esc_attr($color->slug); ?><?php echo isset($_GET['product_cat']) ? '&product_cat=' . esc_attr($_GET['product_cat']) : ''; ?>" 
                                            class="w-6 h-6 rounded-full ring-1 <?php echo $is_active ? 'ring-charcoal ring-offset-2 ring-2' : 'ring-gray-200'; ?> hover:ring-charcoal transition-all"
                                            style="background-color: <?php echo esc_attr($color_hex); ?>;"
                                            title="<?php echo esc_attr($color->name); ?>"
                                        ></a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Stock Status -->
                        <div>
                            <h3 class="text-xs font-semibold text-charcoal mb-4 uppercase tracking-widest">Dostępność</h3>
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input 
                                        type="checkbox" 
                                        <?php echo isset($_GET['stock_status']) && in_array('instock', explode(',', $_GET['stock_status'])) ? 'checked' : ''; ?>
                                        onchange="location.href='<?php echo esc_url(add_query_arg('stock_status', 'instock')); ?>'"
                                        class="w-4 h-4 border-gray-300 text-charcoal focus:ring-charcoal rounded-none"
                                    >
                                    <span class="text-xs text-taupe-700 group-hover:text-charcoal transition-colors">W magazynie</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input 
                                        type="checkbox" 
                                        <?php echo isset($_GET['stock_status']) && in_array('onbackorder', explode(',', $_GET['stock_status'])) ? 'checked' : ''; ?>
                                        onchange="location.href='<?php echo esc_url(add_query_arg('stock_status', 'onbackorder')); ?>'"
                                        class="w-4 h-4 border-gray-300 text-charcoal focus:ring-charcoal rounded-none"
                                    >
                                    <span class="text-xs text-taupe-700 group-hover:text-charcoal transition-colors">Na zamówienie</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Clear Filters -->
                        <div>
                            <h3 class="text-xs font-semibold text-charcoal mb-4 uppercase tracking-widest">Akcje</h3>
                            <a 
                                href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" 
                                class="inline-block w-full text-center py-3 border border-charcoal text-[10px] uppercase tracking-[0.2em] text-charcoal hover:bg-charcoal hover:text-white transition-colors"
                            >
                                Wyczyść wszystko
                            </a>
                        </div>
                        
                    </div>
                </div>

                <!-- Category Filter Pills -->
                <?php
                $product_categories = get_terms(array(
                    'taxonomy' => 'product_cat',
                    'hide_empty' => true,
                    'exclude' => array(get_option('default_product_cat')),
                ));

                if (!empty($product_categories) && !is_wp_error($product_categories)) : ?>
                    <div class="mb-12 flex flex-wrap justify-center gap-4">
                        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" 
                           class="text-[10px] uppercase tracking-[0.2em] px-6 py-2 border border-charcoal transition-colors rounded-none <?php echo (!is_product_category()) ? 'bg-charcoal text-white' : 'text-charcoal hover:bg-charcoal hover:text-white'; ?>">
                            Wszystko
                        </a>
                        <?php foreach ($product_categories as $category) : ?>
                            <a href="<?php echo esc_url(get_term_link($category)); ?>" 
                               class="text-[10px] uppercase tracking-[0.2em] px-6 py-2 border border-charcoal transition-colors rounded-none <?php echo (is_product_category($category->slug)) ? 'bg-charcoal text-white' : 'text-charcoal hover:bg-charcoal hover:text-white'; ?>">
                                <?php echo esc_html($category->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Products Grid -->
                <?php
                woocommerce_product_loop_start();

                while (have_posts()) {
                    the_post();
                    
                    /**
                     * Hook: woocommerce_shop_loop.
                     */
                    do_action('woocommerce_shop_loop');

                    wc_get_template_part('content', 'product');
                }

                woocommerce_product_loop_end();
                ?>
                <div class="clear-both"></div>

                <!-- Pagination -->
                <div class="moretti-pagination-wrapper mt-12 md:mt-20">
                    <style>
                        .moretti-pagination-wrapper ul.page-numbers { display: flex !important; flex-direction: row !important; justify-content: center !important; gap: 8px !important; list-style: none !important; }
                        .moretti-pagination-wrapper ul.page-numbers li { display: block !important; margin: 0 !important; }
                    </style>
                    <?php
                    /**
                     * Hook: woocommerce_after_shop_loop.
                     */
                    do_action('woocommerce_after_shop_loop');
                    ?>
                </div>

            </div>

        <?php else : ?>

        <div class="container mx-auto px-4 py-12 text-center">
            <?php
            /**
             * Hook: woocommerce_no_products_found.
             */
            do_action('woocommerce_no_products_found');
            ?>
        </div>

    <?php endif; ?>

</div>

<?php
get_footer();
