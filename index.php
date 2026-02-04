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
        <img src="<?php echo get_template_directory_uri(); ?>/images/photo1.jpg" class="w-full h-full object-cover" alt="Luxury Wallets">
        <div class="absolute inset-0 bg-black/20"></div>
    </div>
    <div class="container mx-auto px-4 relative z-10 text-white">
        <div class="max-w-2xl">
            <span class="text-xs uppercase tracking-[0.4em] mb-4 block font-medium">Od 2002 roku</span>
            <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold leading-none mb-8 uppercase">
                LUXURY BRAND<br>NEVER FADES
            </h1>
            <p class="text-sm md:text-base max-w-md mb-8 opacity-90 leading-relaxed">
                Odkryj naszą wyselekcjonowaną kolekcję portfeli premium, wykonanych z dbałością o każdy detal. Trwałość, która definiuje styl.
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
        <span class="text-[10px] uppercase tracking-[0.3em] text-taupe-500 mb-2">Nowa Kolekcja</span>
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

<!-- 3. PROMO MARQUEE (Screenshot 5) -->
<div class="bg-charcoal py-4 overflow-hidden whitespace-nowrap border-y border-white/10">
    <div class="inline-block animate-marquee uppercase text-white text-sm font-bold tracking-widest">
        <?php for($i=0; $i<10; $i++): ?>
            <span class="mx-8">* 20% ZNIŻKI PRZY ZAPISIE DO NEWSLETTERA</span>
        <?php endfor; ?>
    </div>
</div>

<!-- 4. GENDER SPLIT / CATEGORIES (Screenshot 3) -->
<section class="grid grid-cols-1 md:grid-cols-2 h-[80vh]">
    <!-- Men -->
    <div class="relative group overflow-hidden flex items-center justify-center">
        <img src="<?php echo get_template_directory_uri(); ?>/images/photo2.jpg" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Dla Niego">
        <div class="absolute inset-0 bg-black/30"></div>
        <div class="relative z-10 text-center">
            <h2 class="text-5xl md:text-7xl font-bold text-white uppercase mb-6 tracking-tighter">DLA NIEGO</h2>
            <a href="/kategoria-produktu/portfele-meskie" class="text-xs font-bold text-white border-b-2 border-white pb-1 hover:opacity-70 transition-opacity">ZOBACZ WIĘCEJ</a>
        </div>
    </div>
    <!-- Women -->
    <div class="relative group overflow-hidden flex items-center justify-center border-l border-white/10">
        <img src="<?php echo get_template_directory_uri(); ?>/images/photo3.png" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Dla Niej">
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
            <span class="text-[10px] uppercase tracking-[0.3em] text-taupe-500 mb-2">Bestsellery</span>
            <h2 class="text-3xl md:text-4xl font-bold text-charcoal uppercase tracking-tighter">TRENDY SEZONU</h2>
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

<!-- 6. FEATURED DETAIL (Screenshot 5) -->
<section class="container mx-auto px-4 py-20 border-t border-gray-100">
    <div class="grid md:grid-cols-2 gap-12 items-center">
        <div>
            <img src="<?php echo get_template_directory_uri(); ?>/images/photo1.jpg" class="w-full h-auto" alt="Detail">
        </div>
        <div class="max-w-md">
            <span class="text-[10px] uppercase tracking-[0.3em] text-taupe-500 mb-4 block">Exclusive</span>
            <h2 class="text-4xl font-bold text-charcoal uppercase mb-6 leading-tight">RĘCZNIE SZYTA<br>SKÓRA LICOWA</h2>
            <p class="text-taupe-700 mb-8 leading-relaxed">
                Nasze portfele powstają z jednego kawałka wyselekcjonowanej skóry. To gwarantuje nie tylko nieskazitelny wygląd, ale i wytrzymałość, która przetrwa dekady.
            </p>
            <div class="flex items-center gap-4 mb-8">
                <span class="text-2xl font-bold text-charcoal">od 199,00 zł</span>
                <div class="flex text-yellow-400">
                    <?php for($i=0; $i<5; $i++) echo "★"; ?>
                    <span class="text-taupe-400 text-xs ml-2">(12 opinii)</span>
                </div>
            </div>
            <a href="/shop" class="inline-block bg-charcoal text-white px-12 py-4 text-xs font-bold uppercase tracking-widest hover:bg-taupe-800 transition-all">DO KOSZYKA</a>
        </div>
    </div>
