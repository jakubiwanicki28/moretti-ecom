<?php
/**
 * Okienko po dodaniu do koszyka: miniatura dodanego produktu, cena, stan koszyka,
 * „Przejdź do koszyka” i „Kupuj dalej”. Na telefonie wysuwa się od dołu, na komputerze na środku ekranu.
 *
 * Działa dla obu dróg dodawania:
 * - formularz na stronie produktu (przeładowanie strony) — okienko drukowane w stopce kolejnego widoku,
 * - szybkie dodanie „+” na kartach (AJAX) — gotowy HTML w fragmencie „moretti_added_to_cart” odpowiedzi
 *   WooCommerce, otwierany przez assets/js/added-to-cart.js na zdarzenie „added_to_cart”.
 */

if (!defined('ABSPATH')) {
    exit;
}

const MORETTI_ADDED_SESSION_KEY = 'moretti_last_added_to_cart';

function moretti_added_to_cart_assets() {
    if (is_cart() || is_checkout()) {
        return;
    }
    $dir = get_template_directory();
    $uri = get_template_directory_uri();
    wp_enqueue_style('moretti-added-to-cart', $uri . '/assets/css/added-to-cart.css', ['moretti-main-style'], filemtime($dir . '/assets/css/added-to-cart.css'));
    wp_enqueue_script('moretti-added-to-cart', $uri . '/assets/js/added-to-cart.js', ['jquery'], filemtime($dir . '/assets/js/added-to-cart.js'), true);
}
add_action('wp_enqueue_scripts', 'moretti_added_to_cart_assets', 20);

function moretti_added_to_cart_remember($cart_item_key, $product_id, $quantity) {
    if (WC()->session) {
        WC()->session->set(MORETTI_ADDED_SESSION_KEY, ['key' => $cart_item_key, 'quantity' => (int) $quantity]);
    }
}
add_action('woocommerce_add_to_cart', 'moretti_added_to_cart_remember', 10, 3);

/**
 * Zwraca i czyści zapamiętany ostatnio dodany produkt (pokazujemy okienko raz).
 */
function moretti_added_to_cart_pull() {
    if (!WC()->session) {
        return null;
    }
    $added = WC()->session->get(MORETTI_ADDED_SESSION_KEY);
    if ($added) {
        WC()->session->set(MORETTI_ADDED_SESSION_KEY, null);
    }
    return $added ?: null;
}

/** „1 produkt”, „3 produkty”, „5 produktów”, „22 produkty” — polska odmiana ma trzy formy. */
function moretti_products_count_label($count) {
    $mod10  = $count % 10;
    $mod100 = $count % 100;
    if ($count === 1) {
        $word = 'produkt';
    } elseif ($mod10 >= 2 && $mod10 <= 4 && ($mod100 < 12 || $mod100 > 14)) {
        $word = 'produkty';
    } else {
        $word = 'produktów';
    }
    return $count . ' ' . $word;
}

function moretti_added_to_cart_html(array $added) {
    $item = WC()->cart ? WC()->cart->get_cart_item($added['key']) : null;
    if (!$item || empty($item['data'])) {
        return '';
    }

    /** @var WC_Product $product */
    $product = $item['data'];
    $count   = WC()->cart->get_cart_contents_count();

    ob_start();
    ?>
    <div class="moretti-atc" id="moretti-atc" role="dialog" aria-modal="true" aria-labelledby="moretti-atc-title" hidden>
        <div class="moretti-atc__backdrop" data-atc-close></div>
        <div class="moretti-atc__panel" tabindex="-1">
            <div class="moretti-atc__head">
                <p class="moretti-atc__title" id="moretti-atc-title">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 13l4 4L19 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Dodano do koszyka
                </p>
                <button type="button" class="moretti-atc__close" data-atc-close aria-label="Zamknij">&times;</button>
            </div>
            <div class="moretti-atc__product">
                <div class="moretti-atc__image"><?php echo $product->get_image('woocommerce_thumbnail'); ?></div>
                <div class="moretti-atc__info">
                    <p class="moretti-atc__name"><?php echo esc_html($product->get_name()); ?></p>
                    <p class="moretti-atc__price">
                        <?php echo wp_kses_post(WC()->cart->get_product_price($product)); ?>
                        <?php if ($added['quantity'] > 1) : ?><span>× <?php echo (int) $added['quantity']; ?></span><?php endif; ?>
                    </p>
                </div>
            </div>
            <p class="moretti-atc__summary">
                W koszyku: <?php echo esc_html(moretti_products_count_label($count)); ?>
                · <?php echo wp_kses_post(WC()->cart->get_cart_subtotal()); ?>
            </p>
            <div class="moretti-atc__actions">
                <a class="moretti-atc__primary" href="<?php echo esc_url(wc_get_cart_url()); ?>">Przejdź do koszyka</a>
                <button type="button" class="moretti-atc__secondary" data-atc-close>Kupuj dalej</button>
            </div>
        </div>
    </div>
    <?php
    return trim(ob_get_clean());
}

// Formularz na stronie produktu i linki ?add-to-cart= — okienko w stopce tej samej odpowiedzi.
add_action('wp_footer', function () {
    if (wp_doing_ajax() || is_cart() || is_checkout()) {
        return;
    }
    $added = moretti_added_to_cart_pull();
    if ($added) {
        echo str_replace('<div class="moretti-atc" ', '<div class="moretti-atc" data-atc-open ', moretti_added_to_cart_html($added));
    }
});

// Szybkie dodanie (AJAX) — HTML okienka jako dodatkowy fragment odpowiedzi WooCommerce.
add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {
    if (wp_doing_ajax() && isset($_REQUEST['wc-ajax']) && $_REQUEST['wc-ajax'] === 'add_to_cart') {
        $added = moretti_added_to_cart_pull();
        if ($added) {
            $fragments['moretti_added_to_cart'] = moretti_added_to_cart_html($added);
        }
    }
    return $fragments;
});

// Komunikat „… został dodany do koszyka” zastępuje okienko; motyw i tak nie wyświetla go na stronie produktu,
// więc kumulował się w sesji i wyskakiwał później w koszyku.
add_filter('wc_add_to_cart_message_html', '__return_empty_string');
