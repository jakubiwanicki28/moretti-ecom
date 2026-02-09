<?php
/**
 * Main template file - Homepage Redesign (STYNRA Style)
 * 
 * @package Moretti
 */

get_header(); ?>

<!-- 1. HERO SECTION (Screenshot 1) -->
<section class="relative h-[90vh] flex items-center overflow-hidden bg-gray-100">
    <div class="absolute inset-0 z-0">
        <img src="<?php echo get_template_directory_uri(); ?>/images/Baner strona www Large.jpeg" class="w-full h-full object-cover" alt="Luxury Wallets">
        <div class="absolute inset-0 bg-black/20"></div>
    </div>
    <div class="container mx-auto px-4 relative z-10 text-white">
        <div class="max-w-2xl">
            <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold leading-none mb-8 uppercase">
                MORETTI FASHION<br>ELEGANCJA I STYL
            </h1>
            <p class="text-sm md:text-base max-w-md mb-8 opacity-90 leading-relaxed">
                Odkryj naszą wyselekcjonowaną kolekcję portfeli premium. Wyjątkowe rzemiosło, które towarzyszy Ci każdego dnia.
            </p>
            <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" 
               class="inline-block bg-white text-charcoal px-12 py-4 text-xs font-bold uppercase tracking-widest hover:bg-charcoal hover:text-white transition-all">
                KUP TERAZ
            </a>
        </div>
    </div>
    
    <!-- Hero Slider Dots -->
    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex gap-3">
        <span class="w-2 h-2 rounded-full bg-white"></span>
        <span class="w-2 h-2 rounded-full bg-white/40"></span>
        <span class="w-2 h-2 rounded-full bg-white/40"></span>
        <span class="w-2 h-2 rounded-full bg-white/40"></span>
    </div>
</section>

<!-- 2. NEW ARRIVALS (Screenshot 1) -->
<section class="container mx-auto px-4 py-20">
    <div class="flex flex-col mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-charcoal uppercase tracking-tighter">NOWOŚCI</h2>
    </div>
    
    <?php
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 3,
        'orderby' => 'date',
        'order' => 'DESC'
    );
    $loop = new WP_Query($args);
    if ($loop->have_posts()) : ?>
        <ul class="products grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php while ($loop->have_posts()) : $loop->the_post(); ?>
                <?php wc_get_template_part('content', 'product'); ?>
            <?php endwhile; ?>
        </ul>
    <?php endif; wp_reset_postdata(); ?>
</section>

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
<section class="grid grid-cols-1 md:grid-cols-2 h-[80vh]">
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
    <div class="relative group overflow-hidden flex items-center justify-center border-l border-white/10">
        <img src="<?php echo get_template_directory_uri(); ?>/images/women-category-v2.png" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Dla Niej">
        <div class="absolute inset-0 bg-black/30"></div>
        <div class="relative z-10 text-center">
            <h2 class="text-5xl md:text-7xl font-bold text-white uppercase mb-6 tracking-tighter">DLA NIEJ</h2>
            <a href="/kategoria-produktu/portfele-damskie" class="text-xs font-bold text-white border-b-2 border-white pb-1 hover:opacity-70 transition-opacity">ZOBACZ WIĘCEJ</a>
        </div>
    </div>
</section>

<!-- 5. TRENDING COLLECTION (Screenshot 3) -->
<section class="container mx-auto px-4 py-20">
    <div class="flex justify-between items-end mb-12">
        <div>
        <h2 class="text-3xl md:text-4xl font-bold text-charcoal uppercase tracking-tighter">KLASYKA I HITY</h2>
    </div>
    <a href="/shop" class="hidden md:block text-xs font-bold border border-charcoal px-6 py-2 hover:bg-charcoal hover:text-white transition-all uppercase tracking-widest">Wszystkie produkty</a>
</div>

<?php
$args = array(
    'post_type' => 'product',
    'posts_per_page' => 6,
    'meta_key' => 'total_sales',
    'orderby' => 'meta_value_num'
);
$loop = new WP_Query($args);
if ($loop->have_posts()) : ?>
    <ul class="products grid grid-cols-2 md:grid-cols-3 gap-x-8 gap-y-12">
        <?php while ($loop->have_posts()) : $loop->the_post(); ?>
            <?php wc_get_template_part('content', 'product'); ?>
        <?php endwhile; ?>
    </ul>
<?php endif; wp_reset_postdata(); ?>
</section>

<!-- 6. COMING SOON / ZAPOWIEDZI -->
<section class="bg-gray-100 py-20">
<div class="container mx-auto px-4">
    <div class="flex flex-col items-center text-center mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-charcoal uppercase tracking-tighter">ZAPOWIEDZI</h2>
        <p class="mt-4 text-taupe-700 max-w-lg">Już za chwilę nowa dostawa wyjątkowych modeli. Zapisz się do newslettera, aby nie przegapić premiery.</p>
    </div>
    
    <?php
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 3,
        'tax_query' => array(
            array(
                'taxonomy' => 'product_tag',
                'field'    => 'slug',
                'terms'    => 'coming-soon',
            ),
        ),
    );
    $loop = new WP_Query($args);
    if ($loop->have_posts()) : ?>
        <ul class="products grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php while ($loop->have_posts()) : $loop->the_post(); ?>
                <?php wc_get_template_part('content', 'product'); ?>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <div class="text-center py-12 border-2 border-dashed border-gray-300 rounded-lg">
            <p class="text-gray-500 italic">Nowe dostawy już w drodze. Sprawdź naszą aktualną ofertę.</p>
        </div>
    <?php endif; wp_reset_postdata(); ?>
