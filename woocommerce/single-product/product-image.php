<?php
/**
 * Single Product Image
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

global $product;

$attachment_ids = $product->get_gallery_image_ids();
?>

<div class="woocommerce-product-gallery">
    <!-- Main Product Image -->
    <div class="main-product-image mb-4">
        <?php
        if (has_post_thumbnail()) {
            $image_id = $product->get_image_id();
            $image_src = wp_get_attachment_image_src($image_id, 'full');
            $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
            ?>
            <div class="relative aspect-[3/4] bg-sand-50 overflow-hidden">
                <img 
                    src="<?php echo esc_url($image_src[0]); ?>" 
                    alt="<?php echo esc_attr($image_alt ? $image_alt : get_the_title()); ?>"
                    class="w-full h-full object-cover"
                >
            </div>
        <?php
        } else {
            echo wc_placeholder_img();
        }
        ?>
    </div>

    <!-- Gallery Thumbnails -->
    <?php if (!empty($attachment_ids)) : ?>
        <div class="product-thumbnails grid grid-cols-4 gap-2">
            <!-- Main image thumbnail -->
            <?php if (has_post_thumbnail()) : ?>
                <button 
                    type="button" 
                    class="thumbnail-item aspect-square bg-sand-50 overflow-hidden border-2 border-charcoal"
                    data-image-id="<?php echo $product->get_image_id(); ?>"
                >
                    <?php echo wp_get_attachment_image($product->get_image_id(), 'thumbnail', false, array('class' => 'w-full h-full object-cover')); ?>
                </button>
            <?php endif; ?>

            <!-- Gallery thumbnails -->
            <?php foreach ($attachment_ids as $attachment_id) : ?>
                <button 
                    type="button" 
                    class="thumbnail-item aspect-square bg-sand-50 overflow-hidden border-2 border-gray-200 hover:border-charcoal transition-colors"
                    data-image-id="<?php echo $attachment_id; ?>"
                >
                    <?php echo wp_get_attachment_image($attachment_id, 'thumbnail', false, array('class' => 'w-full h-full object-cover')); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <script>
            // Simple gallery switcher
            document.addEventListener('DOMContentLoaded', function() {
                const thumbnails = document.querySelectorAll('.thumbnail-item');
                const mainImage = document.querySelector('.main-product-image img');
                
                thumbnails.forEach(thumb => {
                    thumb.addEventListener('click', function() {
                        const imageId = this.dataset.imageId;
                        const newSrc = this.querySelector('img').src.replace('-150x150', '');
                        
                        if (mainImage) {
                            mainImage.src = newSrc;
                        }
                        
                        // Update active state
                        thumbnails.forEach(t => t.classList.remove('border-charcoal'));
                        thumbnails.forEach(t => t.classList.add('border-gray-200'));
                        this.classList.add('border-charcoal');
                        this.classList.remove('border-gray-200');
                    });
                });
            });
        </script>
    <?php endif; ?>
</div>
