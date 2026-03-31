<?php
/**
 * Template Name: Ulubione
 * Strona ulubionych produktów – odczytuje cookie moretti_wishlist (JSON array product IDs).
 */

defined('ABSPATH') || exit;

get_header();

// Odczytaj IDs z cookie ustawianego przez main.js
$wishlist_ids = array();
if (!empty($_COOKIE['moretti_wishlist'])) {
    $decoded = json_decode(urldecode(wp_unslash($_COOKIE['moretti_wishlist'])), true);
    if (is_array($decoded)) {
        $wishlist_ids = array_map('absint', $decoded);
        $wishlist_ids = array_filter($wishlist_ids);
    }
}
?>

<div style="max-width: 1280px; margin: 0 auto; padding: 80px 48px 120px; min-height: 60vh;">

    <h1 style="font-size: clamp(1.8rem, 4vw, 2.8rem); font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; text-align: center; margin-bottom: 64px;">
        Ulubione
    </h1>

    <?php if (empty($wishlist_ids)) : ?>

        <div style="text-align: center; padding: 80px 24px; color: #766a5d;">
            <svg style="width: 56px; height: 56px; margin: 0 auto 24px; display: block; opacity: 0.3;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            <p style="font-size: 1rem; margin-bottom: 8px; color: #2a2826; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em;">Brak ulubionych produktów</p>
            <p style="font-size: 0.875rem; margin-bottom: 32px;">Dodaj produkty do ulubionych klikając ikonę serca na karcie produktu.</p>
            <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>"
               style="display: inline-block; background: #2a2826; color: #fff; font-size: 11px; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase; padding: 14px 32px; text-decoration: none; transition: opacity 0.2s;"
               onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                Przejdź do sklepu
            </a>
        </div>

    <?php else : ?>

        <?php
        $query = new WP_Query(array(
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'post__in'       => $wishlist_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => -1,
        ));
        ?>

        <p style="text-align: center; font-size: 13px; color: #766a5d; margin-bottom: 48px; letter-spacing: 0.05em;">
            <?php echo count($wishlist_ids); ?> <?php echo count($wishlist_ids) === 1 ? 'produkt' : (count($wishlist_ids) < 5 ? 'produkty' : 'produktów'); ?> w ulubionych
        </p>

        <?php if ($query->have_posts()) : ?>

            <ul class="products columns-4" style="list-style: none; padding: 0; margin: 0; display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 32px;">
                <?php
                while ($query->have_posts()) {
                    $query->the_post();
                    global $product;
                    $product = wc_get_product(get_the_ID());
                    if ($product && $product->is_visible()) {
                        wc_get_template_part('content', 'product');
                    }
                }
                wp_reset_postdata();
                ?>
            </ul>

        <?php endif; ?>

    <?php endif; ?>

</div>

<?php get_footer(); ?>
