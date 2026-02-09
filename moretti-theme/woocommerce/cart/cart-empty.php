<?php
/**
 * Empty cart page
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

// Usunięcie domyślnych wiadomości WooCommerce, które mogą nadpisywać nasz UI
remove_action('woocommerce_cart_is_empty', 'wc_empty_cart_message', 10);
?>

<div id="moretti-empty-cart-override" style="display: block !important; visibility: visible !important; opacity: 1 !important; position: relative !important; z-index: 99 !important; padding: 20px 0 100px 0 !important; text-align: center !important; background-color: #ffffff !important; width: 100% !important; min-height: 400px !important; font-family: sans-serif !important;">
    <div style="max-width: 800px !important; margin: 0 auto !important; padding: 0 48px !important;">
        <div style="margin-bottom: 20px !important;">
            <svg style="width: 80px !important; height: 80px !important; margin: 0 auto !important; color: #2a2826 !important; display: block !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
        </div>
        
        <h2 style="font-size: 48px !important; font-weight: 700 !important; color: #2a2826 !important; margin: 0 0 16px 0 !important; text-transform: uppercase !important; letter-spacing: -0.05em !important; line-height: 1 !important;">
            TWÓJ KOSZYK JEST PUSTY
        </h2>
        
        <p style="color: #2a2826 !important; font-size: 18px !important; margin: 0 0 48px 0 !important; line-height: 1.4 !important; font-weight: 600 !important; max-width: 700px !important; margin-left: auto !important; margin-right: auto !important; text-transform: uppercase !important; letter-spacing: -0.02em !important;">
            Twój koszyk jest obecnie pusty!
        </p>
        
        <a href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>" 
           style="display: inline-block !important; padding: 20px 60px !important; background-color: #2a2826 !important; color: #ffffff !important; text-decoration: none !important; font-weight: 700 !important; font-size: 12px !important; text-transform: uppercase !important; letter-spacing: 0.2em !important; border: none !important; cursor: pointer !important;">
            WRÓĆ DO SKLEPU
        </a>
    </div>

    <!-- Recommended Products Section -->
    <div style="margin-top: 84px !important; padding-top: 80px !important; border-top: 1px solid #f3f4f6 !important; max-width: 1280px !important; margin-left: auto !important; margin-right: auto !important; padding-left: 48px !important; padding-right: 48px !important; display: block !important;">
        <h3 style="font-size: 28px !important; font-weight: 700 !important; color: #2a2826 !important; margin: 0 0 48px 0 !important; text-transform: uppercase !important; letter-spacing: -0.05em !important; text-align: center !important;">
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
            <div style="display: flex !important; flex-wrap: wrap !important; justify-content: center !important; gap: 48px !important; width: 100% !important;">
                <?php while ($products_query->have_posts()) : $products_query->the_post(); 
                    global $product;
                    $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'medium');
                    $url = get_permalink();
                    $title = get_the_title();
                    $price = $product->get_price_html();
                ?>
                    <div style="flex: 1 !important; min-width: 250px !important; max-width: 280px !important; text-align: center !important; margin-bottom: 40px !important;">
                        <a href="<?php echo $url; ?>" style="text-decoration: none !important; display: block !important;">
                            <div style="background-color: #f9f9f9 !important; margin-bottom: 20px !important; aspect-ratio: 1/1 !important; overflow: hidden !important;">
                                <?php if ($image) : ?>
                                    <img src="<?php echo $image[0]; ?>" style="width: 100% !important; height: 100% !important; object-fit: cover !important;">
                                <?php endif; ?>
                            </div>
                            <h4 style="font-size: 14px !important; color: #2a2826 !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; margin-bottom: 8px !important; font-weight: 500 !important;"><?php echo $title; ?></h4>
                            <div style="font-size: 14px !important; color: #2a2826 !important; font-weight: 700 !important;"><?php echo $price; ?></div>
                        </a>
                        <a href="<?php echo $url; ?>" style="display: inline-block !important; margin-top: 20px !important; padding: 12px 32px !important; background-color: #2a2826 !important; color: #ffffff !important; text-decoration: none !important; font-size: 10px !important; font-weight: 700 !important; text-transform: uppercase !important; letter-spacing: 0.1em !important;">Wybierz opcje</a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; wp_reset_postdata(); ?>
    </div>
</div>
