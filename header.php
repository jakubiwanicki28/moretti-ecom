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
    <style>
        /* MORETTI PREMIUM CONTROLS */
        
        /* Shop Page Mobile Overrides */
        @media (max-width: 1023px) {
            .shop-top-bar {
                display: flex !important;
                flex-direction: column !important;
                align-items: flex-start !important;
            }
            .shop-title-section {
                margin-bottom: 20px !important;
            }
            .shop-controls {
                display: flex !important;
                width: 100% !important;
                gap: 10px !important;
            }
            .mobile-filter-btn, .sort-dropdown-wrapper {
                flex: 1 !important;
                width: 50% !important;
            }
            .moretti-custom-select {
                width: 100% !important;
            }
        }

        /* Variations table - clean block layout */
        .woocommerce div.product form.cart .variations {
            display: block !important;
            width: 100% !important;
            border: none !important;
            margin-bottom: 24px !important;
        }
        
        .woocommerce div.product form.cart .variations tbody {
            display: block !important;
        }
        
        .woocommerce div.product form.cart .variations tr {
            display: block !important;
            margin-bottom: 20px !important;
            border: none !important;
        }
        
        .woocommerce div.product form.cart .variations td,
        .woocommerce div.product form.cart .variations th {
            display: block !important;
            padding: 0 !important;
            border: none !important;
            width: 100% !important;
        }
        
        .woocommerce div.product form.cart .variations td.label {
            margin-bottom: 8px !important;
        }
        
        .woocommerce div.product form.cart .variations td.value {
            width: 100% !important;
        }
        
        /* Quantity input - consistent 64px */
        .woocommerce div.product form.cart .quantity input.qty {
            height: 64px !important;
            padding: 0 16px !important;
            text-align: center !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            border: 1px solid #e5e7eb !important;
        }
        
        /* Add to cart button - consistent 64px */
        .woocommerce div.product form.cart button.single_add_to_cart_button {
            height: 64px !important;
            padding: 0 40px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        /* EMPTY CART OVERRIDES */
        .cart-empty, .woocommerce-info, .cart-empty-container {
            display: none !important;
        }
        
        #moretti-empty-cart-override {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
    </style>
    <script>
        // Brutal force fix for empty cart
        document.addEventListener('DOMContentLoaded', function() {
            if (document.body.classList.contains('woocommerce-cart')) {
                const checkEmpty = () => {
                    const content = document.querySelector('.page-content') || document.querySelector('.woocommerce');
                    if (content && (content.innerText.includes('pusty') || content.innerText.includes('empty'))) {
                        // If we don't see our override, but we see the empty message, force it
                        if (!document.getElementById('moretti-empty-cart-override')) {
                            window.location.reload(); // Refresh might help if it's a race condition
                        }
                    }
                };
                setTimeout(checkEmpty, 500);
            }
        });
    </script>
</head>
<body <?php body_class('bg-white text-charcoal'); ?>>
<?php wp_body_open(); ?>

<!-- Top Banner (Hidden by user request) -->
<?php if (false && get_theme_mod('show_top_banner', true)) : ?>
<div class="bg-charcoal text-white text-center py-2 px-4 text-xs md:text-sm">
    <?php echo wp_kses_post(get_theme_mod('top_banner_text', 'Darmowa dostawa przy zamówieniach powyżej 250 zł. <a href="/shop" class="underline">Kup teraz</a>')); ?>
</div>
<?php endif; ?>

<header class="bg-white border-b border-gray-100 sticky top-0 z-50">
    <div style="max-width: 1700px; margin: 0 auto;">
        <div class="flex items-center h-16 md:h-18 px-4" style="position: relative !important;">
            
            <!-- Left Icons (Hamburger + Search) -->
            <div class="flex items-center md:hidden" style="position: absolute !important; left: 10px !important; top: 50% !important; transform: translateY(-50%) !important; z-index: 10 !important;">
                <!-- Mobile Menu Button -->
                <a 
                    href="#mobile-nav" 
                    id="mobile-menu-link"
                    class="w-10 h-10 flex items-center justify-center text-charcoal hover:text-taupe-600"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </a>

                <!-- Search Icon -->
                <a 
                    href="#search" 
                    id="search-toggle-mobile"
                    class="w-10 h-10 flex items-center justify-center text-charcoal hover:text-taupe-600 transition-colors" 
                    aria-label="Search"
                    onclick="event.preventDefault(); var search = document.getElementById('search-bar'); if(search.style.display === 'block') { search.style.display = 'none'; } else { search.style.display = 'block'; document.getElementById('search-input').focus(); } return false;"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </a>
            </div>

            <!-- Logo (Center on mobile, Left on desktop) -->
            <div class="flex-shrink-0 flex items-center justify-center md:justify-start absolute left-1/2 -translate-x-1/2 md:relative md:left-auto md:translate-x-0" style="max-width: 140px !important;">
                <?php if (has_custom_logo()) : ?>
                    <div class="max-w-[100px] md:max-w-none">
                        <?php the_custom_logo(); ?>
                    </div>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="text-lg md:text-2xl font-bold tracking-[0.3em] text-charcoal hover:text-taupe-700 transition-colors uppercase whitespace-nowrap">
                        <?php echo get_bloginfo('name'); ?>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Desktop Navigation (ONLY DESKTOP) -->
            <nav class="hidden md:flex items-center absolute left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2">
                <ul class="flex space-x-12 items-center">
                    <li><a href="<?php echo esc_url(home_url('/')); ?>" class="text-sm text-charcoal hover:text-taupe-600 transition-colors uppercase tracking-[0.2em] font-medium">Start</a></li>
                    <?php if (class_exists('WooCommerce')) : ?>
                        <li><a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="text-sm text-charcoal hover:text-taupe-600 transition-colors uppercase tracking-[0.2em] font-medium">Sklep</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

            <!-- Right Icons (Desktop Search + Cart) -->
            <div class="w-auto flex items-center justify-end space-x-1 md:space-x-4" style="position: absolute !important; right: 10px !important; top: 50% !important; transform: translateY(-50%) !important; z-index: 10 !important;">
                <!-- Desktop Search Icon (Hidden on Mobile) -->
                <a 
                    href="#search" 
                    id="search-toggle"
                    class="hidden md:flex w-10 h-10 items-center justify-center text-charcoal hover:text-taupe-600 transition-colors" 
                    aria-label="Search"
                    onclick="event.preventDefault(); var search = document.getElementById('search-bar'); if(search.style.display === 'block') { search.style.display = 'none'; } else { search.style.display = 'block'; document.getElementById('search-input').focus(); } return false;"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </a>

                <?php if (class_exists('WooCommerce')) : ?>
                    <!-- Wishlist Icon (heart) - HIDDEN BY USER REQUEST -->
                    <!-- 
                    <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>" class="w-10 h-10 flex items-center justify-center text-charcoal hover:text-taupe-600 transition-colors relative" aria-label="Wishlist">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </a>
                    -->

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

    <!-- Search Bar Dropdown - Hidden by default, toggle via search icon -->
    <div id="search-bar" style="display: none;" class="bg-white border-t border-b border-gray-100">
        <div class="container mx-auto px-4" style="padding-top: 2rem; padding-bottom: 2rem;">
            <form role="search" method="get" class="flex items-center gap-2" action="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">
                <input 
                    type="search" 
                    id="search-input"
                    name="s" 
                    class="flex-1 border border-gray-200 focus:outline-none focus:border-charcoal"
                    style="height: 64px; padding: 0 1.5rem; font-size: 14px;"
                    placeholder="Szukaj produktów..."
                    value="<?php echo get_search_query(); ?>"
                />
                <input type="hidden" name="post_type" value="product" />
                <button 
                    type="submit" 
                    class="bg-charcoal text-white hover:bg-taupe-700 transition-colors font-bold uppercase"
                    style="height: 64px; padding: 0 2.5rem; font-size: 11px; letter-spacing: 0.15em; border: 1px solid #2a2826;"
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

    <!-- Mobile Menu Dropdown - SLIDE FROM LEFT -->
    <div id="mobile-menu" class="md:hidden bg-white border-r border-gray-200 fixed top-0 left-0 bottom-0 w-[85vw] max-w-[400px] z-[200] overflow-y-auto transition-transform duration-300 -translate-x-full shadow-2xl">
        <nav class="px-6 py-8">
            <!-- Close Button -->
            <button id="mobile-menu-close" class="absolute top-4 right-4 w-10 h-10 flex items-center justify-center text-charcoal hover:text-taupe-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <!-- Search Bar -->
            <div class="mb-8 mt-8">
                <form role="search" method="get" action="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">
                    <div style="position: relative;">
                        <input 
                            type="search" 
                            name="s" 
                            placeholder="Wpisz szukaną frazę"
                            style="width: 100%; height: 48px; padding: 0 48px 0 16px; border: 1px solid #e5e7eb; border-radius: 4px; font-size: 14px; color: #2a2826;"
                        />
                        <input type="hidden" name="post_type" value="product" />
                        <button type="submit" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">
                            <svg width="20" height="20" fill="none" stroke="#6b7280" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <ul class="space-y-6">
                <li><a href="<?php echo esc_url(home_url('/#nowosci')); ?>" class="mobile-menu-link block text-base font-medium text-charcoal uppercase tracking-[0.1em]">Nowości</a></li>
                <li><a href="<?php echo esc_url(home_url('/#klasyki')); ?>" class="mobile-menu-link block text-base font-medium text-charcoal uppercase tracking-[0.1em]">Klasyki i Hity</a></li>
                
                <!-- Divider -->
                <li style="border-top: 1px solid #f3f4f6; margin: 16px 0 !important; padding-top: 16px;"></li>
                
                <!-- WooCommerce Categories -->
                <?php
                if (class_exists('WooCommerce')) {
                    $categories = get_terms(array(
                        'taxonomy' => 'product_cat',
                        'hide_empty' => true,
                        'exclude' => array(get_option('default_product_cat')),
                    ));
                    
                    if (!empty($categories) && !is_wp_error($categories)) {
                        foreach ($categories as $cat) {
                            echo '<li><a href="' . esc_url(get_term_link($cat)) . '" class="mobile-menu-link block text-base font-medium text-charcoal uppercase tracking-[0.1em]">' . esc_html($cat->name) . '</a></li>';
                        }
                    }
                }
                ?>
                
                <!-- Divider -->
                <li style="border-top: 1px solid #f3f4f6; margin: 16px 0 !important; padding-top: 16px;"></li>
                
                <!-- Login/Logout - HIDDEN BY USER REQUEST -->
                <!-- 
                <li>
                    <?php if (is_user_logged_in()) : ?>
                        <a href="<?php echo wp_logout_url(home_url()); ?>" class="block text-base font-medium text-charcoal uppercase tracking-[0.1em]">Wyloguj się</a>
                    <?php else : ?>
                        <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>" class="block text-base font-medium text-charcoal uppercase tracking-[0.1em]">Zaloguj się</a>
                    <?php endif; ?>
                </li>
                -->
                
                <!-- Social Media Icons -->
                <li class="pt-4">
                    <div style="display: flex; gap: 20px; align-items: center;">
                        <a href="#" aria-label="Facebook" style="color: #766a5d; transition: color 0.2s;" onmouseover="this.style.color='#2a2826'" onmouseout="this.style.color='#766a5d'">
                            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                        </a>
                        <a href="#" aria-label="Instagram" style="color: #766a5d; transition: color 0.2s;" onmouseover="this.style.color='#2a2826'" onmouseout="this.style.color='#766a5d'">
                            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.668-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                    </div>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu-overlay" class="fixed inset-0 bg-black/50 z-[190] hidden"></div>
</header>
