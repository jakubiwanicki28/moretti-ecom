<?php
/**
 * Zawartość strony głównej po hero (NOWOŚCI, Dla Niego/Niej, carousele, featured).
 * Używane przez index.php oraz front-page.php gdy wyświetlane są "Najnowsze wpisy".
 */
?>
<!-- 2. NOWOŚCI -->
<?php moretti_render_home_carousel_section('nowosci', 'NOWOŚCI', 'nowosci', 'pt-8 pb-16 md:py-20 overflow-hidden bg-white'); ?>

<!-- 3. PROMO MARQUEE (Screenshot 5) - HIDDEN BY USER REQUEST
<div class="bg-charcoal py-4 overflow-hidden whitespace-nowrap border-y border-white/10">
    <div class="inline-block animate-marquee uppercase text-white text-sm font-bold tracking-widest">
        <?php for($i=0; $i<10; $i++): ?>
            <span class="mx-8">* 20% ZNIŻKI PRZY ZAPISIE DO NEWSLETTERA</span>
        <?php endfor; ?>
    </div>
</div>
-->

<!-- 4. GENDER SPLIT / CATEGORIES (Screenshot 3) -->
<section style="max-width: 1260px; margin: 0 auto; padding: 0 1rem 4rem;">
<div id="home-gender-split-grid" class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-white/10 overflow-hidden" style="height: clamp(420px, 72vh, 860px);">
    <!-- Men -->
    <div class="relative group overflow-hidden flex items-center justify-center">
        <a href="https://www.morettifashion.com/kategoria-produktu/dzial-meski/" class="absolute inset-0 z-10" aria-label="Przejdź do kategorii Dla Niego"></a>
        <img src="<?php echo get_template_directory_uri(); ?>/images/men-category-v2.png" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Dla Niego">
        <div class="absolute inset-0 bg-black/30"></div>
        <div class="relative z-20 text-center pointer-events-none">
            <h2 class="text-5xl md:text-7xl font-bold text-white uppercase mb-6 tracking-tighter">DLA NIEGO</h2>
            <span class="text-xs font-bold text-white border-b-2 border-white pb-1">ZOBACZ WIĘCEJ</span>
        </div>
    </div>
    <!-- Women -->
    <div class="relative group overflow-hidden flex items-center justify-center">
        <a href="https://www.morettifashion.com/kategoria-produktu/dzial-damski/" class="absolute inset-0 z-10" aria-label="Przejdź do kategorii Dla Niej"></a>
        <img src="<?php echo get_template_directory_uri(); ?>/images/women-category-v2.png" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Dla Niej">
        <div class="absolute inset-0 bg-black/30"></div>
        <div class="relative z-20 text-center pointer-events-none">
            <h2 class="text-5xl md:text-7xl font-bold text-white uppercase mb-6 tracking-tighter">DLA NIEJ</h2>
            <span class="text-xs font-bold text-white border-b-2 border-white pb-1">ZOBACZ WIĘCEJ</span>
        </div>
    </div>
</div>
</section>

<!-- 5. BESTSELLERY -->
<?php moretti_render_home_carousel_section('klasyki', 'BESTSELLERY', 'bestsellery', 'py-20 overflow-hidden bg-white'); ?>

<!-- 6. VIDEO DIVIDER -->
<section id="home-video-break-banner" aria-label="Prezentacja kolekcji">
    <video class="home-video-break-video" autoplay muted loop playsinline preload="metadata">
        <source src="<?php echo esc_url(get_template_directory_uri() . '/images/LOOP.mp4'); ?>" type="video/mp4">
        <source src="<?php echo esc_url(get_template_directory_uri() . '/images/LOOP.mov'); ?>" type="video/quicktime">
        Twoja przeglądarka nie obsługuje odtwarzania wideo.
    </video>
    <div class="home-video-break-tint" aria-hidden="true"></div>
    <div class="home-video-break-edge-fade" aria-hidden="true"></div>
    <div class="home-video-break-content">
        <p class="home-video-break-kicker">MORETTI COLLECTION</p>
        <h2>ZOBACZ NASZ ASORTYMENT</h2>
        <p class="home-video-break-subtitle">Klasyczne modele i nowe kolekcje <br class="home-video-break-br-mobile" aria-hidden="true">w jednym miejscu.</p>
        <a class="home-video-break-cta" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">PRZEJDŹ DO SKLEPU</a>
    </div>
</section>

<!-- 6. OKAZJE -->
<?php moretti_render_home_carousel_section('okazje', 'OKAZJE', 'okazje', 'py-20 overflow-hidden bg-gray-100'); ?>

<!-- 7. VIDEO BREAK PLACEHOLDER BANNER - HIDDEN BY USER REQUEST
<section id="home-video-break-banner" aria-label="Miejsce na nagrania promocyjne">
    <div class="home-video-break-overlay" aria-hidden="true"></div>
    <div class="home-video-break-inner">
        <div class="home-video-break-copy">
            <p class="home-video-break-kicker">Przestrzeń na content video</p>
            <h2>Miejsce na Twoje nagrania</h2>
            <p>Wstaw tutaj finalne ujęcia i animacje, aby dodać lekki przerywnik między sekcjami.</p>
        </div>
        <div class="home-video-break-placeholders" aria-label="Placeholdery pod nagrania">
            <div class="home-video-break-card"><span>Placeholder 01</span></div>
            <div class="home-video-break-card"><span>Placeholder 02</span></div>
            <div class="home-video-break-card"><span>Placeholder 03</span></div>
        </div>
    </div>
