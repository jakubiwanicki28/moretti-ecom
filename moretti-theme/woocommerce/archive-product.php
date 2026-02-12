<?php
/**
 * Shop Page - Clean Custom Template
 * Zero WooCommerce hooks, full design control
 * 
 * @package Moretti
 */

defined('ABSPATH') || exit;

get_header();

// Get all products
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
    'post_type' => 'product',
    'posts_per_page' => 12,
    'paged' => $paged,
    'post_status' => 'publish',
);

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
if (!empty($_GET['filter_color'])) {
    $tax_query[] = array(
        'taxonomy' => 'pa_color',
        'field' => 'slug',
        'terms' => sanitize_text_field($_GET['filter_color']),
    );
}

if (!empty($_GET['filter_material'])) {
    $tax_query[] = array(
        'taxonomy' => 'pa_material',
        'field' => 'slug',
        'terms' => sanitize_text_field($_GET['filter_material']),
    );
}

if (!empty($_GET['filter_size'])) {
    $tax_query[] = array(
        'taxonomy' => 'pa_wielkosc',
        'field' => 'slug',
        'terms' => sanitize_text_field($_GET['filter_size']),
    );
}

if (count($tax_query) > 1) {
    $args['tax_query'] = $tax_query;
}

// Handle price filter
if (!empty($_GET['min_price']) || !empty($_GET['max_price'])) {
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
}
?>

<div class="shop-page">
    <div class="shop-container">
        
        <!-- Sidebar -->
        <aside class="shop-sidebar" id="shop-sidebar">
            
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
            $colors = get_terms(array(
                'taxonomy' => 'pa_color',
                'hide_empty' => true,
            ));
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
                        $is_active = isset($_GET['filter_color']) && $_GET['filter_color'] === $color->slug;
                        // Map color names to hex
                        $color_hex = moretti_get_color_hex($color->name);
                    ?>
                        <a href="<?php echo $is_active ? esc_url(remove_query_arg('filter_color')) : esc_url(add_query_arg('filter_color', $color->slug)); ?>" 
                           class="filter-option <?php echo $is_active ? 'active' : ''; ?>">
                            <span class="color-dot" style="background-color: <?php echo esc_attr($color_hex); ?>; <?php echo $color_hex === '#FFFFFF' ? 'border: 2px solid #e5e7eb;' : ''; ?>"></span>
                            <?php echo esc_html($color->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Material Filter -->2
            <?php
            $materials = get_terms(array(
                'taxonomy' => 'pa_material',
                'hide_empty' => true,
            ));
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
                        $is_active = isset($_GET['filter_material']) && $_GET['filter_material'] === $material->slug;
                    ?>
                        <a href="<?php echo esc_url(add_query_arg('filter_material', $material->slug)); ?>" 
                           class="filter-option <?php echo $is_active ? 'active' : ''; ?>">
                            <?php echo esc_html($material->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Size Filter -->
            <?php
            $sizes = get_terms(array(
                'taxonomy' => 'pa_wielkosc',
                'hide_empty' => true,
            ));
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
                        $is_active = isset($_GET['filter_size']) && $_GET['filter_size'] === $size->slug;
                    ?>
                        <a href="<?php echo esc_url(add_query_arg('filter_size', $size->slug)); ?>" 
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
                <form method="get" class="price-form">
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
            <?php if (!empty($_GET['filter_color']) || !empty($_GET['filter_material']) || !empty($_GET['filter_size']) || !empty($_GET['min_price']) || !empty($_GET['max_price'])) : ?>
            <div class="sidebar-block">
                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="clear-all-btn">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Wyczyść wszystkie filtry
                </a>
            </div>
            <?php endif; ?>

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
                <?php else : ?>
                    <span class="current">Sklep</span>
                <?php endif; ?>
            </nav>
            
            <!-- Top Bar -->
            <div class="shop-topbar">
                <div class="shop-title-wrap">
                    <h1 class="shop-title"><?php echo esc_html($page_title); ?></h1>
                    <span class="shop-count"><?php echo $products->found_posts; ?> produktów</span>
                </div>
                
                <div class="shop-actions">
                    <!-- Filter Button -->
                    <button type="button" class="action-btn action-filter" id="filter-toggle">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filtry
                    </button>
                    
                    <!-- Sort Dropdown -->
                    <div class="action-dropdown">
                        <button type="button" class="action-btn action-sort" id="sort-toggle">
                            <span id="sort-label">
                                <?php
                                $sort_options = array(
                                    'menu_order' => 'Sortuj',
                                    'popularity' => 'Popularność',
                                    'date' => 'Nowości',
                                    'price' => 'Cena: rosnąco',
                                    'price-desc' => 'Cena: malejąco',
                                );
                                echo esc_html($sort_options[$orderby]);
                                ?>
                            </span>
                            <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="sort-arrow">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="dropdown-menu" id="sort-menu">
                            <?php foreach ($sort_options as $key => $label) : ?>
                                <a href="<?php echo esc_url(add_query_arg('orderby', $key)); ?>" 
                                   class="dropdown-item <?php echo $orderby === $key ? 'active' : ''; ?>"
                                   data-value="<?php echo esc_attr($key); ?>">
                                    <?php echo esc_html($label); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
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
                                
                                    <div class="product-image <?php echo $has_gallery ? 'has-gallery' : ''; ?>">
                                        <?php if ($image_count > 0) : ?>
                                            <?php foreach ($all_images as $index => $image_id) : ?>
                                                <div class="product-image-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>">
                                                    <a href="<?php echo esc_url(get_permalink()); ?>">
                                                        <?php echo wp_get_attachment_image($image_id, 'medium'); ?>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter Toggle (Mobile)
    const filterToggle = document.getElementById('filter-toggle');
    const sidebar = document.getElementById('shop-sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    if (filterToggle && sidebar && overlay) {
        filterToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        });
        
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        });
    }
    
    // Sort Dropdown
    const sortToggle = document.getElementById('sort-toggle');
    const sortMenu = document.getElementById('sort-menu');
    
    if (sortToggle && sortMenu) {
        sortToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sortMenu.classList.toggle('open');
        });
        
        document.addEventListener('click', function() {
            sortMenu.classList.remove('open');
        });
    }
    
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
            const productId = this.dataset.productId;
            const btn = this;
            
            btn.classList.add('loading');
            btn.disabled = true;
            
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'moretti_quick_add_to_cart',
                    product_id: productId,
                    nonce: '<?php echo wp_create_nonce('moretti-nonce'); ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                btn.classList.remove('loading');
                
                if (data.success) {
                    btn.classList.add('added');
                    
                    setTimeout(() => {
                        btn.classList.remove('added');
                        btn.disabled = false;
                    }, 2000);
                    
                    // Update cart count
                    if (data.data && data.data.cart_count) {
                        const cartCountEl = document.querySelector('header .absolute.top-1.right-1');
                        if (cartCountEl) {
                            cartCountEl.textContent = data.data.cart_count;
                            if (!cartCountEl.classList.contains('flex')) {
                                cartCountEl.style.display = 'flex';
                            }
                        }
                    }
                } else {
                    btn.disabled = false;
                    alert(data.data && data.data.message ? data.data.message : 'Błąd dodawania do koszyka');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.classList.remove('loading');
                btn.disabled = false;
                alert('Błąd połączenia. Spróbuj ponownie.');
            });
        });
    });
});
</script>

<?php get_footer(); ?>
