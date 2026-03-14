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
            // Fetch more products and then collapse color variants by SKU/model.
            // We will only render up to 5 distinct models below.
            'posts_per_page' => 30,
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
                    <?php
                    $rendered_models = array();
                    $rendered_count  = 0;
                    $max_models      = 5;
                    ?>
                    <?php if ($loop->have_posts()) : ?>
                        <?php while ($loop->have_posts()) : $loop->the_post(); ?>
                            <?php
                            if ($rendered_count >= $max_models) {
                                break;
                            }

                            $product = wc_get_product(get_the_ID());
                            if ($product instanceof WC_Product) {
                                $sku = (string) $product->get_sku();
                            } else {
                                $sku = '';
                            }

                            $model_key = '';
                            if (function_exists('moretti_parse_sku_model_and_color') && $sku !== '') {
                                $parsed = moretti_parse_sku_model_and_color($sku);
                                if (!empty($parsed['model'])) {
                                    $model_key = $parsed['model'];
                                }
                            }

                            if ($model_key === '') {
                                // Fallback: treat product ID as its own "model" when SKU/model is not available.
                                $model_key = 'id-' . get_the_ID();
                            }

                            if (isset($rendered_models[$model_key])) {
                                // Another color variant of a model already rendered in this section – skip.
                                continue;
                            }

                            $rendered_models[$model_key] = true;
                            $rendered_count++;
                            ?>
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

// Zbierz pliki: legacy (N.ext), desktop_N.ext, mobile_N.ext
$orders_map = array(); // order => [ 'desktop' => filename, 'mobile' => filename, 'legacy' => filename ]

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

            $order = null;
            $type = null;

            if (preg_match('/^([0-9]+)\.[^.]+$/i', $hero_banner_entry, $m) === 1) {
                $order = (int) $m[1];
                $type = 'legacy';
            } elseif (preg_match('/^desktop_([0-9]+)\.[^.]+$/i', $hero_banner_entry, $m) === 1) {
                $order = (int) $m[1];
                $type = 'desktop';
            } elseif (preg_match('/^mobile_([0-9]+)\.[^.]+$/i', $hero_banner_entry, $m) === 1) {
                $order = (int) $m[1];
                $type = 'mobile';
            }

            if ($order === null || $type === null) {
                continue;
            }

            if (!isset($orders_map[ $order ])) {
                $orders_map[ $order ] = array('desktop' => null, 'mobile' => null, 'legacy' => null);
            }
            $orders_map[ $order ][ $type ] = $hero_banner_entry;
        }
    }
}

if (!empty($orders_map)) {
    ksort($orders_map, SORT_NUMERIC);
    foreach ($orders_map as $order => $files) {
        $legacy = isset($files['legacy']) ? $files['legacy'] : null;
        $desktop = isset($files['desktop']) ? $files['desktop'] : null;
        $mobile = isset($files['mobile']) ? $files['mobile'] : null;
        $desktop_name = $desktop ?: $legacy;
        $mobile_name = $mobile ?: $legacy;
        if ($desktop_name || $mobile_name) {
            $hero_banners[] = array(
                'order'        => $order,
                'name'         => $desktop_name ?: $mobile_name,
                'desktop_name' => $desktop_name,
                'mobile_name'  => $mobile_name,
            );
        }
    }
}

if (empty($hero_banners)) {
    $hero_banners[] = array(
        'order'        => 1,
        'name'         => 'Baner strona www Large.jpeg',
        'desktop_name' => 'Baner strona www Large.jpeg',
        'mobile_name'  => 'Baner strona www Large.jpeg',
    );
}

$hero_banners_count = count($hero_banners);

