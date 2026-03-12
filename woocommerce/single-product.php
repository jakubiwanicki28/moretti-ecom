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
    .product-reviews-summary {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: -8px 0 18px;
    }
    .product-reviews-summary .star-rating {
        margin: 0 !important;
    }
    .product-reviews-link {
        font-size: 14px;
        color: #6b7280;
        text-decoration: none;
    }
    .product-reviews-link:hover {
        color: #2a2826;
    }
    .product-lowest-price-note {
        margin-top: -6px;
        margin-bottom: 20px;
        font-size: 14px;
        color: #766a5d;
        line-height: 1.45;
    }
    .product-actions-row {
        display: flex;
        align-items: stretch;
        gap: 14px;
        margin-bottom: 2rem;
    }
    .product-actions-row .product-cart-form-custom {
        flex: 1;
        margin-bottom: 0 !important;
    }
    .product-cart-form-custom form.cart {
        display: flex !important;
        align-items: stretch !important;
        gap: 12px !important;
        margin: 0 !important;
    }
    .product-cart-form-custom form.cart .quantity {
        display: none !important;
    }
    .product-cart-form-custom form.cart .single_add_to_cart_button {
        flex: 1 !important;
        margin: 0 !important;
    }
    .wishlist-single-btn {
        width: 64px;
        min-width: 64px;
        border: 1px solid #2a2826;
        background: #fff;
        color: #2a2826;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .wishlist-single-btn:hover,
    .wishlist-single-btn.is-active {
        background: #2a2826;
        color: #fff;
    }
    .product-mvp-status {
        margin-bottom: 1.75rem;
    }
    .product-mvp-status-list {
        display: grid;
        gap: 0.8rem;
    }
    .product-mvp-status-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        color: #2a2826;
        line-height: 1.4;
    }
    .product-mvp-status-item svg {
        width: 18px;
        height: 18px;
        color: #6b7280;
        flex: 0 0 auto;
    }
    .product-mvp-status-item .product-care-link {
        color: #2a2826;
        font-weight: 600;
        text-decoration: underline;
        text-underline-offset: 2px;
        text-decoration-thickness: 1px;
        transition: color 0.2s ease;
    }
    .product-mvp-status-item .product-care-link:hover {
        color: #000000;
    }
    .product-mvp-status-item.is-available {
        color: #15803d;
        font-weight: 500;
    }
    .product-mvp-status-item.is-available svg {
        color: #15803d;
    }
    .product-reviews-custom {
        border-top: 1px solid #e5e7eb;
        padding-top: 2rem;
    }
    .product-reviews-custom h2 {
        font-size: 1.5rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #2a2826;
        margin-bottom: 1.25rem;
    }
    .product-reviews-custom #reviews .commentlist {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .product-reviews-custom #reviews .commentlist li {
        border-bottom: 1px solid #f1f5f9;
        padding: 1rem 0;
    }
    .product-reviews-custom #review_form_wrapper {
        margin-top: 1.5rem;
    }
    .product-reviews-custom #respond .comment-form {
        display: grid;
        gap: 14px;
    }
    .product-reviews-custom #respond label {
        display: block;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.14em;
        font-weight: 700;
        margin-bottom: 6px;
        color: #2a2826;
    }
    .product-reviews-custom #respond input[type="text"],
    .product-reviews-custom #respond input[type="email"],
    .product-reviews-custom #respond select,
    .product-reviews-custom #respond textarea {
        width: 100%;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #2a2826;
        padding: 12px 14px;
        font-size: 14px;
    }
    .product-reviews-custom #respond textarea {
        min-height: 140px;
        resize: vertical;
    }
    .product-reviews-custom #respond .form-submit input[type="submit"] {
        height: 48px;
        padding: 0 24px;
        border: 1px solid #2a2826;
        background: #2a2826;
        color: #fff;
        text-transform: uppercase;
        letter-spacing: 0.14em;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
    }
    .product-reviews-custom #respond .comment-notes,
    .product-reviews-custom #respond .logged-in-as {
        color: #6b7280;
        font-size: 13px;
        line-height: 1.4;
    }
    .single-color-variants-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .single-color-variants {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .single-color-dot {
        width: 16px;
        height: 16px;
        border-radius: 999px;
        border: 1px solid rgba(17, 17, 17, 0.3);
        box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.9);
        display: inline-block;
        text-decoration: none;
        transition: transform 0.15s ease;
    }
    .single-color-dot:hover {
        transform: scale(1.08);
    }
    .single-color-dot:focus-visible {
        outline: 2px solid #111111;
        outline-offset: 2px;
    }
    .single-color-dot.is-current {
        width: 18px;
        height: 18px;
        border-width: 2px;
        border-color: #111111;
        box-shadow: 0 0 0 2px #ffffff;
    }
    @media (min-width: 768px) {
        .product-summary-custom {
            margin-left: auto !important;
        }
        .single-product-main .product-images {
            max-width: 70% !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }
        .single-product-main .single-product-row {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
            gap: clamp(24px, 4vw, 64px) !important;
            align-items: flex-start !important;
        }
    }
    .single-product-main .product-images,
    .single-product-main .product-summary-custom {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
    }
    .woocommerce-product-gallery {
        width: 100% !important;
        max-width: 100% !important;
    }
    .woocommerce-product-gallery .main-product-image {
        margin: 0 0 24px !important;
    }
    .woocommerce-product-gallery .main-product-image-frame {
        position: relative !important;
        width: 100% !important;
        background: transparent !important;
        overflow: hidden !important;
    }
    .woocommerce-product-gallery .main-product-image-frame::before {
        content: "" !important;
        display: block !important;
        padding-top: 75% !important; /* STAŁE pole 4:3 (szerokość:wysokość) */
    }
    .woocommerce-product-gallery .main-product-image-el {
        position: absolute !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        height: 100% !important;
        width: auto !important;
        max-width: 100% !important;
        object-fit: contain !important;
    }
    .single-gallery-arrow {
        position: absolute !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        width: 32px !important;
        height: 32px !important;
        border: none !important;
        border-radius: 50% !important;
        background: rgba(255, 255, 255, 0.92) !important;
        color: #2a2826 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        z-index: 12 !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.14) !important;
        opacity: 1 !important;
        transition: background-color 0.2s ease !important;
    }
    .single-gallery-prev {
        left: 10px !important;
    }
    .single-gallery-next {
        right: 10px !important;
    }
    .single-gallery-arrow:hover {
        background: #ffffff !important;
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
        .single-product-main .single-product-row {
            display: flex !important;
            flex-direction: column !important;
            gap: 1rem !important;
        }
        .main-product-image-el {
            object-fit: contain !important;
        }
        .product-summary-custom {
            margin-top: 1rem !important;
        }
        .product-reviews-summary {
            margin: -4px 0 14px;
        }
        .product-reviews-link {
            font-size: 13px;
        }
        .product-lowest-price-note {
            font-size: 13px;
            margin-bottom: 16px;
        }
        .product-mvp-status-item {
            font-size: 13px;
            gap: 8px;
        }
        .product-mvp-status-item .product-care-link {
            font-size: 13px;
        }
        .product-actions-row {
            gap: 10px;
            margin-bottom: 1.5rem;
        }
        .wishlist-single-btn {
            width: 58px;
            min-width: 58px;
        }
        .single-color-dot {
            width: 15px;
            height: 15px;
        }
        .single-color-dot.is-current {
            width: 17px;
            height: 17px;
        }
        .product-reviews-custom h2 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
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
            margin: 0 !important;
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
        
        <?php
        global $product;
        $single_color_variants = function_exists('moretti_get_product_color_variants')
            ? moretti_get_product_color_variants($product)
            : array();
        ?>
        
        <div class="single-product-main mx-auto px-3 md:px-4 lg:px-5">
            
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
            
            <div class="single-product-row flex flex-col md:grid md:grid-cols-2 items-start">
                
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

                        <?php
                        $average_rating = (float) $product->get_average_rating();
                        $review_count = (int) $product->get_review_count();
                        ?>
                        <div class="product-reviews-summary">
                            <?php if (wc_review_ratings_enabled()) : ?>
                                <?php echo wc_get_rating_html($average_rating, $review_count); ?>
                            <?php endif; ?>
                            <a href="#moretti-reviews" class="product-reviews-link">
                                <?php echo $review_count > 0 ? sprintf('(Zobacz opinie %d)', $review_count) : '(Brak opinii)'; ?>
                            </a>
                        </div>

                        <?php
                        $current_price_value = (float) $product->get_price();
                        if ($current_price_value > 0) :
                            $lowest_price_mvp = wc_get_price_to_display($product, array('price' => $current_price_value));
                        ?>
                            <div class="product-lowest-price-note">
                                Najniższa cena z 30 dni przed obniżką: <?php echo wp_kses_post(wc_price($lowest_price_mvp)); ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Short Description -->
                        <?php if ($product->get_short_description()) : ?>
                            <div class="product-description-custom text-sm md:text-base text-taupe-700 leading-relaxed mb-8">
                                <?php echo wpautop($product->get_short_description()); ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Add to Cart + Wishlist -->
                        <div class="product-actions-row">
                            <div class="product-cart-form-custom">
                                <?php woocommerce_template_single_add_to_cart(); ?>
                            </div>
                            <button
                                type="button"
                                class="wishlist-toggle wishlist-single-btn"
                                data-product-id="<?php echo esc_attr($product->get_id()); ?>"
                                aria-label="Dodaj do ulubionych"
                                aria-pressed="false"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </button>
                        </div>

                        <?php
                        $base_ts = current_time('timestamp');
                        $ship_date = wp_date('d.m', strtotime('+1 day', $base_ts));
                        $delivery_date = wp_date('d.m', strtotime('+3 days', $base_ts));
                        $care_page = get_page_by_path('pielegnacja-portfela', OBJECT, 'page');
                        $care_page_url = ($care_page instanceof WP_Post && $care_page->post_status === 'publish')
                            ? get_permalink($care_page->ID)
                            : home_url('/pielegnacja-portfela/');
                        ?>
                        <div class="product-mvp-status">
                            <div class="product-mvp-status-list">
                                <div class="product-mvp-status-item is-available">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Produkt dostępny
                                </div>
                                <div class="product-mvp-status-item">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Kup teraz, wysyłka <?php echo esc_html($ship_date); ?>, u Ciebie <?php echo esc_html($delivery_date); ?>
                                </div>
                                <div class="product-mvp-status-item">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h15l3 4v6a2 2 0 01-2 2h-1a2 2 0 01-4 0H9a2 2 0 01-4 0H4a1 1 0 01-1-1V7zm16 4h-4V9h2.5L19 11z"></path></svg>
                                    Darmowa dostawa od 250 zł
                                </div>
                                <div class="product-mvp-status-item">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-4-8h8m-9 10h10a2 2 0 002-2V8l-4-4H7a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <a class="product-care-link" href="<?php echo esc_url($care_page_url); ?>">Pielęgnacja portfela ze skóry naturalnej</a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Meta (SKU, Kategorie) -->
                        <div class="product-meta-custom border-t border-gray-200 pt-6 space-y-3">
                            <?php if (wc_product_sku_enabled() && ($product->get_sku() || $product->is_type('variable'))) : ?>
                                <div class="text-[10px] text-taupe-600 flex items-center gap-2">
                                    <span class="font-bold text-charcoal uppercase tracking-[0.2em]">SKU:</span>
                                    <span class="sku"><?php echo $product->get_sku() ? $product->get_sku() : 'Brak'; ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($single_color_variants)) : ?>
                                <div class="text-[10px] text-taupe-600 single-color-variants-row">
                                    <span class="font-bold text-charcoal uppercase tracking-[0.2em]">KOLORY:</span>
                                    <div class="single-color-variants" aria-label="Dostępne warianty kolorystyczne">
                                        <?php foreach ($single_color_variants as $variant) : ?>
                                            <a
                                                class="single-color-dot <?php echo !empty($variant['is_current']) ? 'is-current' : ''; ?>"
                                                href="<?php echo esc_url($variant['url']); ?>"
                                                style="background-color: <?php echo esc_attr($variant['color_hex']); ?>;"
                                                aria-label="<?php echo esc_attr($variant['color_label']); ?>"
                                                title="<?php echo esc_attr($variant['color_label']); ?>"
                                            >
                                                <span class="screen-reader-text"><?php echo esc_html($variant['color_label']); ?></span>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="text-[10px] text-taupe-600 leading-relaxed">
                                <span class="font-bold text-charcoal uppercase tracking-[0.2em] mr-1">KATEGORIA:</span>
                                <span class="product-categories-list">
                                    <?php echo wc_get_product_category_list($product->get_id(), ', '); ?>
                                </span>
                            </div>
                        </div>

                        <style>
                            .product-categories-list a {
                                color: inherit;
                                transition: color 0.2s;
                            }
                            .product-categories-list a:hover {
                                color: #000;
                            }
                        </style>
                        
                        <!-- Additional Info Accordion -->
                        <div class="product-accordion mt-8">
                            <!-- Opis -->
                            <details class="border-t border-gray-200 py-4" open>
                                <summary class="cursor-pointer text-charcoal font-medium flex items-center justify-between text-xs uppercase tracking-[0.2em] list-none marker:content-none [&::-webkit-details-marker]:hidden">
                                    <span>OPIS</span>
                                    <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </summary>
                                <div class="mt-4 text-taupe-700 text-sm leading-relaxed product-accordion-content">
                                    <?php the_content(); ?>
                                </div>
                            </details>
                            
                            <!-- Dostawa i Zwroty -->
                            <details class="border-t border-gray-200 py-4">
                                <summary class="cursor-pointer text-charcoal font-medium flex items-center justify-between text-xs uppercase tracking-[0.2em] list-none marker:content-none [&::-webkit-details-marker]:hidden">
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

                            <!-- Opinie -->
                            <?php if (comments_open() || get_comments_number()) : ?>
                                <?php comments_template(); ?>
                            <?php endif; ?>
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
