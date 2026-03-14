<?php
/**
 * Single Product Image
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

global $product;

$attachment_ids = $product->get_gallery_image_ids();
$main_image_id = $product->get_image_id();

// If featured image is missing, use first gallery image as main.
if (!$main_image_id && !empty($attachment_ids)) {
    $main_image_id = (int) $attachment_ids[0];
}

$gallery_nav_ids = array();
if ($main_image_id) {
    $gallery_nav_ids[] = (int) $main_image_id;
}
foreach ($attachment_ids as $attachment_id) {
    $attachment_id = (int) $attachment_id;
    if (!in_array($attachment_id, $gallery_nav_ids, true)) {
        $gallery_nav_ids[] = $attachment_id;
    }
}
?>

<div class="woocommerce-product-gallery">
    <!-- Main Product Image -->
    <div class="main-product-image mb-6">
        <?php
        if ($main_image_id) {
            $image_src = wp_get_attachment_image_src($main_image_id, 'large');
            $image_alt = get_post_meta($main_image_id, '_wp_attachment_image_alt', true);
            ?>
            <div class="main-product-image-frame relative overflow-hidden">
                <img 
                    src="<?php echo esc_url($image_src[0]); ?>" 
                    alt="<?php echo esc_attr($image_alt ? $image_alt : get_the_title()); ?>"
                    class="main-product-image-el"
                    id="moretti-main-img"
                >

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

    <!-- Gallery Thumbnails -->
    <?php
    // Build unique thumbnails list: main image first, then gallery images.
    $thumb_ids = $gallery_nav_ids;
    ?>
    <?php if (!empty($thumb_ids)) : ?>
        <div class="product-thumbnails">
            <?php foreach ($thumb_ids as $index => $thumb_id) :
                $full_url = wp_get_attachment_image_src($thumb_id, 'large');
                if (!$full_url || empty($full_url[0])) {
                    continue;
                }
            ?>
                <button
                    type="button"
                    class="thumbnail-item <?php echo $index === 0 ? 'is-active' : ''; ?>"
                    data-full-url="<?php echo esc_url($full_url[0]); ?>"
                    aria-label="<?php echo esc_attr(sprintf('Pokaż zdjęcie %d', $index + 1)); ?>"
                >
                    <?php echo wp_get_attachment_image($thumb_id, 'thumbnail', false, array('class' => 'w-full h-full object-contain')); ?>
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
                    const fullUrl = selectedThumb.dataset.fullUrl;
                    if (!fullUrl) return;

                    mainImage.style.opacity = '0.4';
                    mainImage.src = fullUrl;
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
