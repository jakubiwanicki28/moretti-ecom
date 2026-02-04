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

<div class="cart-empty-container py-12 text-center">
    <div class="mb-8">
        <div class="inline-block p-6 rounded-full bg-sand-50 mb-6 text-taupe-400">
            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
        </div>
        <h2 class="text-2xl md:text-3xl font-light text-charcoal mb-4 uppercase tracking-widest">
            Twój koszyk jest pusty
        </h2>
        <p class="text-taupe-700 mb-10 max-w-md mx-auto leading-relaxed">
            Wygląda na to, że nie dodałeś jeszcze żadnego portfela do swojego zamówienia. 
            Odkryj naszą kolekcję i znajdź swój nowy ulubiony dodatek.
        </p>
        
        <a href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>" 
           class="inline-block px-10 py-4 bg-charcoal text-white hover:bg-taupe-800 transition-colors font-bold text-sm uppercase tracking-widest">
            Wróć do sklepu
        </a>
    </div>

    <!-- Recommended Products Section -->
    <div class="mt-24 pt-16 border-t border-gray-100">
        <h3 class="text-xl md:text-2xl font-light text-charcoal mb-8 uppercase tracking-widest">
            Nowe w sklepie
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
            <ul class="products grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
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
