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
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-light text-charcoal mb-6">
                <?php echo get_the_title() ? get_the_title() : 'About Us'; ?>
            </h1>
            <div class="text-lg md:text-xl text-taupe-700 max-w-3xl mx-auto">
                <?php if (get_the_content()) : ?>
                    <?php the_content(); ?>
                <?php else : ?>
                    <p>We believe in timeless design, sustainable practices, and the power of simplicity. Every piece we create is thoughtfully crafted to elevate your everyday.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Image Section -->
<section class="container mx-auto px-4 py-12 md:py-20">
    <div class="relative h-[50vh] md:h-[60vh] overflow-hidden rounded-lg">
        <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('full', array('class' => 'w-full h-full object-cover')); ?>
        <?php else : ?>
            <div class="w-full h-full bg-sand-100 flex items-center justify-center">
                <p class="text-taupe-600 text-sm">About Us Hero Image</p>
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
                    <h2 class="text-2xl md:text-3xl font-light text-charcoal mb-6">Our Story</h2>
                    <div class="text-taupe-700 space-y-4">
                        <p>Founded in 2020, Moretti began with a simple vision: to create clothing that transcends trends and celebrates the beauty of minimalism.</p>
                        <p>We believe that true style comes from confidence, not complexity. Every piece in our collection is designed to be versatile, timeless, and effortlessly elegant.</p>
                        <p>From our headquarters to your closet, we're committed to sustainable practices, ethical production, and exceptional quality.</p>
                    </div>
                </div>
                <div>
                    <h2 class="text-2xl md:text-3xl font-light text-charcoal mb-6">Our Values</h2>
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-charcoal mb-2">Sustainability</h3>
                            <p class="text-taupe-700 text-sm">We source eco-friendly materials and partner with ethical manufacturers who share our commitment to the planet.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-charcoal mb-2">Quality</h3>
                            <p class="text-taupe-700 text-sm">Each piece is crafted with meticulous attention to detail, designed to last for years, not seasons.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-charcoal mb-2">Simplicity</h3>
                            <p class="text-taupe-700 text-sm">We embrace clean lines, neutral tones, and versatile designs that integrate seamlessly into any wardrobe.</p>
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
            <h2 class="text-3xl md:text-4xl font-light text-charcoal mb-6">Our Mission</h2>
            <p class="text-lg md:text-xl text-taupe-700 mb-8">
                To empower individuals through thoughtfully designed, sustainably produced clothing that enhances their daily lives and respects our planet.
            </p>
            <div class="flex flex-wrap justify-center gap-12 md:gap-16 mt-12">
                <div>
                    <div class="text-4xl md:text-5xl font-light text-charcoal mb-2">500+</div>
                    <div class="text-sm text-taupe-600 uppercase tracking-wider">Products</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-light text-charcoal mb-2">50K+</div>
                    <div class="text-sm text-taupe-600 uppercase tracking-wider">Happy Customers</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-light text-charcoal mb-2">100%</div>
                    <div class="text-sm text-taupe-600 uppercase tracking-wider">Sustainable</div>
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