</section>
-->

<!-- 8. FEATURED DETAIL -->
<section id="home-featured-product" style="max-width: 1260px; margin: 0 auto; padding: 5rem 1rem; border-top: 1px solid #f3f4f6;">
    <?php
    $featured_taxonomy = 'pa_strona-glowna';
    $featured_term_slug = 'tak';

    if (!taxonomy_exists($featured_taxonomy)) {
        $featured_taxonomy = '';
    }

    $featured_args = array(
        'post_type'      => 'product',
        'posts_per_page' => 1,
        'orderby'        => 'rand',
        'post_status'    => 'publish',
    );

    if ($featured_taxonomy !== '' && $featured_term_slug !== '') {
        $featured_args['tax_query'] = array(
            array(
                'taxonomy' => $featured_taxonomy,
                'field'    => 'slug',
                'terms'    => $featured_term_slug,
            ),
        );
    }

    $featured_loop = new WP_Query($featured_args);

    if (!$featured_loop->have_posts()) {
        $fallback_args = array(
            'post_type'      => 'product',
            'posts_per_page' => 1,
            'orderby'        => 'rand',
            'post_status'    => 'publish',
        );

        $featured_loop = new WP_Query($fallback_args);
    }

    if ($featured_loop->have_posts()) : $featured_loop->the_post();
        global $product;
        $product_id = get_the_ID();
        $gallery_ids = $product->get_gallery_image_ids();
        $main_image_id = $product->get_image_id();
        $featured_placeholder_src = wc_placeholder_img_src();
        if (empty($featured_placeholder_src)) {
            $featured_placeholder_src = 'data:image/svg+xml;utf8,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 800"><rect width="600" height="800" fill="#f7f5f2"/><rect x="170" y="250" width="260" height="220" fill="none" stroke="#d6d1ca" stroke-width="8"/><circle cx="270" cy="320" r="28" fill="none" stroke="#d6d1ca" stroke-width="8"/><path d="M190 430l85-92 65 66 40-40 40 66" fill="none" stroke="#d6d1ca" stroke-width="8"/></svg>');
        }
        
        // Gallery order first (no preview/featured image as first); then main if not in gallery.
        $all_images = array();
        if (!empty($gallery_ids)) {
            $all_images = array_map('absint', $gallery_ids);
            if ($main_image_id && !in_array((int) $main_image_id, $all_images)) {
                $all_images[] = (int) $main_image_id;
            }
        } else {
            if ($main_image_id) {
                $all_images[] = (int) $main_image_id;
            }
        }
        $all_images = array_values(array_unique(array_filter($all_images)));
        $slider_images = array();
        foreach ($all_images as $image_id) {
            if (wp_get_attachment_image_url($image_id, 'large')) {
                $slider_images[] = $image_id;
            }
        }
        // Limit to 3 images for the slider
        $slider_images = array_slice($slider_images, 0, 3);
    ?>
    <div id="home-featured-grid" class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-start">
        <!-- Image Slider Column -->
        <div id="home-featured-media-col" style="display: flex; justify-content: center; align-items: flex-start; padding: 3rem 0;">
            <div id="featured-slider" style="position: relative; width: 100%; overflow: visible;">
                <div style="height: 100%; position: relative;">
                    <?php if (!empty($slider_images)) : ?>
                        <?php foreach ($slider_images as $index => $image_id) :
                            $image_url = wp_get_attachment_image_url($image_id, 'large');
                        ?>
                            <div class="slider-image" data-index="<?php echo $index; ?>" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: <?php echo $index === 0 ? '1' : '0'; ?>; transition: opacity 0.7s ease; overflow: hidden;">
                                <img src="<?php echo esc_url($image_url); ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?php the_title(); ?>">
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="slider-image" data-index="0" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 1; transition: opacity 0.7s ease; overflow: hidden;">
                            <img src="<?php echo esc_url($featured_placeholder_src); ?>" style="width: 100%; height: 100%; object-fit: contain; background: #f7f5f2;" alt="<?php echo esc_attr(get_the_title()); ?>">
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (count($slider_images) > 1) : ?>
                <!-- Slider Arrows -->
                <!-- Strzałki: lewy przycisk = w lewo (←), prawy = w prawo (→). Ścieżki zamienione względem typowego rysunku. -->
                <button id="home-featured-prev-btn" class="home-featured-image-nav" onclick="featuredSliderPrev()" aria-label="Poprzednie zdjęcie">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
                <button id="home-featured-next-btn" class="home-featured-image-nav" onclick="featuredSliderNext()" aria-label="Następne zdjęcie">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>

                <!-- Slider Dots -->
                <div id="home-featured-dots" style="position: absolute; bottom: -2.5rem; left: 50%; transform: translateX(-50%); display: flex; gap: 0.5rem; z-index: 20;">
                    <?php foreach ($slider_images as $index => $image_id) : ?>
                        <button onclick="featuredSliderGoTo(<?php echo $index; ?>)" class="slider-dot" data-index="<?php echo $index; ?>" style="width: <?php echo $index === 0 ? '24px' : '8px'; ?>; height: 8px; border-radius: 4px; background: <?php echo $index === 0 ? '#2a2826' : 'rgba(42,40,38,0.2)'; ?>; border: none; cursor: pointer; transition: all 0.3s; padding: 0;"></button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Content Column -->
        <div id="home-featured-content-col" style="padding: 3rem 0;">
            <h2 style="font-size: 2.25rem; font-weight: 700; text-transform: uppercase; color: #2a2826; margin-bottom: 1.5rem; line-height: 1.1;"><?php the_title(); ?></h2>
            <p style="color: #766a5d; margin-bottom: 2rem; line-height: 1.7;">
                <?php echo wp_trim_words(get_the_excerpt(), 25); ?>
            </p>
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
                <span style="font-size: 1.5rem; font-weight: 700; color: #2a2826;"><?php echo $product->get_price_html(); ?></span>
                <div style="display: flex; color: #fbbf24; font-size: 1rem;">
                    <?php for($i=0; $i<5; $i++) echo "★"; ?>
                    <span style="color: #a39588; font-size: 0.75rem; margin-left: 0.5rem;">(<?php echo $product->get_review_count(); ?> opinie)</span>
                </div>
            </div>
            <div id="home-featured-cta-row" style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a id="home-featured-details-btn" href="<?php the_permalink(); ?>" style="display: inline-block; background: #2a2826; color: #fff; padding: 1rem 3rem; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; text-decoration: none; transition: background 0.3s;">ZOBACZ SZCZEGÓŁY</a>
                <button id="home-featured-cart-btn" class="add_to_cart_button ajax_add_to_cart" onclick="morettiQuickAddToCart(<?php echo $product_id; ?>, this)" data-product-id="<?php echo $product_id; ?>" data-product-type="<?php echo esc_attr($product->get_type()); ?>" data-product-url="<?php echo esc_url($product->get_permalink()); ?>" style="display: inline-block; background: transparent; color: #2a2826; padding: 1rem 2rem; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; border: 1px solid #2a2826; cursor: pointer; transition: all 0.3s;">DO KOSZYKA</button>
            </div>
        </div>
    </div>
    <?php endif; wp_reset_postdata(); ?>