$hero_banners_config = array();
$hero_config_path = trailingslashit(get_template_directory()) . 'hero-banners-config.php';
if (is_readable($hero_config_path)) {
    $hero_banners_config = (array) include $hero_config_path;
}
$hero_shop_url = function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : '';
foreach ($hero_banners as $idx => $_b) {
    $cfg = isset($hero_banners_config[ $idx ]) ? $hero_banners_config[ $idx ] : array();
    $hero_banners[ $idx ]['offset_x'] = isset($cfg['offset_x']) ? (int) $cfg['offset_x'] : 0;
    $hero_banners[ $idx ]['cta_url'] = isset($cfg['cta_url']) ? (string) $cfg['cta_url'] : '';
    $hero_banners[ $idx ]['cta_filters'] = isset($cfg['cta_filters']) && is_array($cfg['cta_filters']) ? $cfg['cta_filters'] : array();
    $hero_banners[ $idx ]['cta_category_slug'] = isset($cfg['cta_category_slug']) ? (string) $cfg['cta_category_slug'] : '';
    $hero_banners[ $idx ]['text_position'] = isset($cfg['text_position']) ? (string) $cfg['text_position'] : 'left-center';
    $hero_banners[ $idx ]['title'] = isset($cfg['title']) ? (string) $cfg['title'] : '';
    $hero_banners[ $idx ]['subtitle'] = isset($cfg['subtitle']) ? (string) $cfg['subtitle'] : '';
    $hero_banners[ $idx ]['cta_text'] = isset($cfg['cta_text']) ? (string) $cfg['cta_text'] : 'KUP TERAZ';
    $hero_banners[ $idx ]['overlay_desktop'] = isset($cfg['overlay_desktop']) ? (string) $cfg['overlay_desktop'] : '';
    $hero_banners[ $idx ]['overlay_mobile'] = isset($cfg['overlay_mobile']) ? (string) $cfg['overlay_mobile'] : '';
}
?>
<!-- 1. HERO SECTION (Wittchen-style, izolowany .mh2) -->
<section id="moretti-home-hero" class="mh2">
    <?php if ($hero_banners_count > 1) : ?>
    <div class="mh2__controls-strip" aria-hidden="true">
        <div class="mh2__controls" id="moretti-hero-controls">
            <button type="button" class="mh2__arrow" id="moretti-hero-prev" aria-label="Poprzedni baner">
                <svg class="mh2__arrow-ico" width="11" height="20" viewBox="0 0 11 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.38296 20.0762C0.111788 19.805 0.111788 19.3654 0.38296 19.0942L9.19758 10.2796L0.38296 1.46497C0.111788 1.19379 0.111788 0.754138 0.38296 0.482966C0.654131 0.211794 1.09379 0.211794 1.36496 0.482966L10.4341 9.55214C10.8359 9.9539 10.8359 10.6053 10.4341 11.007L1.36496 20.0762C1.09379 20.3474 0.654131 20.3474 0.38296 20.0762Z" fill="currentColor"/></svg>
            </button>
            <div class="mh2__dots" id="moretti-hero-dots">
                <?php foreach ($hero_banners as $di => $_) : ?>
                <button type="button" class="mh2__dot<?php echo $di === 0 ? ' is-active' : ''; ?>" data-slide-index="<?php echo (int) $di; ?>" aria-label="<?php echo esc_attr(sprintf('Pokaż baner %d', $di + 1)); ?>">
                    <span class="mh2__dot-core" aria-hidden="true"></span>
                </button>
                <?php endforeach; ?>
            </div>
            <button type="button" class="mh2__arrow mh2__arrow--next" id="moretti-hero-next" aria-label="Następny baner">
                <svg class="mh2__arrow-ico" width="11" height="20" viewBox="0 0 11 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.38296 20.0762C0.111788 19.805 0.111788 19.3654 0.38296 19.0942L9.19758 10.2796L0.38296 1.46497C0.111788 1.19379 0.111788 0.754138 0.38296 0.482966C0.654131 0.211794 1.09379 0.211794 1.36496 0.482966L10.4341 9.55214C10.8359 9.9539 10.8359 10.6053 10.4341 11.007L1.36496 20.0762C1.09379 20.3474 0.654131 20.3474 0.38296 20.0762Z" fill="currentColor"/></svg>
            </button>
        </div>
    </div>
    <?php endif; ?>
    <div class="mh2__track-wrap">
        <div class="mh2__track" id="moretti-hero-track">
            <?php foreach ($hero_banners as $hero_banner_index => $hero_banner) : ?>
                <?php
                $offset_x = isset($hero_banner['offset_x']) ? (int) $hero_banner['offset_x'] : 0;
                $img_style = $offset_x !== 0 ? 'object-position: calc(50% + ' . $offset_x . 'px) center;' : '';
                $fallback_src = get_template_directory_uri() . '/images/Baner strona www Large.jpeg';
                $desktop_name = isset($hero_banner['desktop_name']) ? $hero_banner['desktop_name'] : $hero_banner['name'];
                $mobile_name = isset($hero_banner['mobile_name']) ? $hero_banner['mobile_name'] : $hero_banner['name'];
                $same_image = ($desktop_name === $mobile_name);
                $desktop_src = $hero_banner_dir_url . rawurlencode($desktop_name);
                if (!file_exists($hero_banner_dir_path . $desktop_name)) {
                    $desktop_src = $fallback_src;
                }
                $mobile_src = $hero_banner_dir_url . rawurlencode($mobile_name);
                if (!file_exists($hero_banner_dir_path . $mobile_name)) {
                    $mobile_src = $fallback_src;
                }
                $alt = sprintf('Baner %d', $hero_banner_index + 1);
                $loading = $hero_banner_index === 0 ? 'loading="eager" fetchpriority="high"' : 'loading="lazy"';
                $text_pos = isset($hero_banner['text_position']) ? $hero_banner['text_position'] : 'left-center';
                $pos_class = preg_match('/^(left|center|right)-(top|center|bottom)$/', $text_pos) ? 'mh2__content--' . $text_pos : 'mh2__content--left-center';
                if (!empty($hero_banner['cta_url'])) {
                    $slide_url = $hero_banner['cta_url'];
                } elseif (!empty($hero_banner['cta_category_slug']) && taxonomy_exists('product_cat')) {
                    $cat_term = get_term_by('slug', $hero_banner['cta_category_slug'], 'product_cat');
                    $slide_url = ($cat_term && !is_wp_error($cat_term)) ? get_term_link($cat_term) : $hero_shop_url;
                } elseif (!empty($hero_banner['cta_filters']) && $hero_shop_url !== '') {
                    $slide_url = add_query_arg($hero_banner['cta_filters'], $hero_shop_url);
                } else {
                    $slide_url = $hero_shop_url;
                }
                $slide_url = esc_url($slide_url);
                $overlay_d = isset($hero_banner['overlay_desktop']) ? trim($hero_banner['overlay_desktop']) : '';
                $overlay_m = isset($hero_banner['overlay_mobile']) ? trim($hero_banner['overlay_mobile']) : $overlay_d;
                ?>
                <div class="mh2__slide" data-slide-index="<?php echo (int) $hero_banner_index; ?>">
                    <div class="mh2__bg">
                        <?php if ($same_image) : ?>
                        <img src="<?php echo esc_url($desktop_src); ?>" alt="<?php echo esc_attr($alt); ?>" <?php if ($img_style) : ?>style="<?php echo esc_attr($img_style); ?>"<?php endif; ?> <?php echo $loading; ?>>
                        <?php else : ?>
                        <picture>
                            <source srcset="<?php echo esc_url($desktop_src); ?>" media="(min-width: 768px)">
                            <img src="<?php echo esc_url($mobile_src); ?>" alt="<?php echo esc_attr($alt); ?>" <?php if ($img_style) : ?>style="<?php echo esc_attr($img_style); ?>"<?php endif; ?> <?php echo $loading; ?>>
                        </picture>
                        <?php endif; ?>
                    </div>
                    <?php if ($overlay_d !== '' && file_exists($hero_banner_dir_path . $overlay_d)) : ?>
                    <div class="mh2__overlay">
                        <picture>
                            <?php if ($overlay_m !== '' && $overlay_m !== $overlay_d && file_exists($hero_banner_dir_path . $overlay_m)) : ?>
                            <source srcset="<?php echo esc_url($hero_banner_dir_url . rawurlencode($overlay_m)); ?>" media="(max-width: 767px)">
                            <?php endif; ?>
                            <img src="<?php echo esc_url($hero_banner_dir_url . rawurlencode($overlay_d)); ?>" alt="" decoding="async">
                        </picture>
                    </div>
                    <?php endif; ?>
                    <div class="mh2__content <?php echo esc_attr($pos_class); ?>">
                        <?php if (!empty($hero_banner['title'])) : ?>
                        <h2 class="mh2__title"><?php echo wp_kses_post($hero_banner['title']); ?></h2>
                        <?php endif; ?>
                        <?php if (!empty($hero_banner['subtitle'])) : ?>
                        <p class="mh2__subtitle"><?php echo wp_kses_post($hero_banner['subtitle']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($hero_banner['cta_text'])) : ?>
                        <a href="<?php echo $slide_url; ?>" class="mh2__cta"><?php echo esc_html($hero_banner['cta_text']); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<script>
