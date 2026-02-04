<?php
/**
 * The Template for displaying product archives, including the main shop page
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

get_header(); ?>

<div class="woocommerce-shop-wrapper bg-white min-h-screen">
    <div class="container mx-auto px-4 py-12 md:py-20">
        
        <?php if (have_posts()) : ?>

            <!-- Shop Header -->
            <header class="text-center mb-16 md:mb-24">
                <span class="text-[10px] uppercase tracking-[0.4em] text-taupe-500 mb-4 block font-medium">Kolekcja Akcesoriów</span>
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-charcoal uppercase tracking-tighter leading-none">
                    <?php 
                    $title = woocommerce_page_title(false);
                    if (is_search()) {
                        echo 'Wyniki wyszukiwania';
                    } elseif (is_product_category()) {
                        echo esc_html(strtoupper(single_cat_title('', false)));
                    } else {
                        echo 'NASZ SKLEP';
                    }
                    ?>
                </h1>
            </header>

            <!-- Control Bar: Search, Sort, Filter -->
            <div class="flex flex-col md:flex-row items-end justify-between gap-8 mb-16 pb-8 border-b border-gray-100 relative z-50">
                
                <!-- Search -->
                <div class="w-full md:w-1/3">
                    <form role="search" method="get" class="relative group" action="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">
                        <input 
                            type="search" 
                            name="s" 
                            placeholder="SZUKAJ..."
                            value="<?php echo get_search_query(); ?>"
                            class="w-full bg-transparent border-b border-gray-200 focus:border-charcoal focus:outline-none py-4 pr-10 text-xs font-bold uppercase tracking-widest transition-colors"
                        >
                        <input type="hidden" name="post_type" value="product" />
                        <button type="submit" class="absolute right-0 top-1/2 transform -translate-y-1/2 text-charcoal hover:text-taupe-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </form>
                </div>

                <!-- Category Navigation -->
                <nav class="hidden lg:flex items-center gap-8">
                    <?php
                    $categories = get_terms(array(
                        'taxonomy' => 'product_cat',
                        'hide_empty' => true,
                        'exclude' => array(get_option('default_product_cat')),
                    ));
                    ?>
                    <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" 
                       class="text-[10px] font-bold uppercase tracking-[0.2em] <?php echo !is_product_category() ? 'text-charcoal border-b-2 border-charcoal' : 'text-taupe-400 hover:text-charcoal'; ?> pb-1 transition-all">
                        Wszystko
                    </a>
                    <?php foreach ($categories as $cat) : ?>
                        <a href="<?php echo esc_url(get_term_link($cat)); ?>" 
                           class="text-[10px] font-bold uppercase tracking-[0.2em] <?php echo is_product_category($cat->slug) ? 'text-charcoal border-b-2 border-charcoal' : 'text-taupe-400 hover:text-charcoal'; ?> pb-1 transition-all">
                            <?php echo esc_html($cat->name); ?>
                        </a>
                    <?php endforeach; ?>
                </nav>

                <!-- Sort & Filter Actions -->
                <div class="flex items-center gap-4 w-full md:w-auto justify-between md:justify-end">
                    <!-- Sort -->
                    <div class="relative w-full md:w-[220px]">
                        <select name="orderby" class="moretti-custom-select appearance-none text-[10px] font-bold uppercase tracking-[0.2em] bg-transparent border-none text-charcoal cursor-pointer hover:text-taupe-600 focus:outline-none pr-6" onchange="window.location.href=window.location.pathname + '?orderby=' + this.value">
                            <?php
                            $catalog_orderby_options = apply_filters('woocommerce_catalog_orderby', array(
                                'menu_order' => 'Sortowanie',
                                'popularity' => 'Popularność',
                                'date'       => 'Nowości',
                                'price'      => 'Cena ↑',
                                'price-desc' => 'Cena ↓',
                            ));
                            $orderby = isset($_GET['orderby']) ? wc_clean($_GET['orderby']) : 'menu_order';
                            foreach ($catalog_orderby_options as $id => $name) : ?>
                                <option value="<?php echo esc_attr($id); ?>" <?php selected($orderby, $id); ?>><?php echo esc_html($name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Filter Button -->
                    <button id="filter-toggle" class="bg-charcoal text-white px-8 text-[10px] font-bold uppercase tracking-[0.2em] hover:bg-taupe-800 transition-all flex items-center justify-center gap-3">
                        FILTRY
                        <svg class="transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 12px; height: 12px;"><path d="M19 9l-7 7-7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Expandable Filter Panel -->
            <div id="filters-panel" class="hidden mb-16 bg-gray-50 border border-gray-100 p-8 md:p-12">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-12">
                    <!-- Price -->
                    <div>
                        <h4 class="text-[10px] font-bold uppercase tracking-[0.2em] mb-6">Cena (PLN)</h4>
                        <form method="get" class="flex flex-col gap-4">
                            <div class="flex items-center gap-3">
                                <input type="number" name="min_price" placeholder="Od" class="w-full bg-white border-none py-3 px-4 text-xs focus:ring-1 focus:ring-charcoal" value="<?php echo isset($_GET['min_price']) ? esc_attr($_GET['min_price']) : ''; ?>">
                                <input type="number" name="max_price" placeholder="Do" class="w-full bg-white border-none py-3 px-4 text-xs focus:ring-1 focus:ring-charcoal" value="<?php echo isset($_GET['max_price']) ? esc_attr($_GET['max_price']) : ''; ?>">
                            </div>
                            <button type="submit" class="w-full py-3 bg-charcoal text-white text-[10px] font-bold uppercase tracking-widest hover:bg-taupe-800 transition-colors">Zastosuj</button>
                        </form>
                    </div>

                    <!-- Colors -->
                    <div>
                        <h4 class="text-[10px] font-bold uppercase tracking-[0.2em] mb-6">Kolory</h4>
                        <div class="flex flex-wrap gap-3">
                            <?php
                            $colors = get_terms(array('taxonomy' => 'pa_color', 'hide_empty' => true));
                            foreach ($colors as $color) :
                                $color_hex = moretti_get_color_hex($color->name);
                                $is_active = isset($_GET['filter_color']) && $_GET['filter_color'] === $color->slug;
                            ?>
                                <a href="?filter_color=<?php echo $color->slug; ?>" class="w-8 h-8 rounded-full border-2 <?php echo $is_active ? 'border-charcoal scale-110' : 'border-transparent'; ?> transition-all hover:scale-110" style="background-color: <?php echo $color_hex; ?>;" title="<?php echo $color->name; ?>"></a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Categories (Mobile only) -->
                    <div class="lg:hidden">
                        <h4 class="text-[10px] font-bold uppercase tracking-[0.2em] mb-6">Kategorie</h4>
                        <ul class="space-y-3">
                            <?php foreach ($categories as $cat) : ?>
                                <li><a href="<?php echo esc_url(get_term_link($cat)); ?>" class="text-xs uppercase tracking-widest text-taupe-600 hover:text-charcoal"><?php echo $cat->name; ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col justify-end">
                        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="text-[10px] font-bold uppercase tracking-[0.2em] text-charcoal border-b border-charcoal pb-1 w-fit hover:opacity-70 transition-opacity">Wyczyść wszystkie filtry</a>
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <?php woocommerce_product_loop_start(); ?>
                <?php while (have_posts()) : the_post(); ?>
                    <?php wc_get_template_part('content', 'product'); ?>
                <?php endwhile; ?>
            <?php woocommerce_product_loop_end(); ?>

            <!-- Pagination -->
            <div class="mt-20 flex justify-center">
                <?php
                echo paginate_links(array(
                    'base'         => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                    'format'       => '?paged=%#%',
                    'current'      => max(1, get_query_var('paged')),
                    'total'        => $wp_query->max_num_pages,
                    'prev_text'    => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>',
                    'next_text'    => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>',
                    'type'         => 'list',
                    'class'        => 'moretti-pagination'
                ));
                ?>
            </div>

        <?php else : ?>
            <div class="text-center py-20">
                <h2 class="text-2xl font-light text-charcoal mb-4 uppercase tracking-widest">Nie znaleziono produktów</h2>
                <p class="text-taupe-600 mb-8">Spróbuj zmienić filtry lub wyszukiwaną frazę.</p>
                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="inline-block bg-charcoal text-white px-10 py-4 font-bold text-xs uppercase tracking-widest">Wszystkie produkty</a>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php get_footer(); ?>
