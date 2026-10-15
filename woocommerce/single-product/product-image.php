<?php
/**
 * Single Product Image
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

// Szerokość głównego zdjęcia w układzie strony produktu (dla srcset).
defined('MORETTI_PRODUCT_MAIN_IMAGE_SIZES') || define('MORETTI_PRODUCT_MAIN_IMAGE_SIZES', '(max-width: 768px) calc(100vw - 24px), (max-width: 1100px) 45vw, 580px');

// Na ekranie wystarcza 1024 px (telefon z gęstym ekranem bez tego sięgał po oryginał,
// zwykle 0,5–4 MB). Pełną rozdzielczość pobiera tylko lupa na komputerze.
$moretti_cap_srcset = function () {
    return 1024;
};
add_filter('max_srcset_image_width', $moretti_cap_srcset);

global $product;

$attachment_ids = $product->get_gallery_image_ids();
$main_image_id = $product->get_image_id();

// Karuzela tylko z galerii – jak na gridzie, bez prowadzenia Featured. Kolejność: galeria, potem ewentualnie main.
$gallery_nav_ids = array();
if (!empty($attachment_ids)) {
    $gallery_nav_ids = array_map('absint', $attachment_ids);
    if ($main_image_id && !in_array((int) $main_image_id, $gallery_nav_ids, true)) {
        $gallery_nav_ids[] = (int) $main_image_id;
    }
} elseif ($main_image_id) {
    $gallery_nav_ids[] = (int) $main_image_id;
}

$first_slide_id = !empty($gallery_nav_ids) ? (int) $gallery_nav_ids[0] : 0;
?>

<div class="woocommerce-product-gallery">
    <!-- Main Product Image (pierwszy z karuzeli, nie Featured) -->
    <div class="main-product-image mb-6">
        <?php
        if ($first_slide_id) {
            $image_alt = get_post_meta($first_slide_id, '_wp_attachment_image_alt', true);
            $zoom_src  = wp_get_attachment_image_url($first_slide_id, 'full');
            ?>
            <div id="moretti-img-wrapper" class="main-product-image-frame relative overflow-hidden">
                <?php
                // Na ekranie miniatura dopasowana do szerokości (srcset); oryginał — bywa kilka MB —
                // dociąga dopiero lupa na komputerze (main.js, data-zoom-src).
                echo wp_get_attachment_image($first_slide_id, 'large', false, array(
                    'alt'           => $image_alt ? $image_alt : get_the_title(),
                    'class'         => 'main-product-image-el',
                    'id'            => 'moretti-main-img',
                    'sizes'         => MORETTI_PRODUCT_MAIN_IMAGE_SIZES,
                    'loading'       => 'eager',
                    'fetchpriority' => 'high',
                    'decoding'      => 'async',
                    'data-zoom-src' => $zoom_src ? $zoom_src : '',
                ));
                ?>

                <?php if (count($gallery_nav_ids) > 1) : ?>
                    <button type="button" class="single-gallery-arrow single-gallery-prev" aria-label="Poprzednie zdjęcie">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <button type="button" class="single-gallery-arrow single-gallery-next" aria-label="Następne zdjęcie">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                <?php endif; ?>
            </div>
        <?php
        } else {
            $placeholder_src = wc_placeholder_img_src('woocommerce_single');
            ?>
            <div class="main-product-image-frame relative overflow-hidden">
                <img
                    src="<?php echo esc_url($placeholder_src); ?>"
                    alt="<?php echo esc_attr(get_the_title()); ?>"
                    class="main-product-image-el"
                    id="moretti-main-img"
                >
            </div>
            <?php
        }
        ?>
    </div>

    <!-- Gallery Thumbnails (ta sama kolejność co karuzela: galeria, potem ewent. main) -->
    <?php
    $thumb_ids = $gallery_nav_ids;
    ?>
    <?php if (!empty($thumb_ids)) : ?>
        <div class="product-thumbnails">
            <?php foreach ($thumb_ids as $index => $thumb_id) :
                $display_url = wp_get_attachment_image_url($thumb_id, 'large');
                if (!$display_url) {
                    continue;
                }
            ?>
                <button
                    type="button"
                    class="thumbnail-item <?php echo $index === 0 ? 'is-active' : ''; ?>"
                    data-src="<?php echo esc_url($display_url); ?>"
                    data-srcset="<?php echo esc_attr((string) wp_get_attachment_image_srcset($thumb_id, 'large')); ?>"
                    data-zoom-src="<?php echo esc_url((string) wp_get_attachment_image_url($thumb_id, 'full')); ?>"
                    aria-label="<?php echo esc_attr(sprintf('Pokaż zdjęcie %d', $index + 1)); ?>"
                >
                    <?php echo wp_get_attachment_image($thumb_id, 'medium', false, array('class' => 'w-full h-full object-contain')); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const gallery = document.querySelector('.woocommerce-product-gallery');
                if (!gallery) return;

                const thumbnails = Array.from(gallery.querySelectorAll('.thumbnail-item'));
                const mainImage = gallery.querySelector('#moretti-main-img');
                const prevBtn = gallery.querySelector('.single-gallery-prev');
                const nextBtn = gallery.querySelector('.single-gallery-next');
                if (!mainImage || !thumbnails.length) return;

                let currentIndex = Math.max(0, thumbnails.findIndex((thumb) => thumb.classList.contains('is-active')));

                const showIndex = (index) => {
                    if (index < 0 || index >= thumbnails.length) return;
                    const selectedThumb = thumbnails[index];
                    const src = selectedThumb.dataset.src;
                    if (!src) return;

                    mainImage.style.opacity = '0.4';
                    mainImage.dataset.zoomSrc = selectedThumb.dataset.zoomSrc || '';
                    if (selectedThumb.dataset.srcset) {
                        mainImage.setAttribute('sizes', <?php echo wp_json_encode(MORETTI_PRODUCT_MAIN_IMAGE_SIZES); ?>);
                        mainImage.setAttribute('srcset', selectedThumb.dataset.srcset);
                    } else {
                        mainImage.removeAttribute('srcset');
                    }
                    mainImage.src = src;
                    mainImage.onload = function() {
                        mainImage.style.opacity = '1';
                        mainImage.onload = null;
                    };
                    mainImage.onerror = function() {
                        mainImage.style.opacity = '1';
                        mainImage.onerror = null;
                    };

                    thumbnails.forEach((thumb) => thumb.classList.remove('is-active'));
                    selectedThumb.classList.add('is-active');
                    currentIndex = index;
                };

                thumbnails.forEach((thumb, index) => {
                    thumb.addEventListener('click', function(e) {
                        e.preventDefault();
                        showIndex(index);
                    });

                    thumb.addEventListener('keydown', function(event) {
                        if (event.key === 'Enter' || event.key === ' ') {
                            event.preventDefault();
                            showIndex(index);
                        }
                    });
                });

                if (prevBtn) {
                    prevBtn.addEventListener('click', function() {
                        const prevIndex = (currentIndex - 1 + thumbnails.length) % thumbnails.length;
                        showIndex(prevIndex);
                    });
                }

                if (nextBtn) {
                    nextBtn.addEventListener('click', function() {
                        const nextIndex = (currentIndex + 1) % thumbnails.length;
                        showIndex(nextIndex);
                    });
                }

                let startX = 0;
                let startY = 0;
                let deltaX = 0;
                let deltaY = 0;

                const frame = gallery.querySelector('.main-product-image-frame');
                if (frame && thumbnails.length > 1) {
                    frame.addEventListener('touchstart', function(event) {
                        if (event.touches.length !== 1) return;
                        startX = event.touches[0].clientX;
                        startY = event.touches[0].clientY;
                        deltaX = 0;
                        deltaY = 0;
                    }, { passive: true });

                    frame.addEventListener('touchmove', function(event) {
                        if (event.touches.length !== 1) return;
                        deltaX = event.touches[0].clientX - startX;
                        deltaY = event.touches[0].clientY - startY;
                        if (Math.abs(deltaX) > Math.abs(deltaY)) {
                            event.preventDefault();
                        }
                    }, { passive: false });

                    frame.addEventListener('touchend', function() {
                        const swipeThreshold = 40;
                        if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > swipeThreshold) {
                            if (deltaX < 0) {
                                showIndex((currentIndex + 1) % thumbnails.length);
                            } else {
                                showIndex((currentIndex - 1 + thumbnails.length) % thumbnails.length);
                            }
                        }
                    });
                }
            });
        </script>
    <?php endif; ?>
</div>

<?php remove_filter('max_srcset_image_width', $moretti_cap_srcset); ?>

<style>
#moretti-main-img {
    will-change: transform;
    display: block !important;
}
</style>
