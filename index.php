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
        $query_args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
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
            <div style="max-width: 1700px; margin: 0 auto; padding: 0 1rem; margin-bottom: 3rem;">
                <div class="flex justify-between items-end pb-4 border-b border-charcoal">
                    <h2 class="text-4xl md:text-6xl font-bold text-charcoal uppercase tracking-tighter"><?php echo esc_html($title); ?></h2>
                    <div class="flex gap-4">
                        <button class="home-carousel-prev w-12 h-12 flex items-center justify-center border border-gray-200 hover:bg-charcoal hover:text-white transition-all" aria-label="<?php echo esc_attr(sprintf('%s poprzednie', $title)); ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <button class="home-carousel-next w-12 h-12 flex items-center justify-center border border-gray-200 hover:bg-charcoal hover:text-white transition-all" aria-label="<?php echo esc_attr(sprintf('%s następne', $title)); ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div style="max-width: 1700px; margin: 0 auto; padding: 0 1rem;">
                <div class="relative overflow-hidden">
                    <div class="home-carousel-track flex transition-transform duration-700 ease-in-out" style="gap: 2rem;">
                        <?php set_query_var('moretti_home_carousel', true); ?>
                        <?php if ($loop->have_posts()) : ?>
                            <?php while ($loop->have_posts()) : $loop->the_post(); ?>
                                <div class="home-carousel-item flex-shrink-0">
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
<section class="grid grid-cols-1 md:grid-cols-2 h-[80vh] divide-y md:divide-y-0 md:divide-x divide-white/10">
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
</section>

<!-- 5. KLASYKA I HITY -->
<?php moretti_render_home_carousel_section('klasyki', 'KLASYKA I HITY', 'klasyka-i-hity', 'py-20 overflow-hidden bg-white'); ?>

<!-- 6. OKAZJE -->
<?php moretti_render_home_carousel_section('okazje', 'OKAZJE', 'okazje', 'py-20 overflow-hidden bg-gray-100'); ?>

<!-- 7. FEATURED DETAIL -->
<section id="home-featured-product" style="max-width: 1700px; margin: 0 auto; padding: 5rem 1rem; border-top: 1px solid #f3f4f6;">
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
        
        // Combine main image + gallery
        $all_images = array_filter(array_merge(array($main_image_id), $gallery_ids));
        // Limit to 3 images for the slider
        $slider_images = array_slice($all_images, 0, 3);
    ?>
    <div id="home-featured-grid" class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-start">
        <!-- Image Slider Column -->
        <div id="home-featured-media-col" style="display: flex; justify-content: center; align-items: flex-start; padding: 3rem 0;">
            <div id="featured-slider" style="position: relative; width: 100%; max-width: 600px; height: 550px; overflow: visible;">
                <div style="height: 100%; position: relative;">
                    <?php foreach ($slider_images as $index => $image_id) : 
                        $image_url = wp_get_attachment_image_url($image_id, 'large');
                        if (!$image_url) continue;
                    ?>
                        <div class="slider-image" data-index="<?php echo $index; ?>" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: <?php echo $index === 0 ? '1' : '0'; ?>; transition: opacity 0.7s ease; overflow: hidden;">
                            <img src="<?php echo esc_url($image_url); ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?php the_title(); ?>">
                        </div>
                    <?php endforeach; ?>
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
/* ===== Homepage carousel consistency ===== */
#nowosci .home-carousel-item,
#klasyki .home-carousel-item,
#okazje .home-carousel-item {
    display: flex;
}

#nowosci .home-carousel-item .product,
#klasyki .home-carousel-item .product,
#okazje .home-carousel-item .product {
    width: 100%;
    margin: 0 !important;
}

#nowosci .home-carousel-item .product-card,
#klasyki .home-carousel-item .product-card,
#okazje .home-carousel-item .product-card {
    display: flex;
    flex-direction: column;
    height: 100%;
    gap: 8px;
    background: transparent !important;
}

