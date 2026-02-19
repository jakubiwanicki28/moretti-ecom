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
?>

<div class="woocommerce-product-gallery">
    <!-- Main Product Image -->
    <div class="main-product-image mb-6">
        <?php
        if ($main_image_id) {
            $image_src = wp_get_attachment_image_src($main_image_id, 'large');
            $image_alt = get_post_meta($main_image_id, '_wp_attachment_image_alt', true);
            ?>
            <div class="main-product-image-frame relative overflow-hidden flex items-center justify-center">
                <img 
                    src="<?php echo esc_url($image_src[0]); ?>" 
                    alt="<?php echo esc_attr($image_alt ? $image_alt : get_the_title()); ?>"
                    class="main-product-image-el w-full h-full block object-contain"
                    id="moretti-main-img"
                >
            </div>
        <?php
        } else {
            $placeholder_src = wc_placeholder_img_src('woocommerce_single');
            ?>
            <div class="main-product-image-frame relative overflow-hidden flex items-center justify-center">
                <img
                    src="<?php echo esc_url($placeholder_src); ?>"
                    alt="<?php echo esc_attr(get_the_title()); ?>"
                    class="main-product-image-el w-full h-full block object-contain"
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
    $thumb_ids = array();
    if ($main_image_id) {
        $thumb_ids[] = (int) $main_image_id;
    }
    foreach ($attachment_ids as $attachment_id) {
        $attachment_id = (int) $attachment_id;
        if (!in_array($attachment_id, $thumb_ids, true)) {
            $thumb_ids[] = $attachment_id;
        }
    }
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
                    <?php echo wp_get_attachment_image($thumb_id, 'thumbnail', false, array('class' => 'w-full h-full object-cover')); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const gallery = document.querySelector('.woocommerce-product-gallery');
                if (!gallery) return;

                const thumbnails = gallery.querySelectorAll('.thumbnail-item');
                const mainImage = gallery.querySelector('#moretti-main-img');
                if (!mainImage || !thumbnails.length) return;

                thumbnails.forEach(thumb => {
                    thumb.addEventListener('click', function(e) {
                        e.preventDefault();
                        const fullUrl = this.dataset.fullUrl;
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

                        thumbnails.forEach(t => t.classList.remove('is-active'));
                        this.classList.add('is-active');
                    });
                });

                // Ensure first thumbnail can recover main image state.
                const firstThumb = thumbnails[0];
                if (firstThumb && firstThumb.dataset.fullUrl) {
                    firstThumb.addEventListener('keydown', function(event) {
                        if (event.key === 'Enter' || event.key === ' ') {
                            event.preventDefault();
                            this.click();
                        }
                    });
                }
            });
        </script>
    <?php endif; ?>
</div>
