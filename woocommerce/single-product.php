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
        
        <!-- Breadcrumbs -->
        <?php if (function_exists('woocommerce_breadcrumb')) : ?>
            <div class="container mx-auto px-4 mb-6 text-sm text-taupe-600 pt-8 md:pt-12">
                <?php woocommerce_breadcrumb(array(
                    'delimiter' => ' <span class="mx-2">/</span> ',
                    'wrap_before' => '<nav class="woocommerce-breadcrumb">',
                    'wrap_after' => '</nav>',
                )); ?>
            </div>
        <?php endif; ?>
        
        <div class="flex flex-col md:grid md:grid-cols-2 md:gap-12 lg:gap-16">
            
            <!-- Product Images - Full width on mobile, no padding -->
            <div class="product-images w-full md:px-4">
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
            
            <!-- Product Info - With padding on mobile -->
            <div class="product-summary-custom px-4 py-6 md:px-4 md:py-0">
                    
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
                    
                    <!-- Meta (SKU, Kategorie) -->
                    <div class="product-meta-custom border-t border-gray-200 pt-6 space-y-2 text-xs text-taupe-600">
                        <?php if (wc_product_sku_enabled() && ($product->get_sku() || $product->is_type('variable'))) : ?>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-charcoal uppercase tracking-wider">SKU:</span>
                                <span class="sku"><?php echo $product->get_sku() ? $product->get_sku() : 'Brak'; ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php echo wc_get_product_category_list($product->get_id(), ', ', '<div class="flex items-center gap-2"><span class="font-semibold text-charcoal uppercase tracking-wider">KATEGORIA:</span> ', '</div>'); ?>
                    </div>
                    
                    <!-- Additional Info Accordion -->
                    <div class="product-accordion mt-8 space-y-3">
                        <!-- Opis -->
                        <details class="border-t border-gray-200 pt-4" open>
                            <summary class="cursor-pointer text-charcoal font-medium flex items-center justify-between text-xs uppercase tracking-[0.2em]">
                                <span>OPIS</span>
                                <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </summary>
                            <div class="mt-4 text-taupe-700 text-sm leading-relaxed">
                                <?php the_content(); ?>
                            </div>
                        </details>
                        
                        <!-- Rozmiar -->
                        <details class="border-t border-gray-200 pt-4">
                            <summary class="cursor-pointer text-charcoal font-medium flex items-center justify-between text-xs uppercase tracking-[0.2em]">
                                <span>WYMIARY I DOPASOWANIE</span>
                                <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </summary>
                            <div class="mt-4 text-taupe-700 text-sm space-y-2">
                                <p>Nasz produkt został zaprojektowany z myślą o ergonomii i codziennym użytkowaniu.</p>
                                <p>Dokładne wymiary znajdziesz w specyfikacji technicznej produktu.</p>
                            </div>
                        </details>
                        
                        <!-- Pielęgnacja -->
                        <details class="border-t border-gray-200 pt-4">
                            <summary class="cursor-pointer text-charcoal font-medium flex items-center justify-between text-xs uppercase tracking-[0.2em]">
                                <span>PIELĘGNACJA</span>
                                <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </summary>
                            <div class="mt-4 text-taupe-700 text-sm space-y-1">
                                <p>• Czyścić wyłącznie miękką szmatką</p>
                                <p>• Unikać nadmiernego kontaktu z wodą</p>
                                <p>• Przechowywać w suchym miejscu</p>
                            </div>
                        </details>
                        
                        <!-- Dostawa i Zwroty -->
                        <details class="border-t border-gray-200 pt-4 pb-4 border-b">
                            <summary class="cursor-pointer text-charcoal font-medium flex items-center justify-between text-xs uppercase tracking-[0.2em]">
                                <span>DOSTAWA I ZWROTY</span>
                                <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </summary>
                            <div class="mt-4 text-taupe-700 text-sm space-y-2">
                                <p><strong>Darmowa dostawa</strong> dla zamówień powyżej 250 zł</p>
                                <p><strong>Szybki zwrot</strong> do 30 dni</p>
                                <p>Wysyłka w ciągu 24-48 godzin</p>
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
            <div class="related-products container mx-auto px-4 mt-16 md:mt-24">
                <?php woocommerce_output_related_products(); ?>
            </div>
        
    </div>

<?php endwhile; ?>

    </main>
</div>

<?php get_footer(); ?>