</section>

<style>
/* ===== Homepage product sections (no carousel) ===== */
.home-products-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 18px 14px;
}

.home-products-item .product {
    width: 100%;
    margin: 0 !important;
}

.home-products-item ul.products {
    display: block !important;
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
}

.home-products-item ul.products li.product {
    float: none !important;
    width: 100% !important;
    max-width: none !important;
    margin: 0 !important;
    padding: 0 !important;
    clear: none !important;
    list-style: none !important;
}

.home-products-item .product-card {
    display: flex !important;
    flex-direction: column !important;
    align-items: flex-start !important;
    height: auto !important;
    gap: 4px;
    background: transparent !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    overflow: visible !important;
}

.home-products-item .product-image-wrapper {
    position: relative;
    width: 100%;
}

.home-products-item .product-image {
    position: relative;
    width: 100%;
    aspect-ratio: 3 / 4 !important;
    overflow: hidden;
    background: #f7f5f2 !important;
}

.home-products-item .product-image-slide {
    position: absolute;
    inset: 0;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.home-products-item .product-image-slide.active {
    opacity: 1;
    pointer-events: auto;
}

.home-products-item .product-image img {
    display: block;
    width: 100% !important;
    height: 100% !important;
    object-fit: contain !important;
    object-position: center center !important;
    background: #f7f5f2 !important;
}

.home-products-item .product-image-slide > a {
    display: block;
    width: 100%;
    height: 100%;
}

.home-products-item .image-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.95);
    cursor: pointer;
    opacity: 0;
    transition: all 0.3s;
    z-index: 2;
}

.home-products-item .image-prev {
    left: 8px;
}

.home-products-item .image-next {
    right: 8px;
}

.home-products-item .product-card:hover .image-nav {
    opacity: 1;
}

.home-products-item .image-dots {
    position: absolute;
    bottom: 12px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 6px;
    z-index: 2;
    opacity: 0;
    transition: opacity 0.3s;
}

.home-products-item .product-card:hover .image-dots {
    opacity: 1;
}

.home-products-item .image-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.5);
    cursor: pointer;
    transition: all 0.2s;
}

.home-products-item .image-dot.active {
    background: #ffffff;
    width: 20px;
    border-radius: 3px;
}

/* Strona główna: strefa hover obejmuje link + strzałki + kropki, żeby najechanie na strzałkę nie gasiło wnętrza */
.home-products-item .product-image-interior-hover-zone {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 82%;
    z-index: 2;
    pointer-events: auto;
    display: block;
}
.home-products-item .product-image-interior-link {
    position: absolute;
    inset: 0;
    display: block;
    z-index: 0;
    text-decoration: none;
    color: transparent;
}
.home-products-item .product-image-interior-hover-zone .image-nav,
.home-products-item .product-image-interior-hover-zone .image-dots {
    z-index: 1;
}

@media (hover: hover) {
    .home-products-item .product-card.has-hover-second-image .image-nav,
    .home-products-item .product-card.has-hover-second-image .image-dots {
        display: none !important;
    }
    .home-products-item .product-image .product-image-interior-hover-zone:hover .image-nav,
    .home-products-item .product-image .product-image-interior-hover-zone:hover .image-dots {
        display: flex !important;
    }
}
/* Jedna karuzela: widoczność tylko z .active; hover = przejście na slajd 1 w JS */
.home-products-item .product-card.has-hover-second-image .product-image-slide {
    opacity: 0;
}
.home-products-item .product-card.has-hover-second-image .product-image-slide.active {
    opacity: 1;
}
.home-products-item .product-card.has-hover-second-image.is-showing-variant-preview .product-image-slide {
    opacity: 0 !important;
}
.home-products-item .product-card.has-hover-second-image.is-showing-variant-preview .product-image-slide[data-index="0"] {
    opacity: 1 !important;
}

