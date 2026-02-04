<?php
/**
 * Template Name: About Page
 * 
 * @package Moretti
 */

get_header(); ?>

<!-- Hero Section -->
<section class="relative bg-cream py-16 md:py-24">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-light text-charcoal mb-6 uppercase tracking-widest">
                O nas
            </h1>
            <div class="text-lg md:text-xl text-taupe-700 max-w-3xl mx-auto">
                <?php if (get_the_content()) : ?>
                    <?php the_content(); ?>
                <?php else : ?>
                    <p>Wierzymy w ponadczasowy design, najwyższą jakość polskiej skóry i rzemiosło, które przetrwa lata. Każdy nasz portfel to owoc pasji do detalu i minimalizmu.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Image Section -->
<section class="container mx-auto px-4 py-12 md:py-20">
    <div class="relative h-[50vh] md:h-[60vh] overflow-hidden rounded-none">
        <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('full', array('class' => 'w-full h-full object-cover')); ?>
        <?php else : ?>
            <div class="w-full h-full bg-sand-100 flex items-center justify-center">
                <p class="text-taupe-600 text-xs uppercase tracking-widest">Nasza Pracownia</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Our Story Section -->
<section class="bg-white py-12 md:py-20">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="grid md:grid-cols-2 gap-12 md:gap-16 items-start">
                <div>
                    <h2 class="text-2xl md:text-3xl font-light text-charcoal mb-6 uppercase tracking-widest text-center md:text-left">Nasza Historia</h2>
                    <div class="text-taupe-700 space-y-4 text-sm leading-relaxed">
                        <p>Moretti narodziło się z miłości do tradycyjnego kaletnictwa. Zaczynaliśmy jako mała rodzinna hurtownia, dostarczając produkty najwyższej klasy do najlepszych sklepów w kraju.</p>
                        <p>Dzisiaj Moretti to marka detaliczna, która stawia na bezpośrednią relację z klientem. Rezygnujemy z pośredników, aby oferować luksusowe portfele w uczciwych cenach.</p>
                        <p>Każdy kawałek skóry jest przez nas starannie selekcjonowany, a każdy szew sprawdzany dwukrotnie.</p>
                    </div>
                </div>
                <div>
                    <h2 class="text-2xl md:text-3xl font-light text-charcoal mb-6 uppercase tracking-widest text-center md:text-left">Nasze Wartości</h2>
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-xs font-semibold text-charcoal mb-2 uppercase tracking-widest">Jakość</h3>
                            <p class="text-taupe-700 text-sm">Używamy wyłącznie skóry licowej, która z wiekiem szlachetnieje i nabiera charakteru.</p>
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold text-charcoal mb-2 uppercase tracking-widest">Rzemiosło</h3>
                            <p class="text-taupe-700 text-sm">Łączymy tradycyjne techniki kaletnicze z nowoczesną technologią ochrony RFID.</p>
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold text-charcoal mb-2 uppercase tracking-widest">Minimalizm</h3>
                            <p class="text-taupe-700 text-sm">Tworzymy akcesoria, które są funkcjonalne i eleganckie bez zbędnych ozdobników.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission Statement -->
<section class="bg-sand-50 py-16 md:py-24">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-light text-charcoal mb-6 uppercase tracking-widest">Nasza Misja</h2>
            <p class="text-lg md:text-xl text-taupe-700 mb-8 leading-relaxed">
                Tworzyć akcesoria, które towarzyszą Ci w najważniejszych momentach życia, łącząc polską tradycję z nowoczesnym stylem życia.
            </p>
            <div class="flex flex-wrap justify-center gap-12 md:gap-16 mt-12">
                <div>
                    <div class="text-4xl md:text-5xl font-light text-charcoal mb-2">15+</div>
                    <div class="text-[10px] text-taupe-600 uppercase tracking-[0.2em]">Lat Doświadczenia</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-light text-charcoal mb-2">100k+</div>
                    <div class="text-[10px] text-taupe-600 uppercase tracking-[0.2em]">Zadowolonych Klientów</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-light text-charcoal mb-2">100%</div>
                    <div class="text-[10px] text-taupe-600 uppercase tracking-[0.2em]">Polska Skóra</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section (Optional) -->
<section class="container mx-auto px-4 py-12 md:py-20">
    <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl md:text-4xl font-light text-charcoal mb-12 text-center">Meet Our Team</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
            <?php 
            $team_members = array(
                array('name' => 'Sarah Johnson', 'role' => 'Founder & CEO'),
                array('name' => 'Michael Chen', 'role' => 'Creative Director'),
                array('name' => 'Emma Rodriguez', 'role' => 'Head of Sustainability'),
                array('name' => 'David Kim', 'role' => 'Operations Manager'),
            );
            
            foreach ($team_members as $member) : ?>
                <div class="text-center">
                    <div class="aspect-square bg-sand-100 rounded-lg mb-4 overflow-hidden">
                        <div class="w-full h-full flex items-center justify-center">
                            <span class="text-taupe-600 text-xs">Team Member</span>
                        </div>
                    </div>
                    <h3 class="text-base font-medium text-charcoal mb-1"><?php echo esc_html($member['name']); ?></h3>
                    <p class="text-sm text-taupe-600"><?php echo esc_html($member['role']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="bg-charcoal text-white py-16 md:py-20">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-2xl md:text-3xl font-light mb-6">Join Our Journey</h2>
        <p class="text-sand-100 mb-8 max-w-2xl mx-auto">
            Be part of a movement that values quality, sustainability, and timeless style.
        </p>
        <?php if (class_exists('WooCommerce')) : ?>
            <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" 
               class="inline-block px-8 py-3 bg-white text-charcoal hover:bg-sand-50 transition-colors">
                Shop Now
            </a>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
