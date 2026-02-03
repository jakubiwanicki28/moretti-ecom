<?php
/**
 * Single Product Template
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

<?php while (have_posts()) : the_post(); ?>

    <div class="single-product-wrapper bg-white">
        
        <?php global $product; ?>
        
        <div class="container mx-auto px-4 py-8 md:py-12">
            
            <!-- Breadcrumbs -->
            <?php if (function_exists('woocommerce_breadcrumb')) : ?>
                <div class="mb-6 text-sm text-taupe-600">
                    <?php woocommerce_breadcrumb(array(
                        'delimiter' => ' <span class="mx-2">/</span> ',
                        'wrap_before' => '<nav class="woocommerce-breadcrumb">',
                        'wrap_after' => '</nav>',
                    )); ?>
                </div>
            <?php endif; ?>
            
            <div class="grid md:grid-cols-2 gap-8 md:gap-12 lg:gap-16">
                
                <!-- Product Images -->
                <div class="product-images">
                    <?php
                    /**
                     * Hook: woocommerce_before_single_product_summary.
                     *
                     * @hooked woocommerce_show_product_sale_flash - 10
                     * @hooked woocommerce_show_product_images - 20
                     */
                    do_action('woocommerce_before_single_product_summary');
                    ?>
                </div>
                
                <!-- Product Info -->
                <div class="product-summary-custom">
                    
                    <!-- Title - BIG AND BOLD -->
                    <h1 class="product-title-custom text-2xl md:text-4xl lg:text-5xl font-bold text-charcoal uppercase tracking-wide mb-4">
                        <?php the_title(); ?>
                    </h1>
                    
                    <!-- Price - BIG AND BOLD -->
                    <div class="product-price-custom text-xl md:text-2xl lg:text-3xl font-bold text-charcoal mb-6">
                        <?php echo $product->get_price_html(); ?>
                    </div>
                    
                    <!-- Short Description -->
                    <?php if ($product->get_short_description()) : ?>
                        <div class="product-description-custom text-sm md:text-base text-taupe-700 leading-relaxed mb-8">
                            <?php echo wpautop($product->get_short_description()); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Add to Cart Form -->
                    <div class="product-cart-form-custom mb-8">
                        <?php woocommerce_template_single_add_to_cart(); ?>
                    </div>
                    
                    <!-- Meta (SKU, Categories) -->
                    <div class="product-meta-custom border-t border-gray-200 pt-6 space-y-2 text-xs text-taupe-600">
                        <?php if (wc_product_sku_enabled() && ($product->get_sku() || $product->is_type('variable'))) : ?>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-charcoal uppercase tracking-wider">SKU:</span>
                                <span class="sku"><?php echo $product->get_sku() ? $product->get_sku() : __('N/A', 'moretti'); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php echo wc_get_product_category_list($product->get_id(), ', ', '<div class="flex items-center gap-2"><span class="font-semibold text-charcoal uppercase tracking-wider">' . _n('Category:', 'Categories:', count($product->get_category_ids()), 'moretti') . '</span> ', '</div>'); ?>
                    </div>
                    
                    <!-- Additional Info Accordion -->
                    <div class="product-accordion mt-8 space-y-3">
                        <!-- Description -->
                        <details class="border-t border-gray-200 pt-4">
                            <summary class="cursor-pointer text-charcoal font-medium flex items-center justify-between text-xs uppercase tracking-[0.2em]">
                                <span>DESCRIPTION</span>
                                <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </summary>
                            <div class="mt-4 text-taupe-700 text-sm leading-relaxed">
                                <?php the_content(); ?>
                            </div>
                        </details>
                        
                        <!-- Size & Fit -->
                        <details class="border-t border-gray-200 pt-4">
                            <summary class="cursor-pointer text-charcoal font-medium flex items-center justify-between text-xs uppercase tracking-[0.2em]">
                                <span>SIZE & FIT</span>
                                <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </summary>
                            <div class="mt-4 text-taupe-700 text-sm space-y-2">
                                <p>Model is wearing size S</p>
                                <p>Model measurements: Height 5'9", Bust 32", Waist 24", Hips 35"</p>
                                <p>Fits true to size</p>
                            </div>
                        </details>
                        
                        <!-- Care Instructions -->
                        <details class="border-t border-gray-200 pt-4">
                            <summary class="cursor-pointer text-charcoal font-medium flex items-center justify-between text-xs uppercase tracking-[0.2em]">
                                <span>CARE INSTRUCTIONS</span>
                                <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </summary>
                            <div class="mt-4 text-taupe-700 text-sm space-y-1">
                                <p>• Dry clean only</p>
                                <p>• Do not bleach</p>
                                <p>• Iron on low heat</p>
                                <p>• Store folded or hanging</p>
                            </div>
                        </details>
                        
                        <!-- Shipping & Returns -->
                        <details class="border-t border-gray-200 pt-4 pb-4 border-b">
                            <summary class="cursor-pointer text-charcoal font-medium flex items-center justify-between text-xs uppercase tracking-[0.2em]">
                                <span>SHIPPING & RETURNS</span>
                                <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </summary>
                            <div class="mt-4 text-taupe-700 text-sm space-y-2">
                                <p><strong>Free shipping</strong> on orders over $60</p>
                                <p><strong>Easy returns</strong> within 30 days</p>
                                <p>Ships within 2-3 business days</p>
                            </div>
                        </details>
                    </div>
                    
                </div>
                
            </div>
            
            <!-- Related Products -->
            <?php
            /**
             * Hook: woocommerce_after_single_product_summary.
             *
             * @hooked woocommerce_output_product_data_tabs - 10
             * @hooked woocommerce_upsell_display - 15
             * @hooked woocommerce_output_related_products - 20
             */
            ?>
            <div class="related-products mt-16 md:mt-24">
                <?php woocommerce_output_related_products(); ?>
            </div>
            
        </div>
        
    </div>

<?php endwhile; ?>

    </main>
</div>

<?php get_footer(); ?>
