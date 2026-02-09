<?php
/**
 * The Template for displaying product archives, including the main shop page
 * UX-First Design: Products visible immediately
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

get_header(); 

// Declare WordPress global query object
global $wp_query;

// Get categories for navigation
$categories = get_terms(array(
    'taxonomy' => 'product_cat',
    'hide_empty' => true,
    'exclude' => array(get_option('default_product_cat')),
));

// Determine page title
$page_title = 'Sklep';
if (is_search()) {
    $page_title = 'Wyniki: "' . get_search_query() . '"';
} elseif (is_product_category()) {
    $page_title = single_cat_title('', false);
}

// Count products
$product_count = $wp_query->found_posts;
?>

<div class="woocommerce-shop-wrapper bg-white min-h-screen">
    <div class="container mx-auto px-4 py-8">
        
        <?php if (have_posts()) : ?>

            <!-- Sidebar + Main Grid Layout -->
            <div class="moretti-shop-layout">
                
                <!-- LEFT SIDEBAR: Filters & Categories -->
                <aside class="moretti-sidebar">
                    
                    <!-- Categories Section -->
                    <div class="sidebar-section">
                        <h3 class="sidebar-heading">Kategorie</h3>
                        <nav class="sidebar-nav">
                            <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" 
                               class="sidebar-link <?php echo !is_product_category() ? 'active' : ''; ?>">
                                Wszystko
                            </a>
                            <?php foreach ($categories as $cat) : ?>
                                <a href="<?php echo esc_url(get_term_link($cat)); ?>" 
                                   class="sidebar-link <?php echo is_product_category($cat->slug) ? 'active' : ''; ?>">
                                    <?php echo esc_html($cat->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </nav>
                    </div>

                    <!-- Price Filter -->
                    <div class="sidebar-section">
                        <h3 class="sidebar-heading">Cena (PLN)</h3>
                        <form method="get" class="price-filter-form">
                            <div class="price-inputs">
                                <input type="number" name="min_price" placeholder="Od" class="price-input" value="<?php echo isset($_GET['min_price']) ? esc_attr($_GET['min_price']) : ''; ?>">
                                <span class="price-separator">-</span>
                                <input type="number" name="max_price" placeholder="Do" class="price-input" value="<?php echo isset($_GET['max_price']) ? esc_attr($_GET['max_price']) : ''; ?>">
                            </div>
                            <button type="submit" class="price-submit">Zastosuj</button>
                        </form>
                    </div>

                    <!-- Color Filter -->
                    <?php
                    $colors = get_terms(array('taxonomy' => 'pa_color', 'hide_empty' => true));
                    if (!empty($colors) && !is_wp_error($colors)) :
                    ?>
                    <div class="sidebar-section">
                        <h3 class="sidebar-heading">Kolory</h3>
                        <div class="color-swatches">
                            <?php
                            foreach ($colors as $color) :
                                $color_hex = moretti_get_color_hex($color->name);
                                $is_active = isset($_GET['filter_color']) && $_GET['filter_color'] === $color->slug;
                            ?>
                                <a href="?filter_color=<?php echo $color->slug; ?>" 
                                   class="color-swatch <?php echo $is_active ? 'active' : ''; ?>"
                                   style="background-color: <?php echo $color_hex; ?>;" 
                                   title="<?php echo $color->name; ?>"></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Clear Filters -->
                    <div class="sidebar-section">
                        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="clear-filters">
                            Wyczyść filtry
                        </a>
                    </div>

                </aside>

                <!-- RIGHT MAIN: Products Grid -->
                <main class="moretti-main">
                    
                    <!-- Top Bar: Title + Sort + Mobile Filter Toggle -->
                    <div class="shop-top-bar flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-8 border-b border-gray-100 pb-4">
                        <div class="shop-title-section flex flex-col gap-1">
                            <h1 class="shop-title text-2xl font-bold uppercase tracking-wide text-charcoal m-0"><?php echo esc_html($page_title); ?></h1>
                            <span class="product-count text-[10px] font-bold uppercase tracking-widest text-gray-400"><?php echo $product_count; ?> <?php echo $product_count == 1 ? 'produkt' : ($product_count < 5 ? 'produkty' : 'produktów'); ?></span>
                        </div>
                        
                        <div class="shop-controls flex flex-col w-full lg:w-auto gap-4 lg:flex-row lg:items-center">
                            <!-- Filter Toggle Button (Custom) -->
                            <div class="custom-filter-wrapper">
                                <button type="button" id="mobile-filter-toggle" class="custom-filter-btn">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                    </svg>
                                    Filtry
                                </button>
                            </div>
                            
                            <!-- Sort Dropdown (Custom Select) -->
                            <div class="custom-select-wrapper">
                                <button type="button" class="custom-select-trigger" id="sort-select-trigger">
                                    <?php
                                    $catalog_orderby_options = array(
                                        'menu_order' => 'Sortuj',
                                        'popularity' => 'Popularność',
                                        'date'       => 'Nowości',
                                        'price'      => 'Cena: rosnąco',
                                        'price-desc' => 'Cena: malejąco',
                                    );
                                    $orderby = isset($_GET['orderby']) ? wc_clean($_GET['orderby']) : 'menu_order';
                                    echo esc_html($catalog_orderby_options[$orderby]);
                                    ?>
                                    <svg width="10" height="10" fill="none" stroke="#2a2826" viewBox="0 0 24 24" class="select-arrow">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div class="custom-select-dropdown" id="sort-select-dropdown">
                                    <?php foreach ($catalog_orderby_options as $id => $name) : ?>
                                        <a href="<?php echo esc_url(add_query_arg('orderby', $id, get_permalink())); ?>" 
                                           class="custom-select-option <?php echo $orderby === $id ? 'active' : ''; ?>"
                                           data-value="<?php echo esc_attr($id); ?>">
                                            <?php echo esc_html($name); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Grid -->
                    <div class="products-wrapper">
                        <?php woocommerce_product_loop_start(); ?>
                            <?php while (have_posts()) : the_post(); ?>
                                <?php wc_get_template_part('content', 'product'); ?>
                            <?php endwhile; ?>
                        <?php woocommerce_product_loop_end(); ?>
                    </div>

                    <!-- Pagination -->
                    <div class="shop-pagination">
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

                </main>

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

<!-- Mobile Sidebar Overlay -->
<div id="mobile-sidebar-overlay" class="mobile-sidebar-overlay"></div>

<script>
// Mobile Sidebar Toggle
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('mobile-filter-toggle');
    const sidebar = document.querySelector('.moretti-sidebar');
    const overlay = document.getElementById('mobile-sidebar-overlay');
    
    if (!toggleBtn || !sidebar || !overlay) return;
    
    function openSidebar() {
        sidebar.classList.add('mobile-open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeSidebar() {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    toggleBtn.addEventListener('click', openSidebar);
    overlay.addEventListener('click', closeSidebar);
    
    // Close on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('mobile-open')) {
            closeSidebar();
        }
    });
});

// Custom Select Dropdown
document.addEventListener('DOMContentLoaded', function() {
    const selectTrigger = document.getElementById('sort-select-trigger');
    const selectWrapper = document.querySelector('.custom-select-wrapper');
    const selectDropdown = document.getElementById('sort-select-dropdown');
    
    if (!selectTrigger || !selectWrapper || !selectDropdown) return;
    
    // Toggle dropdown
    selectTrigger.addEventListener('click', function(e) {
        e.stopPropagation();
        selectWrapper.classList.toggle('open');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!selectWrapper.contains(e.target)) {
            selectWrapper.classList.remove('open');
        }
    });
    
    // Close on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && selectWrapper.classList.contains('open')) {
            selectWrapper.classList.remove('open');
        }
    });
});
</script>

<?php get_footer(); ?>
