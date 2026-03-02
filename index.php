<?php
/**
 * Main template file - Homepage Redesign (STYNRA Style)
 * 
 * @package Moretti
 */

if (!function_exists('moretti_render_home_carousel_section')) {
    /**
     * Render homepage product carousel section by WooCommerce category slug.
     */
    function moretti_render_home_carousel_section($section_id, $title, $category_slug, $section_classes = 'py-20 overflow-hidden bg-white') {
        $category_term = get_term_by('slug', $category_slug, 'product_cat');
        $category_url = get_permalink(wc_get_page_id('shop'));
        if ($category_term && !is_wp_error($category_term)) {
            $category_link = get_term_link($category_term);
            if (!is_wp_error($category_link)) {
                $category_url = $category_link;
            }
        }

        $query_args = array(
            'post_type'      => 'product',
            'posts_per_page' => 5,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => $category_slug,
                ),
            ),
        );

        $loop = new WP_Query($query_args);
        ?>
        <section id="<?php echo esc_attr($section_id); ?>" class="<?php echo esc_attr($section_classes); ?>">
            <div style="max-width: 1260px; margin: 0 auto; padding: 0 1rem;">
                <div class="flex justify-between items-end mb-6">
                    <h2 class="text-4xl md:text-6xl font-bold text-charcoal uppercase tracking-tighter"><?php echo esc_html($title); ?></h2>
                    <a href="<?php echo esc_url($category_url); ?>" class="inline-flex items-center gap-2 text-[10px] md:text-xs font-bold uppercase tracking-[0.15em] border border-charcoal text-charcoal px-4 py-2 hover:bg-charcoal hover:text-white transition-colors">
                        Pokaż więcej
                    </a>
                </div>
                <div class="home-products-grid">
                    <?php set_query_var('moretti_home_carousel', true); ?>
                    <?php if ($loop->have_posts()) : ?>
                        <?php while ($loop->have_posts()) : $loop->the_post(); ?>
                            <div class="home-products-item">
                                <ul class="products list-none m-0 p-0">
                                    <?php wc_get_template_part('content', 'product'); ?>
                                </ul>
                            </div>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <div class="w-full py-10 text-center text-gray-500">Brak produktów w tej sekcji.</div>
                    <?php endif; ?>
                    <?php set_query_var('moretti_home_carousel', false); ?>
                </div>
            </div>
        </section>
        <?php
        wp_reset_postdata();
    }
}

get_header(); ?>

<?php
$hero_banner_dir_path = trailingslashit(get_template_directory()) . 'images/banners/';
$hero_banner_dir_url = trailingslashit(get_template_directory_uri()) . 'images/banners/';
$hero_banners = array();

if (is_dir($hero_banner_dir_path)) {
    $hero_banner_entries = scandir($hero_banner_dir_path);

    if ($hero_banner_entries !== false) {
        foreach ($hero_banner_entries as $hero_banner_entry) {
            if ($hero_banner_entry === '.' || $hero_banner_entry === '..') {
                continue;
            }

            $hero_banner_full_path = $hero_banner_dir_path . $hero_banner_entry;
            if (!is_file($hero_banner_full_path)) {
                continue;
            }

            if (preg_match('/^([0-9]+)\.[^.]+$/i', $hero_banner_entry, $hero_banner_match) !== 1) {
                continue;
            }

            $hero_banners[] = array(
                'order' => (int) $hero_banner_match[1],
                'name'  => $hero_banner_entry,
            );
        }
    }
}

if (!empty($hero_banners)) {
    usort($hero_banners, static function ($banner_a, $banner_b) {
        if ($banner_a['order'] === $banner_b['order']) {
            return strnatcasecmp($banner_a['name'], $banner_b['name']);
        }

        return $banner_a['order'] <=> $banner_b['order'];
    });
} else {
    $hero_banners[] = array(
        'order' => 1,
        'name'  => 'Baner strona www Large.jpeg',
    );
}

