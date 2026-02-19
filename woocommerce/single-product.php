<?php
/**
 * Single Product Template
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

get_header(); ?>

<style>
    /* Force restricted width for single product */
    .single-product-main {
        max-width: 1320px !important;
        margin-left: auto !important;
        margin-right: auto !important;
        width: 100% !important;
        display: block !important;
    }
    .product-summary-custom {
        max-width: 560px !important;
    }
    @media (min-width: 768px) {
        .product-summary-custom {
            margin-left: auto !important;
        }
    }
    .main-product-image-frame {
        width: 100% !important;
        aspect-ratio: 1 / 1 !important;
        border: 1px solid #f3f4f6 !important;
        background: #ffffff !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        overflow: hidden !important;
    }
    .main-product-image-el {
        width: 100% !important;
        height: 100% !important;
        object-fit: contain !important;
    }
    .single-gallery-arrow {
        position: absolute !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        width: 36px !important;
        height: 36px !important;
        border: 1px solid #e5e7eb !important;
        background: rgba(255, 255, 255, 0.92) !important;
        color: #2a2826 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        z-index: 12 !important;
        transition: all 0.2s ease !important;
    }
    .single-gallery-prev {
        left: 10px !important;
    }
    .single-gallery-next {
        right: 10px !important;
    }
    .single-gallery-arrow:hover {
        background: #ffffff !important;
        border-color: #d1d5db !important;
    }
    .product-thumbnails {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 10px !important;
        margin-top: 15px !important;
    }
    .thumbnail-item {
        width: 86px !important;
        height: 86px !important;
        border: 1px solid #e5e7eb !important;
        background: #ffffff !important;
        overflow: hidden !important;
        cursor: pointer !important;
        padding: 0 !important;
        transition: border-color 0.2s ease !important;
    }
    .thumbnail-item.is-active {
        border-color: #2a2826 !important;
        border-width: 2px !important;
    }
    @media (max-width: 767px) {
        .single-product-wrapper {
            padding-top: 1.5rem !important;
        }
        .main-product-image-el {
            object-fit: cover !important;
        }
        .product-summary-custom {
            margin-top: 1rem !important;
        }
        .product-thumbnails {
            justify-content: center !important;
            gap: 6px !important;
            margin-top: 10px !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            overflow-y: hidden !important;
            -webkit-overflow-scrolling: touch;
        }
        .thumbnail-item {
            width: 22px !important;
            height: 22px !important;
            min-width: 22px !important;
            min-height: 22px !important;
            max-width: 22px !important;
            max-height: 22px !important;
            aspect-ratio: 1 / 1 !important;
            border-width: 1px !important;
            opacity: 0.55;
            flex: 0 0 22px !important;
            line-height: 0 !important;
            display: block !important;
        }
        .thumbnail-item img {
            opacity: 1 !important;
            display: block !important;
            width: 100% !important;
            height: 100% !important;
            min-width: 100% !important;
            min-height: 100% !important;
            aspect-ratio: 1 / 1 !important;
            object-fit: cover !important;
        }
        .thumbnail-item.is-active {
            opacity: 1;
            border-width: 2px !important;
        }
        .single-gallery-arrow {
            width: 32px !important;
            height: 32px !important;
        }
        .related-products .product-image-slider .slider-arrow {
            opacity: 1 !important;
            width: 16px !important;
            height: 16px !important;
            min-width: 16px !important;
            min-height: 16px !important;
            padding: 0 !important;
        }
        .related-products .product-image-slider .slider-arrow svg {
            width: 10px !important;
            height: 10px !important;
        }
    }
</style>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

<?php while (have_posts()) : the_post(); ?>

    <div class="single-product-wrapper bg-white py-8 md:py-12">
        
        <?php global $product; ?>
        
        <div class="single-product-main mx-auto px-4 md:px-6 lg:px-8">
            
            <!-- Breadcrumbs -->
            <?php if (function_exists('woocommerce_breadcrumb')) : ?>
                <div class="mb-8 text-sm text-taupe-600">
                    <?php woocommerce_breadcrumb(array(
                        'delimiter' => ' <span class="mx-2">/</span> ',
                        'wrap_before' => '<nav class="woocommerce-breadcrumb">',
                        'wrap_after' => '</nav>',
                    )); ?>
                </div>
            <?php endif; ?>
            
            <div class="flex flex-col md:grid md:grid-cols-2 md:gap-12 lg:gap-20 items-start">
                
                <!-- Product Images -->
                <div class="product-images w-full">
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
                <div class="product-summary-custom w-full">
                        
                        <!-- Title -->
                        <h1 class="product-title-custom text-2xl md:text-4xl lg:text-5xl font-bold text-charcoal uppercase tracking-wide mb-4">
                            <?php the_title(); ?>
                        </h1>
                        
                        <!-- Price -->
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
            <div class="related-products mt-20 md:mt-32">
                <?php woocommerce_output_related_products(); ?>
            </div>

        </div> <!-- End single-product-main -->
        
    </div>

<?php endwhile; ?>

    </main>
</div>

<?php get_footer(); ?>
