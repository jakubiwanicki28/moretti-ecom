<?php
/**
 * The template for displaying product content within loops
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

global $product;

// Ensure visibility.
if (empty($product) || !$product->is_visible()) {
    return;
}
?>
<li <?php wc_product_class('group relative', $product); ?>>
    <div class="product-card bg-white">
        
        <!-- Product Image with Gallery Slider -->
        <div class="relative overflow-hidden bg-gray-50 mb-3 product-image-slider" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
            <?php
            // Get product gallery images
            $gallery_image_ids = $product->get_gallery_image_ids();
            $main_image_id = $product->get_image_id();
            
            // Combine main image + gallery images
            $all_images = array();
            if ($main_image_id) {
                $all_images[] = $main_image_id;
            }
            if (!empty($gallery_image_ids)) {
                $all_images = array_merge($all_images, $gallery_image_ids);
            }
            
            // Only show slider if there are 2+ images
            $has_multiple_images = count($all_images) > 1;
            ?>
            
            <a href="<?php the_permalink(); ?>" class="block slider-images-wrapper">
                <?php if (!empty($all_images)) : ?>
                    <?php foreach ($all_images as $index => $image_id) : ?>
                        <div class="slider-image <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>">
                            <?php echo wp_get_attachment_image($image_id, 'woocommerce_thumbnail', false, array(
                                'class' => 'w-full h-auto object-cover aspect-[3/4] group-hover:opacity-90 transition-opacity'
                            )); ?>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <?php echo woocommerce_get_product_thumbnail('woocommerce_thumbnail', array('class' => 'w-full h-auto object-cover aspect-[3/4] group-hover:opacity-90 transition-opacity')); ?>
                <?php endif; ?>
            </a>

            <!-- Wishlist Heart Icon - Top Right -->
            <button 
                class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center text-charcoal hover:text-red-500 transition-colors bg-white/80 rounded-full backdrop-blur-sm z-10"
                aria-label="Add to wishlist"
                onclick="event.preventDefault(); alert('Wishlist feature coming soon!');"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </button>
            
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
                class="absolute bottom-3 right-3 w-8 h-8 flex items-center justify-center bg-white text-charcoal hover:bg-charcoal hover:text-white transition-all rounded-full shadow-md opacity-0 group-hover:opacity-100"
                aria-label="Quick add to cart"
                data-product-id="<?php echo esc_attr($product->get_id()); ?>"
                onclick="morettiQuickAddToCart(<?php echo esc_js($product->get_id()); ?>); event.preventDefault();"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
            </button>
        </div>

        <!-- Product Info -->
        <div class="product-info text-center">
            <!-- Product Title -->
            <h2 class="text-xs md:text-sm font-bold text-charcoal mb-2 hover:text-taupe-600 transition-colors uppercase tracking-wider leading-snug">
                <a href="<?php the_permalink(); ?>">
                    <?php the_title(); ?>
                </a>
            </h2>

            <!-- Product Price -->
            <div class="product-price text-sm md:text-base text-charcoal font-bold mb-3">
                <?php echo $product->get_price_html(); ?>
            </div>

            <!-- Color Swatches (if product has variations) -->
            <?php if ($product->is_type('variable')) : ?>
                <?php
                $available_variations = $product->get_available_variations();
                $color_attributes = array();
                
                // Get color attribute
                foreach ($product->get_variation_attributes() as $attribute_name => $options) {
                    if (stripos($attribute_name, 'color') !== false || stripos($attribute_name, 'colour') !== false) {
                        $color_attributes = $options;
                        break;
                    }
                }

                if (!empty($color_attributes)) : ?>
                    <div class="flex items-center gap-2 mt-2">
                        <?php foreach ($color_attributes as $index => $color) : 
                            if ($index >= 4) break; // Show max 4 colors
                            
                            // Try to map color names to hex values
                            $color_hex = moretti_get_color_hex($color);
                            ?>
                            <button 
                                class="w-5 h-5 rounded-full border border-gray-300 hover:border-charcoal transition-colors"
                                style="background-color: <?php echo esc_attr($color_hex); ?>;"
                                title="<?php echo esc_attr(ucfirst($color)); ?>"
                                aria-label="<?php echo esc_attr(ucfirst($color)); ?>"
                            ></button>
                        <?php endforeach; ?>
                        
                        <?php if (count($color_attributes) > 4) : ?>
                            <span class="text-xs text-taupe-600">+<?php echo count($color_attributes) - 4; ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

    </div>
</li>
