<?php
/**
 * The template for displaying product content within loops
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

global $product;
$is_home_carousel = (bool) get_query_var('moretti_home_carousel', false);

// Guarantee product object in custom loops (homepage uses WP_Query).
if (empty($product) && get_the_ID()) {
    $product = wc_get_product(get_the_ID());
}

if (empty($product)) {
    return;
}

$color_variants = function_exists('moretti_get_product_color_variants')
    ? moretti_get_product_color_variants($product)
    : array();
$color_variants_count = is_array($color_variants) ? count($color_variants) : 0;

// Keep WooCommerce visibility rules outside homepage custom carousels.
if (!$is_home_carousel && !$product->is_visible()) {
    return;
}

if ($is_home_carousel) :
    $gallery_image_ids = $product->get_gallery_image_ids();
    $main_image_id = $product->get_image_id();
    $homepage_first_image_id = moretti_get_product_homepage_carousel_image_id($product->get_id());
    $home_placeholder_src = wc_placeholder_img_src();
    if (empty($home_placeholder_src)) {
        $home_placeholder_src = 'data:image/svg+xml;utf8,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 800"><rect width="600" height="800" fill="#f7f5f2"/><rect x="170" y="250" width="260" height="220" fill="none" stroke="#d6d1ca" stroke-width="8"/><circle cx="270" cy="320" r="28" fill="none" stroke="#d6d1ca" stroke-width="8"/><path d="M190 430l85-92 65 66 40-40 40 66" fill="none" stroke="#d6d1ca" stroke-width="8"/></svg>');
    }

    // Gallery order first (no preview/featured image as first); then main/homepage if not in gallery.
    $all_images = array();
    if (!empty($gallery_image_ids)) {
        $all_images = array_map('absint', $gallery_image_ids);
        if ($main_image_id && !in_array((int) $main_image_id, $all_images)) {
            $all_images[] = (int) $main_image_id;
        }
        if ($homepage_first_image_id && !in_array((int) $homepage_first_image_id, $all_images)) {
            $all_images[] = (int) $homepage_first_image_id;
        }
    } else {
        if ($homepage_first_image_id) {
            $all_images[] = (int) $homepage_first_image_id;
        }
        if ($main_image_id) {
            $all_images[] = (int) $main_image_id;
        }
    }
    $all_images = array_values(array_unique(array_filter($all_images)));
    $valid_image_ids = array();
    foreach ($all_images as $candidate_image_id) {
        if (wp_get_attachment_image_url($candidate_image_id, 'large')) {
            $valid_image_ids[] = $candidate_image_id;
        }
    }
    $image_count = count($valid_image_ids);
    $has_gallery = $image_count > 1;
    ?>
    <li <?php wc_product_class('group relative', $product); ?>>
        <div class="product-card bg-white moretti-card-43 <?php echo $has_gallery ? 'has-hover-second-image' : ''; ?>">
            <div class="product-image-wrapper">
                <div class="product-image <?php echo $has_gallery ? 'has-gallery' : ''; ?>" style="aspect-ratio: 3 / 4;">
                    <div class="product-image-interior-hover-zone" aria-hidden="true"></div>
                    <?php if ($image_count > 0) : ?>
                        <?php foreach ($valid_image_ids as $index => $image_id) : ?>
                            <div class="product-image-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo esc_attr($index); ?>">
                                <a href="<?php the_permalink(); ?>">
                                    <?php echo wp_get_attachment_image($image_id, 'large', false, array(
                                        'class' => 'w-full h-full object-contain group-hover:opacity-90 transition-opacity',
                                    )); ?>
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
                                    <span class="image-dot <?php echo $i === 0 ? 'active' : ''; ?>" data-index="<?php echo esc_attr($i); ?>"></span>
                                <?php endfor; ?>
                            </div>
                        <?php endif; ?>

                    <?php else : ?>
                        <div class="product-image-slide active" data-index="0">
                            <a href="<?php the_permalink(); ?>">
                                <img
                                    src="<?php echo esc_url($home_placeholder_src); ?>"
                                    alt="<?php echo esc_attr(get_the_title()); ?>"
                                    class="w-full h-full object-contain group-hover:opacity-90 transition-opacity"
                                >
                            </a>
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

                    <?php if (!empty($color_variants)) : ?>
                        <div class="sku-color-variants" aria-label="Dostępne warianty kolorystyczne">
                            <?php foreach ($color_variants as $variant) : ?>
                                <a
                                    class="sku-color-dot <?php echo !empty($variant['is_current']) ? 'is-current' : ''; ?>"
                                    href="<?php echo esc_url($variant['url']); ?>"
                                    style="background-color: <?php echo esc_attr($variant['color_hex']); ?>;"
                                    aria-label="<?php echo esc_attr($variant['color_label']); ?>"
                                    title="<?php echo esc_attr($variant['color_label']); ?>"
                                    <?php if (!empty($variant['first_image_url'])) : ?>data-first-image-url="<?php echo esc_url($variant['first_image_url']); ?>"<?php endif; ?>
                                >
                                    <span class="screen-reader-text"><?php echo esc_html($variant['color_label']); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($color_variants_count === 1) : ?>
                        <div class="mt-2 text-[10px] md:text-xs font-medium uppercase tracking-[0.15em] text-charcoal/70">
                            Tylko jeden kolor
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="product-info">
                <h3 class="product-name">
                    <a href="<?php the_permalink(); ?>">
                        <?php the_title(); ?>
                    </a>
                </h3>
                <div class="product-price">
                    <?php echo $product->get_price_html(); ?>
                </div>
            </div>
        </div>
    </li>
    <?php
    return;
endif;
?>
<li <?php wc_product_class('group relative', $product); ?>>
    <div class="product-card bg-white moretti-card-43">
        
        <!-- Product Image with Gallery Slider -->
        <div class="relative overflow-hidden bg-gray-50 mb-3 product-image-slider moretti-card-media" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
            <?php
            // Get product gallery images
            $gallery_image_ids = $product->get_gallery_image_ids();
            $main_image_id = $product->get_image_id();

            // Homepage carousel can use dedicated first image per product.
            $homepage_first_image_id = 0;

            // Gallery order first (no preview/featured image as first); then main if not in gallery.
            $all_images = array();
            if (!empty($gallery_image_ids)) {
                $all_images = array_map('absint', $gallery_image_ids);
                if ($main_image_id && !in_array((int) $main_image_id, $all_images)) {
                    $all_images[] = (int) $main_image_id;
                }
            } else {
                if ($main_image_id) {
                    $all_images[] = (int) $main_image_id;
                }
            }
            $all_images = array_values(array_unique(array_filter($all_images)));
            
            // Only show slider if there are 2+ images
            $has_multiple_images = count($all_images) > 1;
            ?>
            
            <a href="<?php the_permalink(); ?>" class="block slider-images-wrapper moretti-card-media-link" style="aspect-ratio: 3 / 4;">
                <?php if (!empty($all_images)) : ?>
                    <?php foreach ($all_images as $index => $image_id) : ?>
                        <div class="slider-image <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>">
                            <?php echo wp_get_attachment_image($image_id, 'large', false, array(
                                'class' => 'w-full h-full object-contain group-hover:opacity-90 transition-opacity'
                            )); ?>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <?php echo woocommerce_get_product_thumbnail('large', array('class' => 'w-full h-full object-contain group-hover:opacity-90 transition-opacity')); ?>
                <?php endif; ?>
            </a>

            <!-- Wishlist Heart Icon - Top Right -->
            <button 
                type="button"
                class="wishlist-toggle absolute top-3 right-3 w-8 h-8 flex items-center justify-center text-charcoal hover:text-red-500 transition-colors bg-white/80 rounded-full backdrop-blur-sm z-10"
                data-product-id="<?php echo esc_attr($product->get_id()); ?>"
                aria-label="Dodaj do ulubionych"
                aria-pressed="false"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </button>

            <?php if (!empty($color_variants)) : ?>
                <div class="sku-color-variants" aria-label="Dostępne warianty kolorystyczne">
                    <?php foreach ($color_variants as $variant) : ?>
                        <a
                            class="sku-color-dot <?php echo !empty($variant['is_current']) ? 'is-current' : ''; ?>"
                            href="<?php echo esc_url($variant['url']); ?>"
                            style="background-color: <?php echo esc_attr($variant['color_hex']); ?>;"
                            aria-label="<?php echo esc_attr($variant['color_label']); ?>"
                            title="<?php echo esc_attr($variant['color_label']); ?>"
                        >
                            <span class="screen-reader-text"><?php echo esc_html($variant['color_label']); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($has_multiple_images) : ?>
                <!-- Previous Arrow -->
                <button 
                    class="slider-arrow slider-prev absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center bg-white/90 hover:bg-white text-charcoal rounded-full shadow-md opacity-0 group-hover:opacity-100 transition-opacity z-10"
                    onclick="morettiSliderPrev(this); event.preventDefault();"
                    aria-label="Previous image"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                
                <!-- Next Arrow -->
                <button 
                    class="slider-arrow slider-next absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center bg-white/90 hover:bg-white text-charcoal rounded-full shadow-md opacity-0 group-hover:opacity-100 transition-opacity z-10"
                    onclick="morettiSliderNext(this); event.preventDefault();"
                    aria-label="Next image"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                
                <!-- Image Dots Indicator -->
                <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 z-10">
                    <?php foreach ($all_images as $index => $image_id) : ?>
                        <div class="slider-dot w-1.5 h-1.5 rounded-full bg-white/60 transition-all <?php echo $index === 0 ? 'bg-white w-4' : ''; ?>" data-index="<?php echo $index; ?>"></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Quick Add Button - Bottom Right -->
            <button 
                class="add_to_cart_button ajax_add_to_cart absolute bottom-3 right-3 w-8 h-8 flex items-center justify-center bg-white text-charcoal hover:bg-charcoal hover:text-white transition-all rounded-full shadow-md opacity-0 group-hover:opacity-100"
                aria-label="Quick add to cart"
                data-product-id="<?php echo esc_attr($product->get_id()); ?>"
                data-product-type="<?php echo esc_attr($product->get_type()); ?>"
                data-product-url="<?php echo esc_url($product->get_permalink()); ?>"
                onclick="morettiQuickAddToCart(<?php echo esc_js($product->get_id()); ?>, this); event.preventDefault();"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
            </button>
        </div>

        <!-- Product Info -->
        <div class="product-info text-left pt-2 flex flex-col gap-0">
            <!-- Product Title -->
            <h2 class="text-xs md:text-sm font-bold text-charcoal mb-0.5 hover:text-taupe-600 transition-colors leading-tight">
                <a href="<?php the_permalink(); ?>">
                    <?php the_title(); ?>
                </a>
            </h2>

            <!-- Product Price -->
            <div class="product-price text-sm md:text-base text-charcoal font-semibold mb-0 mt-0">
                <?php echo $product->get_price_html(); ?>
            </div>

        </div>

    </div>
</li>
