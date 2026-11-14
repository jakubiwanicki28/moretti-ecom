<?php
/**
 * Darmowa dostawa od MORETTI_FREE_SHIPPING_FROM (inc/store-facts.php) i pasek „brakuje Ci X zł”.
 *
 * Dlaczego nie osobna metoda „Darmowa wysyłka” WooCommerce: klient wybiera przewoźnika i punkt (Paczkomat, DPD,
 * Orlen — mapa Apaczki jest przypięta do konkretnych metod). Osobna metoda „za 0 zł” nie miałaby punktu odbioru.
 * Zamiast tego od progu zerujemy koszt każdej metody, a klient wybiera dalej to samo co zwykle.
 *
 * Podstawa progu: wartość produktów brutto po rabatach (kupony), bez kosztu dostawy.
 *
 * Pasek postępu:
 * - okienko „Dodano do koszyka” — HTML z serwera (inc/added-to-cart.php),
 * - koszyk i kasa (bloki WooCommerce) — assets/js/free-shipping.js czyta dane z Store API (rozszerzenie
 *   „moretti_free_shipping” koszyka), więc liczy to samo co serwer i odświeża się przy każdej zmianie koszyka.
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

use Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema;

/** Wartość produktów w koszyku brutto po rabatach. */
function moretti_free_shipping_basis(): float {
    if (!function_exists('WC') || !WC()->cart) {
        return 0.0;
    }
    return (float) WC()->cart->get_cart_contents_total() + (float) WC()->cart->get_cart_contents_tax();
}

/** @return array{threshold: float, basis: float, remaining: float, qualifies: bool, percent: int} */
function moretti_free_shipping_state(?float $basis = null): array {
    $basis     = $basis ?? moretti_free_shipping_basis();
    $threshold = (float) MORETTI_FREE_SHIPPING_FROM;
    $remaining = max(0.0, round($threshold - $basis, 2));

    return [
        'threshold' => $threshold,
        'basis'     => round($basis, 2),
        'remaining' => $remaining,
        'qualifies' => $remaining <= 0.0,
        'percent'   => (int) min(100, max(0, floor($basis / $threshold * 100))),
    ];
}

/** Od progu każda metoda dostawy kosztuje 0 zł. */
function moretti_free_shipping_rates($rates, $package) {
    $basis = 0.0;
    foreach ($package['contents'] ?? [] as $item) {
        $basis += (float) ($item['line_total'] ?? 0) + (float) ($item['line_tax'] ?? 0);
    }

    if ($basis + 0.001 < MORETTI_FREE_SHIPPING_FROM) {
        return $rates;
    }

    foreach ($rates as $rate) {
        if ($rate instanceof WC_Shipping_Rate) {
            $rate->set_cost(0);
            $rate->set_taxes(array_map('__return_zero', (array) $rate->get_taxes()));
        }
    }
    return $rates;
}
add_filter('woocommerce_package_rates', 'moretti_free_shipping_rates', 20, 2);

/**
 * Próg w danych paczki: WooCommerce trzyma wyliczone stawki w sesji klienta i liczy je od nowa dopiero, gdy
 * paczka się zmieni. Dzięki temu po wdrożeniu (albo zmianie progu) koszyki już otwarte dostają nowe stawki.
 */
add_filter('woocommerce_cart_shipping_packages', function ($packages) {
    foreach ($packages as &$package) {
        $package['moretti_free_shipping_from'] = MORETTI_FREE_SHIPPING_FROM;
    }
    return $packages;
});

/* ---- Pasek postępu ------------------------------------------------------------------------------------------ */

function moretti_free_shipping_message(array $state): string {
    return $state['qualifies']
        ? 'Masz darmową dostawę'
        : sprintf('Brakuje Ci %s do darmowej dostawy', moretti_money_label($state['remaining']));
}

/** HTML paska (okienko „Dodano do koszyka”). Ten sam wygląd buduje free-shipping.js w koszyku i kasie. */
function moretti_free_shipping_bar_html(?array $state = null): string {
    $state = $state ?? moretti_free_shipping_state();
    return sprintf(
        '<div class="moretti-fs%s" role="status"><p class="moretti-fs__text">%s</p><div class="moretti-fs__track" aria-hidden="true"><span style="width:%d%%"></span></div></div>',
        $state['qualifies'] ? ' is-free' : '',
        esc_html(moretti_free_shipping_message($state)),
        $state['percent']
    );
}

/** Dane dla koszyka i kasy (Store API). */
function moretti_free_shipping_store_api() {
    if (!function_exists('woocommerce_store_api_register_endpoint_data')) {
        return;
    }
    woocommerce_store_api_register_endpoint_data([
        'endpoint'        => CartSchema::IDENTIFIER,
        'namespace'       => 'moretti_free_shipping',
        'data_callback'   => function () {
            $state = moretti_free_shipping_state();
            $state['message'] = moretti_free_shipping_message($state);
            return $state;
        },
        'schema_callback' => function () {
            $number = ['type' => 'number', 'context' => ['view', 'edit'], 'readonly' => true];
            return [
                'threshold' => $number,
                'basis'     => $number,
                'remaining' => $number,
                'percent'   => $number,
                'qualifies' => ['type' => 'boolean', 'context' => ['view', 'edit'], 'readonly' => true],
                'message'   => ['type' => 'string', 'context' => ['view', 'edit'], 'readonly' => true],
            ];
        },
        'schema_type'     => ARRAY_A,
    ]);
}
add_action('init', 'moretti_free_shipping_store_api');

function moretti_free_shipping_assets() {
    $dir = get_template_directory();
    $uri = get_template_directory_uri();

    wp_enqueue_style('moretti-free-shipping', $uri . '/assets/css/free-shipping.css', ['moretti-main-style'], filemtime($dir . '/assets/css/free-shipping.css'));

    if ((is_cart() || is_checkout()) && !is_wc_endpoint_url()) {
        wp_enqueue_script('moretti-free-shipping', $uri . '/assets/js/free-shipping.js', ['wp-data', 'wc-blocks-data-store'], filemtime($dir . '/assets/js/free-shipping.js'), true);
    }
}
add_action('wp_enqueue_scripts', 'moretti_free_shipping_assets', 20);