(function() {
    var heroSection = document.getElementById('moretti-home-hero');
    if (!heroSection) return;
    var track = heroSection.querySelector('.mh2__track');
    var dots = heroSection.querySelectorAll('.mh2__dot');
    var slides = heroSection.querySelectorAll('.mh2__slide');
    var prevBtn = heroSection.querySelector('#moretti-hero-prev');
    var nextBtn = heroSection.querySelector('#moretti-hero-next');
    var controlsEl = heroSection.querySelector('.mh2__controls');
    var totalSlides = <?php echo (int) $hero_banners_count; ?>;
    var AUTOPLAY_MS = 5000;
    if (!track || totalSlides <= 1) return;

    var currentSlide = 0;
    var autoplayTimerId = null;
    var isTabVisible = !document.hidden;
    var isWindowFocused = true;
    var isPausedByInteraction = false;

    function updateDots() {
        dots.forEach(function(dot, i) {
            var active = i === currentSlide;
            dot.classList.toggle('is-active', active);
            dot.setAttribute('aria-current', active ? 'true' : 'false');
        });
    }

    function goToSlide(target) {
        currentSlide = (target + totalSlides) % totalSlides;
        track.style.transform = 'translate3d(-' + (currentSlide * 100) + '%, 0, 0)';
        updateDots();
    }

    function clearTimer() {
        if (autoplayTimerId) { clearTimeout(autoplayTimerId); autoplayTimerId = null; }
    }
    function scheduleAutoplay() {
        clearTimer();
        if (!isTabVisible || !isWindowFocused || isPausedByInteraction) return;
        autoplayTimerId = setTimeout(function() { goToSlide(currentSlide + 1); scheduleAutoplay(); }, AUTOPLAY_MS);
    }

    heroSection.addEventListener('click', function(e) {
        var el = e.target;
        while (el && el !== heroSection) {
            if (el.classList && el.classList.contains('mh2__dot')) {
                e.preventDefault(); e.stopPropagation();
                var idx = parseInt(el.getAttribute('data-slide-index'), 10);
                if (!isNaN(idx) && idx >= 0 && idx < totalSlides) { goToSlide(idx); scheduleAutoplay(); }
                return;
            }
            el = el.parentNode;
        }
    }, true);

    if (controlsEl) {
        controlsEl.addEventListener('click', function(e) {
            var el = e.target;
            while (el && el !== controlsEl) {
                if (el.classList && el.classList.contains('mh2__dot')) {
                    e.preventDefault(); e.stopPropagation();
                    var idx = parseInt(el.getAttribute('data-slide-index'), 10);
                    if (!isNaN(idx) && idx >= 0 && idx < totalSlides) { goToSlide(idx); scheduleAutoplay(); }
                    return;
                }
                el = el.parentNode;
            }
        });
    }
    if (prevBtn) prevBtn.addEventListener('click', function(e) { e.preventDefault(); goToSlide(currentSlide - 1); scheduleAutoplay(); });
    if (nextBtn) nextBtn.addEventListener('click', function(e) { e.preventDefault(); goToSlide(currentSlide + 1); scheduleAutoplay(); });

    heroSection.addEventListener('mouseenter', function() { isPausedByInteraction = true; clearTimer(); });
    heroSection.addEventListener('mouseleave', function() { isPausedByInteraction = false; scheduleAutoplay(); });
    heroSection.addEventListener('focusin', function() { isPausedByInteraction = true; clearTimer(); });
    heroSection.addEventListener('focusout', function() {
        setTimeout(function() {
            isPausedByInteraction = heroSection.contains(document.activeElement);
            if (!isPausedByInteraction) scheduleAutoplay();
        }, 0);
    });
    document.addEventListener('visibilitychange', function() {
        isTabVisible = !document.hidden;
        if (!isTabVisible) clearTimer(); else scheduleAutoplay();
    });
    window.addEventListener('blur', function() { isWindowFocused = false; clearTimer(); });
    window.addEventListener('focus', function() { isWindowFocused = true; scheduleAutoplay(); });

    if (slides.length) track.style.willChange = 'transform';
    goToSlide(0);
    scheduleAutoplay();
})();
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
                <button id="home-featured-prev-btn" class="home-featured-image-nav" onclick="featuredSliderPrev()" aria-label="Poprzednie zdjęcie">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button id="home-featured-next-btn" class="home-featured-image-nav" onclick="featuredSliderNext()" aria-label="Następne zdjęcie">
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

