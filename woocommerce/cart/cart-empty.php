<?php
/**
 * Empty cart page
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

/*
 * @hooked wc_empty_cart_message - 10
 */
?>

<div class="cart-empty-container py-20 md:py-32 text-center bg-white">
    <div class="max-w-2xl mx-auto px-4">
        <div class="mb-12">
            <svg class="w-20 h-20 mx-auto text-taupe-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
        </div>
        
        <h2 class="text-4xl md:text-6xl font-bold text-charcoal mb-6 uppercase tracking-tighter leading-none">
            TWÓJ KOSZYK JEST PUSTY
        </h2>
        
        <p class="text-taupe-600 mb-12 leading-relaxed font-medium">
            Wygląda na to, że nie dodałeś jeszcze żadnego portfela do swojego zamówienia. 
            Odkryj naszą kolekcję rzemieślniczych produktów i znajdź swój nowy ulubiony dodatek.
        </p>
        
        <a href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>" 
           class="inline-block px-12 py-5 bg-charcoal text-white hover:bg-taupe-800 transition-all font-bold text-xs uppercase tracking-widest">
            WRÓĆ DO SKLEPU
        </a>
    </div>

    <!-- Recommended Products Section -->
    <div class="mt-40 pt-20 border-t border-gray-100 container mx-auto px-4">
        <h3 class="text-3xl md:text-4xl font-bold text-charcoal mb-12 uppercase tracking-tighter">
            NOWOŚCI W SKLEPIE
        </h3>
        
        <?php
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 4,
            'orderby' => 'date',
            'order' => 'DESC',
        );
        $products_query = new WP_Query($args);
        
        if ($products_query->have_posts()) : ?>
            <ul class="products grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12">
                <?php while ($products_query->have_posts()) : $products_query->the_post(); ?>
                    <?php wc_get_template_part('content', 'product'); ?>
                <?php endwhile; ?>
            </ul>
        <?php endif; wp_reset_postdata(); ?>
    </div>
</div>
<?php
do_action('woocommerce_cart_is_empty');
?>