.home-products-item .product-heart {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 40px;
    height: 40px;
    border: none;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.95);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.7;
    transition: all 0.3s;
    z-index: 3;
}

.home-products-item .product-card:hover .product-heart {
    opacity: 1;
}

.home-products-item .product-info {
    padding: 10px 8px 12px !important;
    background: transparent !important;
    text-align: left !important;
    align-items: flex-start !important;
    gap: 0px !important;
}

.home-products-item .product-name {
    margin: 0 0 10px !important;
    min-height: auto;
    font-size: 12px !important;
    line-height: 1.1;
    font-weight: 700 !important;
    text-transform: none;
}

.home-products-item .product-price {
    margin: 0 !important;
    font-size: 20px !important;
    line-height: 1.0;
    font-weight: 600;
}

.home-products-item .product-price del {
    font-size: 12px;
    color: #8f8275;
    margin-right: 6px;
    font-weight: 400;
}

.home-products-item .product-price ins {
    text-decoration: none;
}

#home-video-break-banner {
    position: relative;
    width: 100%;
    height: clamp(190px, 42vw, 660px);
    margin: 0;
    overflow: hidden;
    background: #111111;
    /* Jedna komórka: wideo + tint na pełny rozmiar, copy wyśrodkowane w pionie i poziomie */
    display: grid;
    grid-template-columns: 1fr;
    grid-template-rows: 1fr;
    place-items: center;
    --home-video-fade-y: clamp(11px, 1.8vw, 26px);
    --home-video-fade-x: 0px;
}

/* Miękkie przejście: mobile tylko góra/dół; desktop + boki (białe „marginesy” obok treści) */
#home-video-break-banner .home-video-break-edge-fade {
    z-index: 2;
    align-self: stretch;
    justify-self: stretch;
    min-height: 0;
    pointer-events: none;
    background:
        linear-gradient(to bottom, #ffffff 0%, rgba(255, 255, 255, 0) 100%) top / 100% var(--home-video-fade-y) no-repeat,
        linear-gradient(to top, #f3f4f6 0%, rgba(243, 244, 246, 0) 100%) bottom / 100% var(--home-video-fade-y) no-repeat;
}

@media (min-width: 768px) {
    #home-video-break-banner {
        --home-video-fade-y: clamp(12px, 1.6vw, 28px);
        --home-video-fade-x: clamp(18px, 2.8vw, 64px);
    }

    #home-video-break-banner .home-video-break-edge-fade {
        background:
            linear-gradient(to bottom, #ffffff 0%, rgba(255, 255, 255, 0) 100%) top / 100% var(--home-video-fade-y) no-repeat,
            linear-gradient(to top, #f3f4f6 0%, rgba(243, 244, 246, 0) 100%) bottom / 100% var(--home-video-fade-y) no-repeat,
            linear-gradient(to right, #ffffff 0%, rgba(255, 255, 255, 0) 100%) left / var(--home-video-fade-x) 100% no-repeat,
            linear-gradient(to left, #ffffff 0%, rgba(255, 255, 255, 0) 100%) right / var(--home-video-fade-x) 100% no-repeat;
    }
}

#home-video-break-banner > * {
    grid-area: 1 / 1;
}

#home-video-break-banner .home-video-break-video {
    z-index: 0;
    width: 100%;
    height: 100%;
    min-height: 0;
    align-self: stretch;
    justify-self: stretch;
    object-fit: cover;
    object-position: 50% 50%;
    transform: scale(1.02);
    filter: saturate(70%) contrast(88%) brightness(92%);
}

#home-video-break-banner .home-video-break-tint {
    z-index: 1;
    align-self: stretch;
    justify-self: stretch;
    min-height: 0;
    background:
        linear-gradient(to bottom, rgba(18, 18, 18, 0.44) 0%, rgba(18, 18, 18, 0.34) 38%, rgba(18, 18, 18, 0.44) 100%),
        rgba(30, 30, 30, 0.18);
    pointer-events: none;
}

#home-video-break-banner .home-video-break-content {
    position: relative;
    z-index: 3;
    place-self: center;
    width: min(760px, 92%);
    max-width: 100%;
    box-sizing: border-box;
    margin: 0;
    text-align: center;
    color: #ffffff;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.8rem;
}

#home-video-break-banner .home-video-break-kicker {
    margin: 0;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.84);
}

#home-video-break-banner .home-video-break-content h2 {
    margin: 0;
    font-size: clamp(1.7rem, 3.4vw, 3rem);
    line-height: 1.05;
    font-weight: 700;
    letter-spacing: 0.02em;
    text-transform: uppercase;
}

#home-video-break-banner .home-video-break-subtitle {
    margin: 0;
    font-size: clamp(0.88rem, 1.25vw, 1rem);
    line-height: 1.6;
    color: rgba(255, 255, 255, 0.9);
}

/* Przełamanie przed „w …” tylko na mobile (na desktopie jedna linia) */
#home-video-break-banner .home-video-break-br-mobile {
    display: none;
}