#nowosci .home-carousel-item .product-image-slider,
#klasyki .home-carousel-item .product-image-slider,
#okazje .home-carousel-item .product-image-slider {
    margin-bottom: 0 !important;
    background: #f7f5f2 !important;
}

#nowosci .home-carousel-item .slider-images-wrapper,
#klasyki .home-carousel-item .slider-images-wrapper,
#okazje .home-carousel-item .slider-images-wrapper {
    aspect-ratio: 3 / 4 !important;
}

/* Match shop grid behavior: fill height, crop side overflow */
#nowosci .home-carousel-item .slider-image img,
#klasyki .home-carousel-item .slider-image img,
#okazje .home-carousel-item .slider-image img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    background: #f7f5f2;
}

#nowosci .home-carousel-item .product-info,
#klasyki .home-carousel-item .product-info,
#okazje .home-carousel-item .product-info {
    padding: 0 !important;
    background: transparent !important;
}

#nowosci .home-carousel-item .product-name,
#klasyki .home-carousel-item .product-name,
#okazje .home-carousel-item .product-name {
    margin: 0 0 4px !important;
    min-height: 34px;
}

#nowosci .home-carousel-item .product-price,
#klasyki .home-carousel-item .product-price,
#okazje .home-carousel-item .product-price {
    margin: 0 !important;
}

/* Featured product section image should also fit container height consistently */
#featured-slider .slider-image img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    background: #f7f5f2;
}

@media (max-width: 767px) {
    #nowosci .home-carousel-item .slider-images-wrapper,
    #klasyki .home-carousel-item .slider-images-wrapper,
    #okazje .home-carousel-item .slider-images-wrapper {
        aspect-ratio: 3 / 4 !important;
    }

    #nowosci .home-carousel-item .product-name,
    #klasyki .home-carousel-item .product-name,
    #okazje .home-carousel-item .product-name {
        min-height: 30px;
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
        const prev = section ? section.querySelector('.home-carousel-prev') : null;
        const next = section ? section.querySelector('.home-carousel-next') : null;
        const items = Array.from(track.children).filter((child) => child.classList.contains('home-carousel-item'));

        if (!prev || !next || items.length === 0) {
            return;
        }

        let index = 0;
        let itemWidth = 0;
        let dragStartX = 0;
        let dragStartY = 0;
        let dragDelta = 0;
        let verticalDelta = 0;
        let isDragging = false;
        const gap = 32;
        const swipeThreshold = 50;

        const getVisibleItems = () => {
            if (window.innerWidth >= 1024) return 4;
            if (window.innerWidth >= 640) return 2;
            return 2;
        };

        const getMaxIndex = () => Math.max(0, items.length - getVisibleItems());

        const updateCarousel = () => {
            const visibleItems = getVisibleItems();
            const container = track.parentElement;
            if (!container) return;

            itemWidth = (container.offsetWidth - (gap * (visibleItems - 1))) / visibleItems;
            items.forEach((item) => {
                item.style.width = `${itemWidth}px`;
            });

            index = Math.min(index, getMaxIndex());
            const offset = index * (itemWidth + gap);
            track.style.transform = `translateX(-${offset}px)`;
        };

        const moveNext = () => {
            const maxIndex = getMaxIndex();
            index = index < maxIndex ? index + 1 : 0;
            updateCarousel();
        };

        const movePrev = () => {
            const maxIndex = getMaxIndex();
            index = index > 0 ? index - 1 : maxIndex;
            updateCarousel();
        };

        prev.addEventListener('click', movePrev);
        next.addEventListener('click', moveNext);

        const onDragStart = (clientX, clientY = 0) => {
            isDragging = true;
            dragStartX = clientX;
            dragStartY = clientY;
            dragDelta = 0;
            verticalDelta = 0;
        };

        const onDragMove = (clientX, clientY = 0) => {
            if (!isDragging) return;
            dragDelta = clientX - dragStartX;
            verticalDelta = clientY - dragStartY;
        };

        const onDragEnd = () => {
            if (!isDragging) return;
            isDragging = false;

            if (Math.abs(dragDelta) >= swipeThreshold) {
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
