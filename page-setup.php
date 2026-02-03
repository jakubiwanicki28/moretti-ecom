<?php
/**
 * Template Name: Setup Pages
 * 
 * One-time setup page to create default pages
 * Access at: /setup-pages/
 * 
 * @package Moretti
 */

// Run setup if accessed
if (isset($_GET['run_setup']) && $_GET['run_setup'] === 'yes') {
    // Create pages
    moretti_create_default_pages();
    
    // Create sample products if WooCommerce is active
    if (class_exists('WooCommerce')) {
        moretti_create_sample_products();
        $products_created = true;
    }
    
    $setup_complete = true;
}

get_header(); ?>

<div class="container mx-auto px-4 py-16">
    <div class="max-w-2xl mx-auto text-center">
        
        <?php if (isset($setup_complete) && $setup_complete) : ?>
            <!-- Success Message -->
            <div class="bg-green-50 border-2 border-green-600 rounded-lg p-8 mb-8">
                <svg class="w-16 h-16 text-green-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h1 class="text-3xl font-light text-green-900 mb-4">Setup Complete!</h1>
                <p class="text-green-800 mb-6">
                    <?php if (isset($products_created) && $products_created) : ?>
                        About and Contact pages have been created, and <strong>12 sample products</strong> have been added to your shop!
                    <?php else : ?>
                        About and Contact pages have been created and added to your menu.
                    <?php endif; ?>
                </p>
                <div class="space-y-3">
                    <?php if (class_exists('WooCommerce')) : ?>
                        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="block px-6 py-3 bg-green-600 text-white hover:bg-green-700 transition-colors">
                            View Shop (with sample products!)
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo home_url('/about'); ?>" class="block px-6 py-3 bg-green-600 text-white hover:bg-green-700 transition-colors">
                        View About Page
                    </a>
                    <a href="<?php echo home_url('/contact'); ?>" class="block px-6 py-3 bg-green-600 text-white hover:bg-green-700 transition-colors">
                        View Contact Page
                    </a>
                    <a href="<?php echo home_url(); ?>" class="block px-6 py-3 bg-charcoal text-white hover:bg-taupe-800 transition-colors">
                        Go to Homepage
                    </a>
                </div>
            </div>
        <?php else : ?>
            <!-- Setup Form -->
            <div class="bg-sand-50 rounded-lg p-8 mb-8">
                <h1 class="text-4xl font-light text-charcoal mb-4">Theme Setup</h1>
                <p class="text-lg text-taupe-700 mb-8">
                    Click the button below to automatically create About and Contact pages.
                </p>
                
                <div class="bg-white rounded-lg p-6 mb-6 text-left">
                    <h2 class="text-lg font-medium text-charcoal mb-3">This will create:</h2>
                    <ul class="space-y-2 text-taupe-700">
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>About page with company info, mission, values, and team</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Contact page with working form and contact details</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Primary navigation menu with Shop, About, and Contact</span>
                        </li>
                        <?php if (class_exists('WooCommerce')) : ?>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span><strong>12 sample products</strong> in 6 categories (Outerwear, Dresses, Skirts, Pants, Tops, Lounge)</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Color variations for each product (Black, White, Beige, Cream, etc.)</span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <a href="?run_setup=yes" class="inline-block px-8 py-4 bg-charcoal text-white hover:bg-taupe-800 transition-colors text-lg">
                    Run Setup Now
                </a>
                
                <p class="text-sm text-taupe-600 mt-4">
                    Note: If pages already exist, they won't be duplicated.
                </p>
            </div>
        <?php endif; ?>

        <div class="text-sm text-taupe-600">
            <p>After setup is complete, you can delete this page or keep it for future reference.</p>
        </div>
    </div>
</div>

<?php get_footer(); ?>