/* Strona główna: ta sama mechanika co na gridzie – najechanie pokazuje drugie zdjęcie (np. otwarty portfel) */
@media (hover: hover) {
    .home-products-item .product-card.has-hover-second-image .image-nav,
    .home-products-item .product-card.has-hover-second-image .image-dots {
        display: none !important;
    }
    .home-products-item .product-card.has-hover-second-image .product-image-slide[data-index="0"] {
        opacity: 1;
    }
    .home-products-item .product-card.has-hover-second-image .product-image-slide[data-index="1"] {
        opacity: 0;
    }
    .home-products-item .product-card.has-hover-second-image:hover .product-image-slide[data-index="0"] {
        opacity: 0;
    }
    .home-products-item .product-card.has-hover-second-image:hover .product-image-slide[data-index="1"] {
        opacity: 1;
    }
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
    height: 600px;
    margin: 0;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #1f1d1b 0%, #2a2826 50%, #3b3834 100%);
}

#home-video-break-banner .home-video-break-overlay {
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.12) 0%, rgba(255, 255, 255, 0) 44%),
        radial-gradient(circle at 85% 70%, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0) 42%);
    pointer-events: none;
}

#home-video-break-banner .home-video-break-inner {
    position: relative;
    z-index: 1;
    width: min(1260px, 100%);
    padding: 0 1rem;
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1.4fr);
    gap: 2rem;
    align-items: center;
}