#home-video-break-banner .home-video-break-cta {
    margin-top: 0.8rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 42px;
    padding: 0.85rem 2.1rem;
    border: 1px solid rgba(255, 255, 255, 0.82);
    color: #ffffff;
    background: rgba(0, 0, 0, 0.14);
    text-decoration: none;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.16em;
    transition: background-color 0.25s ease, color 0.25s ease, border-color 0.25s ease;
}

#home-video-break-banner .home-video-break-cta:hover,
#home-video-break-banner .home-video-break-cta:focus-visible {
    background: #ffffff;
    color: #2a2826;
    border-color: #ffffff;
}

@media (max-width: 992px) {
    .home-products-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

/* Featured product section image should respect portrait 3:4 ratio without cropping */
#featured-slider {
    width: 100%;
    max-width: 600px;
    aspect-ratio: 3 / 4;
    height: auto;
    overflow: hidden;
}

#featured-slider .slider-image img {
    width: 100% !important;
    height: 100% !important;
    object-fit: contain !important;
    background: #f7f5f2;
}

#featured-slider .home-featured-image-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.95);
    color: #2a2826;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.14);
    cursor: pointer;
    z-index: 20;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.25s ease, background-color 0.25s ease, color 0.25s ease;
}

#featured-slider #home-featured-prev-btn {
    left: 8px;
}
#featured-slider #home-featured-prev-btn svg {
    display: block;
}
#featured-slider #home-featured-next-btn {
    right: 8px;
}
#featured-slider #home-featured-next-btn svg {
    display: block;
}

#featured-slider:hover .home-featured-image-nav,
#featured-slider:focus-within .home-featured-image-nav {
    opacity: 1;
    pointer-events: auto;
}

#featured-slider .home-featured-image-nav:hover {
    background: #ffffff;
}

@media (max-width: 767px) {
    /* Keep gender tiles visibly tall on mobile (single column stack). */
    #home-gender-split-grid {
        height: auto !important;
    }

    #home-gender-split-grid > div {
        min-height: 280px;
    }

    .home-products-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px 10px;
    }

    .home-products-item .product-image {
        aspect-ratio: 3 / 4 !important;
    }

    .home-products-item .product-name {
        min-height: 30px;
    }

    .home-products-item .product-price {
        font-size: 16px !important;
    }

    .home-products-item .image-nav {
        display: none !important;
    }

    .home-products-item .image-dots {
        opacity: 1 !important;
        bottom: 8px;
    }

    #home-featured-product {
        padding: 2rem 1rem !important;
    }

    #home-featured-grid {
        gap: 1.25rem !important;
    }

    #home-featured-media-col,
    #home-featured-content-col {
        padding: 0 !important;
    }

    #featured-slider {
        max-width: 100% !important;
        height: auto !important;
        aspect-ratio: 3 / 4 !important;
        overflow: hidden !important;
    }

    #home-featured-prev-btn,
    #home-featured-next-btn {
        display: none !important;
    }

    #home-featured-dots {
        bottom: 0.5rem !important;
    }

    #home-featured-content-col h2 {
        margin-bottom: 0.85rem !important;
        font-size: 2.05rem !important;
    }

    #home-featured-content-col p {
        margin-bottom: 1.1rem !important;
    }

    #home-featured-content-col > div[style*="margin-bottom: 2rem"] {
        margin-bottom: 1rem !important;
    }

    /* Banner wideo: wyższa sekcja + mocniejszy zoom; krótsze paski gradientu niż na desktopie */
    #home-video-break-banner {
        height: clamp(260px, 58vw, 660px);
        min-height: clamp(260px, 58vw, 660px);
        --home-video-fade-y: clamp(6px, 1.1vw, 14px);
    }

    #home-video-break-banner .home-video-break-video {
        transform: scale(1.32);
        transform-origin: center center;
    }

    #home-video-break-banner .home-video-break-br-mobile {
        display: block;
    }

    #home-video-break-banner .home-video-break-content {
        width: min(640px, 92%);
        gap: 0.55rem;
        padding: 0.35rem 0.5rem;
    }

    #home-video-break-banner .home-video-break-content h2 {
        font-size: clamp(1.35rem, 5.2vw, 1.85rem);
        line-height: 1.12;
    }

    #home-video-break-banner .home-video-break-subtitle {
        max-width: 34ch;
        font-size: 0.875rem;
        line-height: 1.45;
    }

    #home-video-break-banner .home-video-break-cta {
        width: min(260px, 100%);
        margin-top: 0.45rem;
        min-height: 40px;
        padding: 0.7rem 1.5rem;
    }

    #home-featured-cta-row {
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
        gap: 0.75rem !important;
        width: 100% !important;
    }

    #home-featured-details-btn,
    #home-featured-cart-btn {
        width: 100% !important;
        text-align: center !important;
        padding: 1rem 0.6rem !important;
    }
}
</style>

<script>
// Featured Product Slider - Pure JavaScript, no classes
let featuredCurrentIndex = 0;

function featuredSliderNext() {
    const slider = document.getElementById('featured-slider');
    if (!slider) return;
    
    const images = slider.querySelectorAll('.slider-image');
    const dots = slider.querySelectorAll('.slider-dot');
    const totalImages = images.length;
    
    // Hide current
    images[featuredCurrentIndex].style.opacity = '0';
    dots[featuredCurrentIndex].style.width = '8px';
    dots[featuredCurrentIndex].style.background = 'rgba(42,40,38,0.2)';
    
    // Calculate next
    featuredCurrentIndex = (featuredCurrentIndex + 1) % totalImages;
    
    // Show next
    images[featuredCurrentIndex].style.opacity = '1';
    dots[featuredCurrentIndex].style.width = '24px';
    dots[featuredCurrentIndex].style.background = '#2a2826';
}

