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
    <div class="main-product-image mb-6">
        <?php
        if (has_post_thumbnail()) {
            $image_id = $product->get_image_id();
            $image_src = wp_get_attachment_image_src($image_id, 'full');
            $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
            ?>
            <div class="relative bg-white overflow-hidden flex items-center justify-center min-h-[400px] md:min-h-[500px]">
                <img 
                    src="<?php echo esc_url($image_src[0]); ?>" 
                    alt="<?php echo esc_attr($image_alt ? $image_alt : get_the_title()); ?>"
                    class="w-full h-auto block object-contain"
                    id="moretti-main-img"
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
        <div class="product-thumbnails grid grid-cols-4 gap-3">
            <!-- Main image thumbnail -->
            <?php if (has_post_thumbnail()) : 
                $full_url = wp_get_attachment_image_src($product->get_image_id(), 'full')[0];
            ?>
                <button 
                    type="button" 
                    class="thumbnail-item aspect-square bg-white border-2 border-charcoal transition-all overflow-hidden"
                    data-full-url="<?php echo esc_url($full_url); ?>"
                >
                    <?php echo wp_get_attachment_image($product->get_image_id(), 'thumbnail', false, array('class' => 'w-full h-full object-cover')); ?>
                </button>
            <?php endif; ?>

            <!-- Gallery thumbnails -->
            <?php foreach ($attachment_ids as $attachment_id) : 
                $full_url = wp_get_attachment_image_src($attachment_id, 'full')[0];
            ?>
                <button 
                    type="button" 
                    class="thumbnail-item aspect-square bg-white border-2 border-transparent hover:border-gray-200 transition-all overflow-hidden"
                    data-full-url="<?php echo esc_url($full_url); ?>"
                >
                    <?php echo wp_get_attachment_image($attachment_id, 'thumbnail', false, array('class' => 'w-full h-full object-cover')); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const thumbnails = document.querySelectorAll('.thumbnail-item');
                const mainImage = document.getElementById('moretti-main-img');
                
                thumbnails.forEach(thumb => {
                    thumb.addEventListener('click', function(e) {
                        e.preventDefault();
                        const fullUrl = this.dataset.fullUrl;
                        
                        if (mainImage && fullUrl) {
                            mainImage.style.opacity = '0.5';
                            mainImage.src = fullUrl;
                            mainImage.onload = function() {
                                mainImage.style.opacity = '1';
                            };
                        }
                        
                        // Update active state
                        thumbnails.forEach(t => t.style.borderColor = 'transparent');
                        this.style.borderColor = '#2a2826';
                    });
                });
            });
        </script>
        <style>
            #moretti-main-img {
                transition: opacity 0.2s ease-in-out;
            }
            .thumbnail-item {
                cursor: pointer;
            }
        </style>
    <?php endif; ?>
</div>