$hero_banners_count = count($hero_banners);
?>
<!-- 1. HERO SECTION (Dynamic banner carousel) -->
<section id="moretti-home-hero" class="relative h-[80vh] overflow-hidden bg-gray-100">
    <div class="moretti-hero-track-wrap absolute inset-0 z-0">
        <div class="moretti-hero-track" style="display: flex; width: 100%; height: 100%; transition: transform 0.7s ease;">
            <?php foreach ($hero_banners as $hero_banner_index => $hero_banner) : ?>
                <?php
                $hero_banner_name = $hero_banner['name'];
                $hero_banner_src = $hero_banner_dir_url . rawurlencode($hero_banner_name);
                if (!file_exists($hero_banner_dir_path . $hero_banner_name)) {
                    $hero_banner_src = get_template_directory_uri() . '/images/Baner strona www Large.jpeg';
                }
                ?>
                <div class="moretti-hero-slide" style="position: relative; min-width: 100%; height: 100%;">
                    <img
                        src="<?php echo esc_url($hero_banner_src); ?>"
                        alt="<?php echo esc_attr(sprintf('Baner %d', $hero_banner_index + 1)); ?>"
                        class="w-full h-full object-cover"
                        <?php echo $hero_banner_index === 0 ? 'loading="eager" fetchpriority="high"' : 'loading="lazy"'; ?>
                    >
                    <div class="absolute inset-0 bg-black/15"></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="container mx-auto px-4 relative z-10 text-white h-full flex items-center">
        <div class="max-w-2xl">
            <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold leading-none mb-8 uppercase">
                MORETTI FASHION<br>ELEGANCJA I STYL
            </h1>
            <p class="text-sm md:text-base max-w-md mb-8 opacity-90 leading-relaxed">
                Odkryj naszą wyselekcjonowaną kolekcję portfeli premium. Wyjątkowe rzemiosło, które towarzyszy Ci każdego dnia.
            </p>
            <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="inline-block bg-white text-charcoal px-12 py-4 text-xs font-bold uppercase tracking-widest hover:bg-charcoal hover:text-white transition-all">
                KUP TERAZ
            </a>
        </div>
    </div>

    <?php if ($hero_banners_count > 1) : ?>
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex gap-3 z-20" id="moretti-hero-dots">
            <?php foreach ($hero_banners as $hero_banner_dot_index => $hero_banner_dot) : ?>
                <button
                    type="button"
                    class="moretti-hero-dot<?php echo $hero_banner_dot_index === 0 ? ' is-active' : ''; ?>"
                    data-slide-index="<?php echo esc_attr($hero_banner_dot_index); ?>"
                    aria-label="<?php echo esc_attr(sprintf('Pokaż baner %d', $hero_banner_dot_index + 1)); ?>"
                    style="width: 8px; height: 8px; border: 0; border-radius: 999px; background: <?php echo $hero_banner_dot_index === 0 ? '#ffffff' : 'rgba(255, 255, 255, 0.42)'; ?>; cursor: pointer; transition: transform 0.25s ease, background-color 0.25s ease; padding: 0;"
                ></button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var heroSection = document.getElementById('moretti-home-hero');
    if (!heroSection) {
        return;
    }

    var track = heroSection.querySelector('.moretti-hero-track');
    var dots = heroSection.querySelectorAll('.moretti-hero-dot');
    var totalSlides = <?php echo (int) $hero_banners_count; ?>;

    if (!track || totalSlides <= 1) {
        return;
    }

    var currentSlide = 0;
    var intervalId = null;

    var updateDots = function() {
        dots.forEach(function(dot, dotIndex) {
            var isActive = dotIndex === currentSlide;
            dot.classList.toggle('is-active', isActive);
            dot.style.background = isActive ? '#ffffff' : 'rgba(255, 255, 255, 0.42)';
            dot.style.transform = isActive ? 'scale(1.4)' : 'scale(1)';
        });
    };

    var goToSlide = function(targetSlide) {
        currentSlide = (targetSlide + totalSlides) % totalSlides;
        track.style.transform = 'translateX(-' + (currentSlide * 100) + '%)';
        updateDots();
    };

    var goToNextSlide = function() {
        goToSlide(currentSlide + 1);
    };

    var stopAutoplay = function() {
        if (intervalId !== null) {
            window.clearInterval(intervalId);
            intervalId = null;
        }
    };

    var startAutoplay = function() {
        stopAutoplay();
        intervalId = window.setInterval(goToNextSlide, 5000);
    };

    dots.forEach(function(dot) {
        dot.addEventListener('click', function() {
            var requestedSlide = parseInt(dot.getAttribute('data-slide-index'), 10);
            if (!Number.isNaN(requestedSlide)) {
                goToSlide(requestedSlide);
                startAutoplay();
            }
        });
    });

    heroSection.addEventListener('mouseenter', stopAutoplay);
    heroSection.addEventListener('mouseleave', startAutoplay);
    heroSection.addEventListener('focusin', stopAutoplay);
    heroSection.addEventListener('focusout', startAutoplay);

    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopAutoplay();
            return;
        }

        startAutoplay();
    });

    goToSlide(0);
    startAutoplay();
});
</script>