function featuredSliderPrev() {
    const slider = document.getElementById('featured-slider');
    if (!slider) return;
    
    const images = slider.querySelectorAll('.slider-image');
    const dots = slider.querySelectorAll('.slider-dot');
    const totalImages = images.length;
    
    // Hide current
    images[featuredCurrentIndex].style.opacity = '0';
    dots[featuredCurrentIndex].style.width = '8px';
    dots[featuredCurrentIndex].style.background = 'rgba(42,40,38,0.2)';
    
    // Calculate prev
    featuredCurrentIndex = (featuredCurrentIndex - 1 + totalImages) % totalImages;
    
    // Show prev
    images[featuredCurrentIndex].style.opacity = '1';
    dots[featuredCurrentIndex].style.width = '24px';
    dots[featuredCurrentIndex].style.background = '#2a2826';
}

function featuredSliderGoTo(index) {
    const slider = document.getElementById('featured-slider');
    if (!slider) return;
    
    const images = slider.querySelectorAll('.slider-image');
    const dots = slider.querySelectorAll('.slider-dot');
    
    // Hide current
    images[featuredCurrentIndex].style.opacity = '0';
    dots[featuredCurrentIndex].style.width = '8px';
    dots[featuredCurrentIndex].style.background = 'rgba(42,40,38,0.2)';
    
    // Set new index
    featuredCurrentIndex = index;
    
    // Show target
    images[featuredCurrentIndex].style.opacity = '1';
    dots[featuredCurrentIndex].style.width = '24px';
    dots[featuredCurrentIndex].style.background = '#2a2826';
}

