<?php
/**
 * Main template file - Homepage
 * 
 * @package Moretti
 */

get_header(); ?>

<!-- Hero Section with Large Image -->
<section class="hero-section mb-12 md:mb-16">
    <div class="relative h-[60vh] md:h-[70vh] lg:h-[80vh] overflow-hidden">
        <?php
        // Get featured image or placeholder
        if (has_post_thumbnail()) :
            the_post_thumbnail('full', array('class' => 'w-full h-full object-cover'));
        else :
        ?>
            <div class="w-full h-full bg-sand-100 flex items-center justify-center">
                <div class="text-center px-4">
                    <h1 class="text-4xl md:text-6xl font-light text-charcoal mb-4">
                        <?php bloginfo('name'); ?>
                    </h1>
                    <p class="text-lg md:text-xl text-taupe-700 max-w-2xl mx-auto uppercase tracking-widest">
                        Odkryj ponadczasową elegancję i nowoczesny minimalizm
                    </p>
                    <?php if (class_exists('WooCommerce')) : ?>
                        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" 
                           class="inline-block mt-8 px-10 py-4 bg-charcoal text-white hover:bg-taupe-800 transition-colors font-bold text-sm uppercase tracking-widest">
                            ZOBACZ KOLEKCJĘ
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Content Section with Lorem Ipsum -->
<section class="container mx-auto px-4 mb-12 md:mb-20">
    <div class="max-w-4xl mx-auto">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            
            <article class="prose prose-lg mx-auto text-center mb-12">
                <h2 class="text-2xl md:text-4xl font-light text-charcoal mb-6">
                    <?php echo get_the_title() ? get_the_title() : 'Lorem ipsum dolor sit amet, consectetur'; ?>
                </h2>
                
                <div class="text-taupe-700 leading-relaxed">
                    <?php 
                    if (get_the_content()) :
                        the_content();
                    else :
                    ?>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque sagittis ligula, viverra eget lorem facilisis condimentum. Donec pellentesque vitae. Nullam vitae erat mi a maximus commodo. Quisque non bibendum mauris ac nibendum fermentum. Donec pellentesque vitae. Nullam vitae erat mi a maximus commodo. Quisque non bibendum</p>
                    <?php endif; ?>
                </div>
            </article>

        <?php endwhile; else : ?>
            
            <!-- Default placeholder content -->
            <article class="text-center mb-12">
                <h2 class="text-2xl md:text-4xl font-light text-charcoal mb-6">
                    Lorem ipsum dolor sit amet, consectetur
                </h2>
                <p class="text-taupe-700 leading-relaxed max-w-3xl mx-auto">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque sagittis ligula, viverra eget lorem facilisis condimentum. Donec pellentesque vitae. Nullam vitae erat mi a maximus commodo. Quisque non bibendum mauris ac nibendum fermentum.
                </p>
            </article>

        <?php endif; ?>
    </div>
</section>

<!-- Secondary Hero Image -->
<section class="container mx-auto px-4 mb-12 md:mb-20">
    <div class="relative h-[50vh] md:h-[60vh] overflow-hidden rounded-lg">
        <div class="w-full h-full bg-taupe-200 flex items-center justify-center">
            <p class="text-taupe-600 text-sm">Hero Image Placeholder</p>
        </div>
    </div>
</section>

<!-- Image Gallery Grid (3 columns) -->
<section class="container mx-auto px-4 mb-12 md:mb-20">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
        <?php for ($i = 1; $i <= 3; $i++) : ?>
            <div class="relative aspect-[3/4] overflow-hidden rounded-lg bg-sand-100 group">
                <div class="w-full h-full flex items-center justify-center">
                    <p class="text-taupe-600 text-sm">Image <?php echo $i; ?></p>
                </div>
                <!-- Hover overlay -->
                <div class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>
        <?php endfor; ?>
    </div>
</section>

<!-- Call to Action / Newsletter Section -->
<section class="bg-cream py-16 md:py-24">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-2xl md:text-3xl font-light text-charcoal mb-4 uppercase tracking-widest">
            Bądź na bieżąco
        </h2>
        <p class="text-taupe-700 mb-8 max-w-2xl mx-auto">
            Zapisz się do naszego newslettera i otrzymuj informacje o nowych kolekcjach oraz ofertach specjalnych.
        </p>
        
        <form class="max-w-md mx-auto flex gap-2">
            <input 
                type="email" 
                placeholder="Twój adres e-mail"
                class="flex-1 px-4 py-3 border border-gray-300 focus:border-charcoal focus:outline-none text-sm"
                required
            >
            <button 
                type="submit"
                class="px-6 py-3 bg-charcoal text-white hover:bg-taupe-800 transition-colors text-sm font-bold uppercase tracking-widest"
            >
                Zapisz się
            </button>
        </form>
    </div>
</section>

<!-- Featured Products (if WooCommerce is active) -->
<?php if (class_exists('WooCommerce') && function_exists('wc_get_featured_product_ids')) : 
    $featured_ids = wc_get_featured_product_ids();
    
    if (!empty($featured_ids)) :
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 4,
            'post__in' => $featured_ids,
        );
        
        $featured_query = new WP_Query($args);
        
        if ($featured_query->have_posts()) : ?>
            
            <section class="container mx-auto px-4 py-12 md:py-20">
                <div class="text-center mb-12">
                    <h2 class="text-2xl md:text-3xl font-light text-charcoal mb-2 uppercase tracking-widest">Polecane produkty</h2>
                    <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" 
                       class="text-xs font-bold text-taupe-600 hover:text-charcoal transition-colors uppercase tracking-widest">
                        Zobacz wszystko →
                    </a>
                </div>
                
                <ul class="products grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                    <?php while ($featured_query->have_posts()) : $featured_query->the_post(); ?>
                        <?php wc_get_template_part('content', 'product'); ?>
                    <?php endwhile; ?>
                </ul>
            </section>
            
        <?php endif;
        wp_reset_postdata();
    endif;
endif; ?>

<?php get_footer(); ?>

