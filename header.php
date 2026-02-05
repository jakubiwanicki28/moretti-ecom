<?php
/**
 * Header template - CEIN style minimalist design
 * 
 * @package Moretti
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class('bg-white text-charcoal'); ?>>
<?php wp_body_open(); ?>

<!-- Top Banner (optional - can be controlled via Customizer) -->
<?php if (get_theme_mod('show_top_banner', true)) : ?>
<div class="bg-charcoal text-white text-center py-2 px-4 text-xs md:text-sm">
    <?php echo wp_kses_post(get_theme_mod('top_banner_text', 'Darmowa dostawa przy zamówieniach powyżej 250 zł. <a href="/shop" class="underline">Kup teraz</a>')); ?>
</div>
<?php endif; ?>

<header class="bg-white border-b border-gray-100 sticky top-0 z-50">
    <div class="container mx-auto">
        <div class="flex items-center h-16 md:h-18 px-4">
            
            <!-- Mobile Menu Button (ONLY MOBILE) -->
            <div class="w-12 md:hidden">
                <a 
                    href="#mobile-nav" 
                    id="mobile-menu-link"
                    class="w-12 h-12 flex items-center justify-center text-charcoal hover:text-taupe-600"
                    onclick="event.preventDefault(); var menu = document.getElementById('mobile-menu'); if(menu.style.display === 'block') { menu.style.display = 'none'; } else { menu.style.display = 'block'; } return false;"
                >
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </a>
            </div>

            <!-- Logo (Center on mobile, Left on desktop) -->
            <div class="flex-shrink-0 flex items-center justify-center md:justify-start absolute left-1/2 -translate-x-1/2 md:relative md:left-auto md:translate-x-0">
                <?php if (has_custom_logo()) : ?>
                    <div class="max-w-[120px] md:max-w-none">
                        <?php the_custom_logo(); ?>
                    </div>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="text-lg md:text-2xl font-bold tracking-[0.3em] text-charcoal hover:text-taupe-700 transition-colors uppercase whitespace-nowrap">
                        <?php echo get_bloginfo('name'); ?>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Desktop Navigation (ONLY DESKTOP) -->
            <nav class="hidden md:flex items-center space-x-8 flex-1 justify-center">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container' => false,
                    'menu_class' => 'flex space-x-8 items-center',
                    'fallback_cb' => 'moretti_default_menu',
                    'items_wrap' => '<ul class="%2$s">%3$s</ul>',
                    'link_before' => '<span class="text-sm text-charcoal hover:text-taupe-600 transition-colors uppercase tracking-wide">',
                    'link_after' => '</span>',
                ));
                ?>
            </nav>

            <!-- Right Icons -->
            <div class="w-auto flex items-center justify-end space-x-1 md:space-x-4">
                <!-- Search Icon -->
                <a 
                    href="#search" 
                    id="search-toggle"
                    class="w-10 h-10 flex items-center justify-center text-charcoal hover:text-taupe-600 transition-colors" 
                    aria-label="Search"
                    onclick="event.preventDefault(); var search = document.getElementById('search-bar'); if(search.style.display === 'block') { search.style.display = 'none'; } else { search.style.display = 'block'; document.getElementById('search-input').focus(); } return false;"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </a>

                <?php if (class_exists('WooCommerce')) : ?>
                    <!-- Wishlist Icon (heart) -->
                    <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>" class="w-10 h-10 flex items-center justify-center text-charcoal hover:text-taupe-600 transition-colors relative" aria-label="Wishlist">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </a>

                    <!-- Cart Icon with Counter -->
                    <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="relative text-charcoal hover:text-taupe-600 w-10 h-10 flex items-center justify-center transition-colors" aria-label="Shopping cart">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <?php
                        $cart_count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
                        ?>
                        <?php if ($cart_count > 0) : ?>
                            <span class="absolute top-1 right-1 bg-charcoal text-white text-[8px] w-4 h-4 flex items-center justify-center rounded-full font-bold"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Search Bar Dropdown -->
    <div id="search-bar" <?php echo (is_search() || is_shop() || is_product_taxonomy()) ? 'style="display: block;"' : 'style="display: none;"'; ?> class="bg-white border-t border-b border-gray-100">
        <div class="container mx-auto px-4 py-4">
            <form role="search" method="get" class="flex items-center gap-2" action="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">
                <input 
                    type="search" 
                    id="search-input"
                    name="s" 
                    class="flex-1 px-4 py-3 border border-gray-200 focus:outline-none focus:border-charcoal text-sm"
                    placeholder="Szukaj produktów..."
                    value="<?php echo get_search_query(); ?>"
                />
                <input type="hidden" name="post_type" value="product" />
                <button 
                    type="submit" 
                    class="px-6 py-3 bg-charcoal text-white hover:bg-taupe-700 transition-colors text-sm font-medium uppercase tracking-wide"
                >
                    Szukaj
                </button>
                <?php if (!is_search() && !is_shop() && !is_product_taxonomy()) : ?>
                <button 
                    type="button"
                    onclick="document.getElementById('search-bar').style.display = 'none';"
                    class="px-4 py-3 text-charcoal hover:text-taupe-600 text-sm"
                >
                    ✕
                </button>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Mobile Menu Dropdown (Simple CSS only) -->
    <div id="mobile-menu" style="display: none;" class="md:hidden bg-white border-t border-gray-100">
        <nav class="container mx-auto px-6 py-8">
            <ul class="space-y-6">
                <li><a href="<?php echo esc_url(home_url('/')); ?>" class="block text-xl font-light text-charcoal uppercase tracking-[0.2em] hover:text-taupe-600">Start</a></li>
                <?php if (class_exists('WooCommerce')) : ?>
                    <li><a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="block text-xl font-light text-charcoal uppercase tracking-[0.2em] hover:text-taupe-600">Sklep</a></li>
                <?php endif; ?>
                <li><a href="<?php echo esc_url(home_url('/o-nas')); ?>" class="block text-xl font-light text-charcoal uppercase tracking-[0.2em] hover:text-taupe-600">O nas</a></li>
                <li><a href="<?php echo esc_url(home_url('/kontakt')); ?>" class="block text-xl font-light text-charcoal uppercase tracking-[0.2em] hover:text-taupe-600">Kontakt</a></li>
            </ul>
        </nav>
    </div>
</header>