// Auto-advance featured slider every 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('featured-slider');
    if (!slider) return;
    
    setInterval(function() {
        featuredSliderNext();
    }, 5000);
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const carousels = document.querySelectorAll('section .home-carousel-track');

    carousels.forEach((track) => {
        const section = track.closest('section');
        const viewport = track.parentElement;
        const prev = section ? section.querySelector('.home-carousel-prev') : null;
        const next = section ? section.querySelector('.home-carousel-next') : null;
        const items = Array.from(track.children).filter((child) => child.classList.contains('home-carousel-item'));

        if (!prev || !next || !viewport || items.length === 0) {
            return;
        }

        let index = 0;
        let itemWidth = 0;
        let step = 0;
        let maxOffset = 0;
        let dragStartX = 0;
        let dragStartY = 0;
        let dragDelta = 0;
        let verticalDelta = 0;
        let isDragging = false;
        let gap = 14;
        let wheelAccumulator = 0;
        let wheelLocked = false;
        const swipeThreshold = 50;
        const wheelThreshold = 30;

        const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

        const getItemWidth = () => {
            if (window.innerWidth >= 1800) return 320;
            if (window.innerWidth >= 1440) return 300;
            if (window.innerWidth >= 1200) return 280;
            if (window.innerWidth >= 992) return 250;
            if (window.innerWidth >= 768) return 220;
            return 170;
        };

        const getGap = () => (window.innerWidth < 768 ? 10 : 14);

        const getLastIndex = () => {
            if (step <= 0) {
                return 0;
            }

            return Math.ceil(maxOffset / step);
        };

        const syncArrows = () => {
            const disabled = maxOffset <= 0;
            prev.disabled = disabled;
            next.disabled = disabled;
            prev.style.opacity = disabled ? '0.35' : '1';
            next.style.opacity = disabled ? '0.35' : '1';
            prev.style.cursor = disabled ? 'not-allowed' : 'pointer';
            next.style.cursor = disabled ? 'not-allowed' : 'pointer';
        };

        const applyTransformFromIndex = () => {
            const lastIndex = getLastIndex();
            index = clamp(index, 0, lastIndex);

            const targetOffset = Math.min(index * step, maxOffset);
            if (step > 0) {
                index = Math.round(targetOffset / step);
            }

            track.style.transform = `translateX(-${targetOffset}px)`;
        };

        const updateCarousel = () => {
            gap = getGap();
            itemWidth = getItemWidth();
            step = itemWidth + gap;

            track.style.gap = `${gap}px`;
            items.forEach((item) => {
                item.style.width = `${itemWidth}px`;
            });

            const trackWidth = (items.length * step) - gap;
            maxOffset = Math.max(0, trackWidth - viewport.offsetWidth);

            applyTransformFromIndex();
            syncArrows();
        };

        const moveNext = () => {
            if (maxOffset <= 0) {
                return;
            }

            const currentOffset = Math.min(index * step, maxOffset);
            index = currentOffset >= maxOffset - 1 ? 0 : index + 1;
            applyTransformFromIndex();
        };

        const movePrev = () => {
            if (maxOffset <= 0) {
                return;
            }

            const currentOffset = Math.min(index * step, maxOffset);
            index = currentOffset <= 1 ? getLastIndex() : index - 1;
            applyTransformFromIndex();
        };

        prev.addEventListener('click', movePrev);
        next.addEventListener('click', moveNext);

        viewport.addEventListener('wheel', (event) => {
            const dominantDelta = Math.abs(event.deltaX) > Math.abs(event.deltaY) ? event.deltaX : event.deltaY;
            wheelAccumulator += dominantDelta;

            if (Math.abs(wheelAccumulator) < wheelThreshold || wheelLocked) {
                return;
            }

            event.preventDefault();
            wheelLocked = true;

            if (wheelAccumulator > 0) {
                moveNext();
            } else {
                movePrev();
            }

            wheelAccumulator = 0;
            window.setTimeout(() => {
                wheelLocked = false;
            }, 220);
        }, { passive: false });

        const onDragStart = (clientX, clientY = 0) => {
            isDragging = true;
            dragStartX = clientX;
            dragStartY = clientY;
            dragDelta = 0;
            verticalDelta = 0;
            viewport.classList.add('is-dragging');
        };

        const onDragMove = (clientX, clientY = 0) => {
            if (!isDragging) return;
            dragDelta = clientX - dragStartX;
            verticalDelta = clientY - dragStartY;
        };

        const onDragEnd = () => {
            if (!isDragging) return;
            isDragging = false;
            viewport.classList.remove('is-dragging');

            if (Math.abs(dragDelta) >= swipeThreshold && Math.abs(dragDelta) > Math.abs(verticalDelta)) {
                if (dragDelta < 0) {
                    moveNext();
                } else {
                    movePrev();
                }
            }
            dragDelta = 0;
        };

        track.addEventListener('touchstart', (event) => {
            if (event.touches.length !== 1) return;
            onDragStart(event.touches[0].clientX, event.touches[0].clientY);
        }, { passive: true });

        track.addEventListener('touchmove', (event) => {
            if (event.touches.length !== 1) return;
            onDragMove(event.touches[0].clientX, event.touches[0].clientY);
            if (Math.abs(dragDelta) > Math.abs(verticalDelta)) {
                event.preventDefault();
            }
        }, { passive: false });

        track.addEventListener('touchend', onDragEnd);
        track.addEventListener('touchcancel', onDragEnd);

        track.addEventListener('pointerdown', (event) => {
            if (event.pointerType === 'mouse' && event.button !== 0) return;
            onDragStart(event.clientX, event.clientY);
        });

        track.addEventListener('pointermove', (event) => {
            onDragMove(event.clientX, event.clientY);
        });

        track.addEventListener('pointerup', onDragEnd);
        track.addEventListener('pointercancel', onDragEnd);
        track.addEventListener('pointerleave', onDragEnd);

        window.addEventListener('resize', updateCarousel);
        updateCarousel();
    });

    const homeCards = document.querySelectorAll('#nowosci .product-card, #klasyki .product-card, #okazje .product-card');

    homeCards.forEach((card) => {
        if (card.dataset.homeSliderInitialized === 'true') {
            return;
        }

        const slides = card.querySelectorAll('.product-image-slide');
        const prevBtn = card.querySelector('.image-prev');
        const nextBtn = card.querySelector('.image-next');
        const dots = card.querySelectorAll('.image-dot');

        if (slides.length <= 1) {
            card.dataset.homeSliderInitialized = 'true';
            return;
        }

        let currentIndex = 0;
        let touchStartX = 0;
        let touchEndX = 0;

        const showSlide = (index) => {
            if (index < 0 || index >= slides.length) return;
            slides.forEach((slide) => slide.classList.remove('active'));
            dots.forEach((dot) => dot.classList.remove('active'));
            slides[index].classList.add('active');
            if (dots[index]) dots[index].classList.add('active');
            currentIndex = index;
        };

        const zone = card.querySelector('.product-image-interior-hover-zone');
        if (zone) {
            zone.addEventListener('mouseenter', () => {
                if (currentIndex === 0) showSlide(1);
            });
        }
        card.addEventListener('mouseleave', () => {
            showSlide(0);
        });

        if (prevBtn) {
            prevBtn.addEventListener('click', (event) => {
                event.preventDefault();
                const newIndex = currentIndex > 0 ? currentIndex - 1 : slides.length - 1;
                showSlide(newIndex);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', (event) => {
                event.preventDefault();
                const newIndex = currentIndex < slides.length - 1 ? currentIndex + 1 : 0;
                showSlide(newIndex);
            });
        }

        dots.forEach((dot) => {
            dot.addEventListener('click', (event) => {
                event.preventDefault();
                const targetIndex = parseInt(dot.dataset.index || '0', 10);
                showSlide(Number.isNaN(targetIndex) ? 0 : targetIndex);
            });
        });

        card.addEventListener('touchstart', (event) => {
            if (!event.changedTouches || event.changedTouches.length === 0) {
                return;
            }
            touchStartX = event.changedTouches[0].screenX;
            touchEndX = touchStartX;
        }, { passive: true });

        card.addEventListener('touchend', (event) => {
            if (!event.changedTouches || event.changedTouches.length === 0) {
                return;
            }
            touchEndX = event.changedTouches[0].screenX;
            const swipeThreshold = 50;
            if (touchEndX < touchStartX - swipeThreshold) {
                const newIndex = currentIndex < slides.length - 1 ? currentIndex + 1 : 0;
                showSlide(newIndex);
            }
            if (touchEndX > touchStartX + swipeThreshold) {
                const newIndex = currentIndex > 0 ? currentIndex - 1 : slides.length - 1;
                showSlide(newIndex);
            }
        }, { passive: true });

        card.dataset.homeSliderInitialized = 'true';
    });
});
</script>