<!-- 2. NOWOŚCI -->
<?php moretti_render_home_carousel_section('nowosci', 'NOWOŚCI', 'nowosci', 'py-20 overflow-hidden bg-white'); ?>

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
        <img src="<?php echo get_template_directory_uri(); ?>/images/men-category-v2.png" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Dla Niego">
        <div class="absolute inset-0 bg-black/30"></div>
        <div class="relative z-10 text-center">
            <h2 class="text-5xl md:text-7xl font-bold text-white uppercase mb-6 tracking-tighter">DLA NIEGO</h2>
            <a href="/kategoria-produktu/portfele-meskie" class="text-xs font-bold text-white border-b-2 border-white pb-1 hover:opacity-70 transition-opacity">ZOBACZ WIĘCEJ</a>
        </div>
    </div>
    <!-- Women -->
    <div class="relative group overflow-hidden flex items-center justify-center">
        <img src="<?php echo get_template_directory_uri(); ?>/images/women-category-v2.png" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Dla Niej">
        <div class="absolute inset-0 bg-black/30"></div>
        <div class="relative z-10 text-center">
            <h2 class="text-5xl md:text-7xl font-bold text-white uppercase mb-6 tracking-tighter">DLA NIEJ</h2>
            <a href="/kategoria-produktu/portfele-damskie" class="text-xs font-bold text-white border-b-2 border-white pb-1 hover:opacity-70 transition-opacity">ZOBACZ WIĘCEJ</a>
        </div>
    </div>
</div>
</section>

<!-- 5. BESTSELLERY -->
<?php moretti_render_home_carousel_section('klasyki', 'BESTSELLERY', 'bestsellery', 'py-20 overflow-hidden bg-white'); ?>

<!-- 6. OKAZJE -->
<?php moretti_render_home_carousel_section('okazje', 'OKAZJE', 'okazje', 'py-20 overflow-hidden bg-gray-100'); ?>

<!-- 7. FEATURED DETAIL -->
<section id="home-featured-product" style="max-width: 1260px; margin: 0 auto; padding: 5rem 1rem; border-top: 1px solid #f3f4f6;">
    <?php
    // Get the specific featured product: Elegance Red
    $featured_product_name = 'Elegance Red - Portfel Damski';
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 1,
        'title' => $featured_product_name
    );
    $featured_loop = new WP_Query($args);
    
    // Fallback if not found by title
    if (!$featured_loop->have_posts()) {
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 1,
            'orderby' => 'rand'
        );
        $featured_loop = new WP_Query($args);
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
        
        // Combine main image + gallery and keep only images that resolve to valid URLs.
        $all_images = array_values(array_unique(array_filter(array_map('absint', array_merge(array($main_image_id), $gallery_ids)))));
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
            <div id="featured-slider" style="position: relative; width: 100%; max-width: 600px; height: 550px; overflow: visible;">
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
                <button id="home-featured-prev-btn" onclick="featuredSliderPrev()" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'" style="position: absolute; left: -3rem; top: 50%; transform: translateY(-50%); width: 40px; height: 40px; background: rgba(255,255,255,0.95); border: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 20; opacity: 0.7; transition: opacity 0.3s;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button id="home-featured-next-btn" onclick="featuredSliderNext()" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'" style="position: absolute; right: -3rem; top: 50%; transform: translateY(-50%); width: 40px; height: 40px; background: rgba(255,255,255,0.95); border: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 20; opacity: 0.7; transition: opacity 0.3s;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
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
                <button id="home-featured-cart-btn" onclick="morettiQuickAddToCart(<?php echo $product_id; ?>)" data-product-id="<?php echo $product_id; ?>" style="display: inline-block; background: transparent; color: #2a2826; padding: 1rem 2rem; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; border: 1px solid #2a2826; cursor: pointer; transition: all 0.3s;">DO KOSZYKA</button>
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

@media (max-width: 1200px) {
    .home-products-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
}

@media (max-width: 992px) {
    .home-products-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

/* Featured product section image should also fit container height consistently */
#featured-slider .slider-image img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    background: #f7f5f2;
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
        aspect-ratio: 1 / 1 !important;
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
            slides.forEach((slide) => slide.classList.remove('active'));
            dots.forEach((dot) => dot.classList.remove('active'));

            slides[index].classList.add('active');
            if (dots[index]) {
                dots[index].classList.add('active');
            }
            currentIndex = index;
        };

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
            <h3 class="font-bold uppercase text-sm mb-2">Darmowa Dostawa</h3>
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
            <p class="text-xs text-taupe-600">30 dni na darmowy zwrot towaru</p>
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

<?php get_footer(); ?>