#home-video-break-banner .home-video-break-copy {
    color: #ffffff;
}

#home-video-break-banner .home-video-break-kicker {
    margin: 0 0 0.9rem;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.16em;
    opacity: 0.82;
}

#home-video-break-banner .home-video-break-copy h2 {
    margin: 0 0 1rem;
    font-size: clamp(2rem, 3.3vw, 3.1rem);
    line-height: 1.05;
    font-weight: 700;
    text-transform: uppercase;
}

#home-video-break-banner .home-video-break-copy p {
    margin: 0;
    max-width: 460px;
    color: rgba(255, 255, 255, 0.84);
    line-height: 1.7;
    font-size: 0.95rem;
}

#home-video-break-banner .home-video-break-placeholders {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
}

#home-video-break-banner .home-video-break-card {
    height: 360px;
    border: 1px dashed rgba(255, 255, 255, 0.38);
    background: rgba(255, 255, 255, 0.08);
    border-radius: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    backdrop-filter: blur(1px);
}

#home-video-break-banner .home-video-break-card span {
    font-size: 11px;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.9);
    text-transform: uppercase;
    letter-spacing: 0.12em;
}

@media (max-width: 1200px) {
    .home-products-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    #home-video-break-banner .home-video-break-inner {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    #home-video-break-banner .home-video-break-copy p {
        max-width: 100%;
    }

    #home-video-break-banner .home-video-break-card {
        height: 220px;
    }
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

#featured-slider #home-featured-next-btn {
    right: 8px;
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

    #home-video-break-banner .home-video-break-placeholders {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    #home-video-break-banner .home-video-break-card {
        height: 180px;
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

    #home-video-break-banner .home-video-break-placeholders {
        grid-template-columns: 1fr;
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

<?php get_footer(); ?>