<!-- 9. ICONS OF THE SEASON (Screenshot 8) - HIDDEN BY USER REQUEST
<section class="relative h-[70vh] flex items-center justify-center text-center">
<div class="absolute inset-0 z-0">
    <img src="<?php echo get_template_directory_uri(); ?>/images/photo2.jpg" class="w-full h-full object-cover" alt="Icons">
    <div class="absolute inset-0 bg-black/40"></div>
</div>
<div class="relative z-10 text-white px-4">
    <h2 class="text-5xl md:text-7xl font-bold uppercase mb-8 tracking-tighter">NASZA PASJA</h2>
    <a href="/o-nas" class="inline-block bg-white text-charcoal px-12 py-4 text-xs font-bold uppercase tracking-widest hover:bg-gray-200 transition-all">NASZA HISTORIA</a>
</div>
</section>
-->

<!-- 10. DESTINATION FOR EXQUISITE FASHION (Screenshot 7) - HIDDEN BY USER REQUEST
<section class="container mx-auto px-4 py-20">
<div class="grid md:grid-cols-2 gap-16 items-center">
    <div>
        <h2 class="text-4xl font-bold text-charcoal uppercase mb-8 leading-tight">MIEJSCE<br>DOSKONAŁEGO<br>RZEMIOSŁA</h2>
        <p class="text-taupe-700 leading-relaxed mb-8">
            Wkrocz do świata, gdzie tradycyjne techniki spotykają się z nowoczesną elegancją. W Moretti tworzymy kolekcje, które celebrują ponadczasowy design i nienaganne wykonanie.
        </p>
        <div class="flex flex-wrap gap-8 opacity-50 grayscale">
            <span class="font-bold text-2xl">MORETTI</span>
            <span class="font-bold text-2xl">CROWN</span>
            <span class="font-bold text-2xl">PREMIUM</span>
        </div>
    </div>
    <div>
        <img src="<?php echo get_template_directory_uri(); ?>/images/women-category-v2.png" class="w-full h-auto grayscale" alt="Fashion">
    </div>
</div>
</section>
-->

<!-- 11. USP & FAQ (Screenshot 9) -->
<section class="bg-gray-50 py-20">
<div class="container mx-auto px-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-20 text-center">
        <div class="flex flex-col items-center">
            <svg class="w-8 h-8 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
            <h3 class="font-bold uppercase text-sm mb-2">Darmowa Dostawa od 250 zł</h3>
            <p class="text-xs text-taupe-600">Dla wszystkich zamówień powyżej 250 zł</p>
        </div>
        <div class="flex flex-col items-center border-x border-gray-200">
            <svg class="w-8 h-8 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            <h3 class="font-bold uppercase text-sm mb-2">Bezpieczne Płatności</h3>
            <p class="text-xs text-taupe-600">Twoje dane są u nas w pełni bezpieczne</p>
        </div>
        <div class="flex flex-col items-center">
            <svg class="w-8 h-8 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path></svg>
            <h3 class="font-bold uppercase text-sm mb-2">Gwarancja Zwrotu</h3>
            <p class="text-xs text-taupe-600">14 dni na zwrot towaru</p>
        </div>
    </div>

    <div class="max-w-2xl mx-auto">
        <h2 class="text-4xl font-bold text-center uppercase mb-12">FAQ</h2>
        <div class="space-y-4">
            <?php 
            $faqs = array(
                "Jakie są koszty dostawy?" => "Dostawa kurierem InPost kosztuje 15 zł, powyżej 250 zł jest darmowa.",
                "Z jakiej skóry wykonane są produkty?" => "Używamy wyłącznie naturalnej skóry bydlęcej.",
                "Jak dbać o skórzany portfel?" => "Zalecamy stosowanie dedykowanych balsamów do skór raz na kwartał."
            );
            foreach($faqs as $q => $a): ?>
            <details class="group border-b border-gray-200 pb-4">
                <summary class="flex justify-between items-center cursor-pointer list-none font-bold uppercase text-xs tracking-widest">
                    <?php echo $q; ?>
                    <span class="text-2xl group-open:rotate-45 transition-transform">+</span>
                </summary>
                <p class="mt-4 text-sm text-taupe-700 leading-relaxed"><?php echo $a; ?></p>
            </details>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</section>

<!-- 12. LATEST NEWS (Screenshot 10) - HIDDEN FOR NOW
<section class="container mx-auto px-4 py-20">
<div class="flex justify-between items-end mb-12 pb-4 border-b border-charcoal">
    <h2 class="text-4xl md:text-6xl font-bold text-charcoal uppercase tracking-tighter">Z BLOGA</h2>
    <a href="/blog" class="text-xs font-bold border-b border-charcoal pb-1 uppercase tracking-widest">Wszystkie wpisy</a>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <?php for($i=1; $i<=3; $i++): ?>
    <article class="group cursor-pointer">
        <div class="aspect-square bg-gray-100 mb-6 overflow-hidden">
            <img src="<?php echo get_template_directory_uri(); ?>/images/hero.png" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
        </div>
        <span class="text-[10px] uppercase tracking-widest text-taupe-500 mb-2 block">3 Aug</span>
        <h3 class="text-xl font-bold uppercase mb-4 leading-tight group-hover:text-taupe-600 transition-colors">Jak dbać o naturalną skórę? Poradnik.</h3>
        <a href="#" class="text-[10px] font-bold uppercase tracking-widest border-b border-charcoal pb-1">Czytaj więcej</a>
    </article>
    <?php endfor; ?>
</div>
</section>
-->