</div>
</section>

<!-- 7. LIMITED EDITION "CROWN" -->
<section class="container mx-auto px-4 py-20">
    <div class="bg-charcoal text-white p-8 md:p-16 relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-8">
        <div class="absolute top-0 right-0 opacity-10 pointer-events-none">
            <svg class="w-64 h-64 md:w-96 md:h-96" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"></path></svg>
        </div>
        <div class="relative z-10 max-w-2xl">
            <h2 class="text-4xl md:text-6xl font-bold uppercase mb-6 tracking-tighter">KOLEKCJA CROWN</h2>
            <p class="text-sm md:text-base opacity-80 leading-relaxed">
                Ekskluzywna linia portfeli sygnowana koroną. Wyjątkowe wzornictwo i limitowana ilość egzemplarzy dla osób ceniących unikalność.
            </p>
        </div>
        <div class="relative z-10 shrink-0">
            <a href="/tag/crown" class="inline-block bg-white text-charcoal px-12 py-4 text-xs font-bold uppercase tracking-widest hover:bg-taupe-100 transition-all">
                ODKRYJ CROWN
            </a>
        </div>
    </div>
</section>

<!-- 8. FEATURED DETAIL (Screenshot 5) -->
<section class="container mx-auto px-4 py-20 border-t border-gray-100">
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
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: start;">
        <!-- Image Slider Column -->
        <div style="display: flex; justify-content: center; align-items: flex-start; padding: 3rem 0;">
            <div id="featured-slider" style="position: relative; width: 100%; max-width: 600px; height: 550px; overflow: visible;">
                <div style="height: 100%; position: relative;">
                    <?php foreach ($slider_images as $index => $image_id) : 
                        $image_url = wp_get_attachment_image_url($image_id, 'large');
                        if (!$image_url) continue;
                    ?>
                        <div class="slider-image" data-index="<?php echo $index; ?>" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: <?php echo $index === 0 ? '1' : '0'; ?>; transition: opacity 0.7s ease;">
                            <img src="<?php echo esc_url($image_url); ?>" style="width: 100%; height: 100%; object-fit: contain;" alt="<?php the_title(); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (count($slider_images) > 1) : ?>
                <!-- Slider Arrows -->
                <button onclick="featuredSliderPrev()" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'" style="position: absolute; left: -3rem; top: 50%; transform: translateY(-50%); width: 40px; height: 40px; background: rgba(255,255,255,0.95); border: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 20; opacity: 0.7; transition: opacity 0.3s;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button onclick="featuredSliderNext()" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'" style="position: absolute; right: -3rem; top: 50%; transform: translateY(-50%); width: 40px; height: 40px; background: rgba(255,255,255,0.95); border: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 20; opacity: 0.7; transition: opacity 0.3s;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </button>

                <!-- Slider Dots -->
                <div style="position: absolute; bottom: -2.5rem; left: 50%; transform: translateX(-50%); display: flex; gap: 0.5rem; z-index: 20;">
                    <?php foreach ($slider_images as $index => $image_id) : ?>
                        <button onclick="featuredSliderGoTo(<?php echo $index; ?>)" class="slider-dot" data-index="<?php echo $index; ?>" style="width: <?php echo $index === 0 ? '24px' : '8px'; ?>; height: 8px; border-radius: 4px; background: <?php echo $index === 0 ? '#2a2826' : 'rgba(42,40,38,0.2)'; ?>; border: none; cursor: pointer; transition: all 0.3s; padding: 0;"></button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Content Column -->
        <div style="padding: 3rem 0;">
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
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="<?php the_permalink(); ?>" style="display: inline-block; background: #2a2826; color: #fff; padding: 1rem 3rem; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; text-decoration: none; transition: background 0.3s;">ZOBACZ SZCZEGÓŁY</a>
                <button onclick="morettiQuickAddToCart(<?php echo $product_id; ?>)" data-product-id="<?php echo $product_id; ?>" style="display: inline-block; background: transparent; color: #2a2826; padding: 1rem 2rem; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; border: 1px solid #2a2826; cursor: pointer; transition: all 0.3s;">DO KOSZYKA</button>
            </div>
        </div>
    </div>
    <?php endif; wp_reset_postdata(); ?>
</section>

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
                "Czy oferujecie grawerowanie?" => "Tak, na wybranych modelach portfeli męskich oferujemy personalizację.",
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
<div class="flex justify-between items-end mb-12">
    <h2 class="text-3xl md:text-4xl font-bold text-charcoal uppercase tracking-tighter">Z BLOGA</h2>
    <a href="/blog" class="text-xs font-bold border-b-2 border-charcoal pb-1 uppercase tracking-widest">Wszystkie wpisy</a>
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