</section>

<!-- 7. ICONS OF THE SEASON (Screenshot 8) -->
<section class="relative h-[70vh] flex items-center justify-center text-center">
    <div class="absolute inset-0 z-0">
        <img src="<?php echo get_template_directory_uri(); ?>/images/photo2.jpg" class="w-full h-full object-cover" alt="Icons">
        <div class="absolute inset-0 bg-black/40"></div>
    </div>
    <div class="relative z-10 text-white px-4">
        <h2 class="text-5xl md:text-7xl font-bold uppercase mb-8 tracking-tighter">IKONY SEZONU</h2>
        <a href="/o-nas" class="inline-block bg-white text-charcoal px-12 py-4 text-xs font-bold uppercase tracking-widest hover:bg-gray-200 transition-all">NASZA HISTORIA</a>
    </div>
</section>

<!-- 8. DESTINATION FOR EXQUISITE FASHION (Screenshot 7) -->
<section class="container mx-auto px-4 py-20">
    <div class="grid md:grid-cols-2 gap-16 items-center">
        <div>
            <h2 class="text-4xl font-bold text-charcoal uppercase mb-8 leading-tight">MIEJSCE<br>DOSKONAŁEGO<br>RZEMIOSŁA</h2>
            <p class="text-taupe-700 leading-relaxed mb-8">
                Wkrocz do świata, gdzie tradycyjne techniki spotykają się z nowoczesną elegancją. W Moretti tworzymy kolekcje, które celebrują ponadczasowy design i nienaganne krawiectwo.
            </p>
            <div class="flex flex-wrap gap-8 opacity-50 grayscale">
                <!-- Brand Logos Placeholders -->
                <span class="font-bold text-2xl">LOGOIPSUM</span>
                <span class="font-bold text-2xl">LOGOIPSUM</span>
                <span class="font-bold text-2xl">LOGOIPSUM</span>
            </div>
        </div>
        <div>
            <img src="<?php echo get_template_directory_uri(); ?>/images/photo3.png" class="w-full h-auto grayscale" alt="Fashion">
        </div>
    </div>
</section>

<!-- 9. USP & FAQ (Screenshot 9) -->
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
                    "Z jakiej skóry wykonane są produkty?" => "Używamy wyłącznie naturalnej skóry bydlęcej z polskich garbarni.",
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

<!-- 10. LATEST NEWS (Screenshot 10) -->
<section class="container mx-auto px-4 py-20">
    <div class="flex justify-between items-end mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-charcoal uppercase tracking-tighter">Z BLOGA</h2>
        <a href="/blog" class="text-xs font-bold border-b-2 border-charcoal pb-1 uppercase tracking-widest">Wszystkie wpisy</a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <?php for($i=1; $i<=3; $i++): ?>
        <article class="group cursor-pointer">
            <div class="aspect-square bg-gray-100 mb-6 overflow-hidden">
                <img src="<?php echo get_template_directory_uri(); ?>/images/photo<?php echo $i; ?>.jpg" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
            </div>
            <span class="text-[10px] uppercase tracking-widest text-taupe-500 mb-2 block">3 Aug</span>
            <h3 class="text-xl font-bold uppercase mb-4 leading-tight group-hover:text-taupe-600 transition-colors">Jak rozpoznać prawdziwą skórę? Poradnik dla kupujących.</h3>
            <a href="#" class="text-[10px] font-bold uppercase tracking-widest border-b border-charcoal pb-1">Czytaj więcej</a>
        </article>
        <?php endfor; ?>
    </div>
</section>

<?php get_footer(); ?>
