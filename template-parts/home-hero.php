<?php
/**
 * Hero slider strony głównej (MORETTI FASHION).
 * Jedno miejsce z strzałkami i kropkami – używane przez index.php i front-page.php.
 * Wymaga w scope: $hero_banners, $hero_banners_count, $hero_banner_dir_path, $hero_banner_dir_url, $hero_shop_url.
 *
 * @package Moretti
 */

if (!isset($hero_banners, $hero_banners_count, $hero_banner_dir_path, $hero_banner_dir_url, $hero_shop_url)) {
    return;
}
?>
<!-- 1. HERO SECTION (Wittchen-style, izolowany .mh2) -->
<section id="moretti-home-hero" class="mh2">
    <div class="mh2__track-wrap">
        <div class="mh2__track" id="moretti-hero-track">
            <?php foreach ($hero_banners as $hero_banner_index => $hero_banner) : ?>
                <?php
                $offset_x = isset($hero_banner['offset_x']) ? (int) $hero_banner['offset_x'] : 0;
                $offset_x_mobile = isset($hero_banner['offset_x_mobile']) ? (int) $hero_banner['offset_x_mobile'] : null;
                $slide_style = '--offset-x: ' . $offset_x . 'px;';
                if ($offset_x_mobile !== null) {
                    $slide_style .= ' --offset-x-mobile: ' . $offset_x_mobile . 'px;';
                }
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
                $cox = isset($hero_banner['content_offset_x']) ? (int) $hero_banner['content_offset_x'] : 0;
                $coy = isset($hero_banner['content_offset_y']) ? (int) $hero_banner['content_offset_y'] : 0;
                $cox_m = isset($hero_banner['content_offset_x_mobile']) ? (int) $hero_banner['content_offset_x_mobile'] : null;
                $coy_m = isset($hero_banner['content_offset_y_mobile']) ? (int) $hero_banner['content_offset_y_mobile'] : null;
                $content_style = ' style="--content-offset-x: ' . $cox . 'px; --content-offset-y: ' . $coy . 'px;';
                if ($cox_m !== null) {
                    $content_style .= ' --content-offset-x-mobile: ' . $cox_m . 'px;';
                }
                if ($coy_m !== null) {
                    $content_style .= ' --content-offset-y-mobile: ' . $coy_m . 'px;';
                }
                $content_style .= '"';
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
                <div class="mh2__slide" data-slide-index="<?php echo (int) $hero_banner_index; ?>" style="<?php echo esc_attr($slide_style); ?>">
                    <div class="mh2__bg">
                        <?php if ($same_image) : ?>
                        <img src="<?php echo esc_url($desktop_src); ?>" alt="<?php echo esc_attr($alt); ?>" <?php echo $loading; ?>>
                        <?php else : ?>
                        <picture>
                            <source srcset="<?php echo esc_url($desktop_src); ?>" media="(min-width: 768px)">
                            <img src="<?php echo esc_url($mobile_src); ?>" alt="<?php echo esc_attr($alt); ?>" <?php echo $loading; ?>>
                        </picture>
                        <?php endif; ?>
                    </div>
                    <div class="mh2__shade" aria-hidden="true">
                        <div class="mh2__shade-gradient"></div>
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
                    <div class="mh2__content <?php echo esc_attr($pos_class); ?>"<?php echo $content_style; ?>>
                        <?php if (!empty($hero_banner['title']) || !empty($hero_banner['subtitle']) || !empty($hero_banner['cta_text'])) : ?>
                        <div class="mh2__text-stack">
                            <?php if (!empty($hero_banner['title'])) : ?>
                            <div class="mh2__title-row">
                                <h2 class="mh2__title"><?php
                                $hero_title_html = function_exists('moretti_hero_format_line_breaks')
                                    ? moretti_hero_format_line_breaks($hero_banner['title'])
                                    : esc_html($hero_banner['title']);
                                echo wp_kses($hero_title_html, array('br' => array()));
                                ?></h2>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($hero_banner['subtitle'])) : ?>
                            <div class="mh2__subtitle-wrap">
                                <p class="mh2__subtitle"><?php
                                $hero_sub_html = function_exists('moretti_hero_format_line_breaks')
                                    ? moretti_hero_format_line_breaks($hero_banner['subtitle'])
                                    : esc_html($hero_banner['subtitle']);
                                echo wp_kses($hero_sub_html, array('br' => array()));
                                ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($hero_banner['cta_text'])) : ?>
                            <a href="<?php echo $slide_url; ?>" class="mh2__cta"><?php echo esc_html($hero_banner['cta_text']); ?></a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
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
