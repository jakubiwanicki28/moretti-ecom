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
                    <div class="shop-top-bar" style="border-bottom: 1px solid #f3f4f6 !important; padding-bottom: 20px !important; margin-bottom: 30px !important;">
                        <div class="shop-title-section" style="display: flex !important; flex-direction: column !important; align-items: flex-start !important; gap: 4px !important; margin-bottom: 15px !important; width: 100% !important;">
                            <h1 class="shop-title" style="font-size: 24px !important; font-weight: 700 !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; color: #2a2826 !important; margin: 0 !important; line-height: 1.2 !important;"><?php echo esc_html($page_title); ?></h1>
                            <span class="product-count" style="font-size: 11px !important; color: #8f8275 !important; text-transform: uppercase !important; letter-spacing: 0.1em !important; font-weight: 500 !important;"><?php echo $product_count; ?> <?php echo $product_count == 1 ? 'produkt' : ($product_count < 5 ? 'produkty' : 'produktów'); ?></span>
                        </div>
                        
                        <div class="shop-controls" style="display: flex !important; flex-direction: column !important; align-items: stretch !important; gap: 12px !important; width: 100% !important;">
                            <!-- Mobile Filter Toggle Button -->
                            <button id="mobile-filter-toggle" class="mobile-filter-btn" style="width: 100% !important; display: flex !important; align-items: center !important; justify-content: center !important; gap: 8px !important; padding: 0 !important; background: #2a2826 !important; color: #fff !important; border: none !important; font-size: 11px !important; font-weight: 700 !important; text-transform: uppercase !important; letter-spacing: 0.15em !important; cursor: pointer !important; height: 56px !important; line-height: 56px !important; margin: 0 !important; box-sizing: border-box !important;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                                Filtry
                            </button>
                            
                            <!-- Sort Dropdown (Custom Select) -->
                            <div class="sort-dropdown-wrapper" style="width: 100% !important; position: relative !important; height: 56px !important; box-sizing: border-box !important; border: 1px solid #e5e7eb !important; margin: 0 !important; background: #fff !important;">
                                <select name="orderby" class="moretti-custom-select" onchange="window.location.href=window.location.pathname + '?orderby=' + this.value" style="width: 100% !important; height: 54px !important; border: none !important; background: transparent !important; padding: 0 15px !important; font-size: 11px !important; font-weight: 700 !important; text-transform: uppercase !important; letter-spacing: 0.15em !important; color: #2a2826 !important; appearance: none !important; -webkit-appearance: none !important; border-radius: 0 !important; cursor: pointer !important; margin: 0 !important; box-sizing: border-box !important; display: block !important; line-height: 54px !important; outline: none !important;">
                                    <?php
                                    $catalog_orderby_options = array(
                                        'menu_order' => 'Sortuj',
                                        'popularity' => 'Popularność',
                                        'date'       => 'Nowości',
                                        'price'      => 'Cena: rosnąco',
                                        'price-desc' => 'Cena: malejąco',
                                    );
                                    $orderby = isset($_GET['orderby']) ? wc_clean($_GET['orderby']) : 'menu_order';
                                    foreach ($catalog_orderby_options as $id => $name) : ?>
                                        <option value="<?php echo esc_attr($id); ?>" <?php selected($orderby, $id); ?>><?php echo esc_html($name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div style="position: absolute !important; right: 15px !important; top: 50% !important; transform: translateY(-50%) !important; pointer-events: none !important;">
                                    <svg width="10" height="10" fill="none" stroke="#2a2826" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
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
</script>

<?php get_footer(); ?>
