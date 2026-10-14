<?php
/**
 * Checkout (blokowy WooCommerce + Przelewy24 + Apaczka).
 *
 * - Jedna zgoda zamiast dwóch: natywny checkbox bloku „Warunki” obejmuje regulamin sklepu i Przelewy24.
 *   Przeglądarka przekazuje jego stan do serwera (Store API extension „moretti”), serwer odrzuca zamówienie
 *   bez zgody i zapisuje ją w zamówieniu; checkbox P24 jest ukryty i stale zaznaczony (assets/js/checkout.js).
 * - Kolejność płatności ustala motyw: P24 sam wypycha swoje „wyróżnione” metody (w tym Raty) na górę.
 * - Mapa punktów Apaczki startuje ze współrzędnych kodu pocztowego klienta (endpoint moretti/v1/postcode,
 *   dane GeoNames w inc/data/pl-postcodes.php), a nie z wyszukiwarki tekstowej widgetu.
 * - Walidacja punktu i wygląd: assets/js/checkout.js + assets/css/checkout.css.
 */

if (!defined('ABSPATH')) {
    exit;
}

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CheckoutSchema;

/** Zmień przy każdej zmianie treści zgody — trafia do zamówienia jako dowód, na co klient się zgodził. */
const MORETTI_TERMS_VERSION = '2026-10-14';

/**
 * Kolejność metod płatności na kasie. Czego tu nie ma, idzie za nimi w kolejności WooCommerce;
 * Raty P24 (303) celowo na końcu — przez pół roku 0 opłaconych, 24 porzucone, gdy stały pierwsze.
 */
const MORETTI_PAYMENT_ORDER = [
    'p24-online-payments-154', // BLIK (przekierowanie do BLIK)
    'p24-blik',                // BLIK z kodem na stronie — pokaże się, gdy P24 włączy BLIK Level 0
    'p24-online-payments',     // przelew online / wybór banku
    'p24-card',
    'p24-apple-pay',
    'p24-google-pay',
];
const MORETTI_PAYMENT_LAST = ['p24-online-payments-303'];

function moretti_checkout_assets() {
    if (!is_cart() && !is_checkout()) {
        return;
    }

    $dir = get_template_directory();
    $uri = get_template_directory_uri();

    wp_enqueue_style('moretti-checkout', $uri . '/assets/css/checkout.css', ['moretti-main-style'], filemtime($dir . '/assets/css/checkout.css'));

    if (is_checkout() && !is_wc_endpoint_url()) {
        wp_enqueue_script('moretti-checkout', $uri . '/assets/js/checkout.js', ['wp-data', 'wc-blocks-data-store'], filemtime($dir . '/assets/js/checkout.js'), true);
        wp_add_inline_script('moretti-checkout', 'window.morettiCheckout = ' . wp_json_encode([
            'postcodeUrl' => rest_url('moretti/v1/postcode/'),
        ]) . ';', 'before');
    }
}
add_action('wp_enqueue_scripts', 'moretti_checkout_assets', 20);

/**
 * Pola adresu: jedno pole „Ulica i numer” (z numerem mieszkania, jak w polskich adresach) zamiast „Adres”
 * + przełącznika „Dodaj nr mieszkania”, który łapał Tab między ulicą a kodem pocztowym. Telefon wymagany —
 * paczkomaty i kurierzy go potrzebują (Apaczka i tak wymuszała go dopiero po wyborze punktu).
 * Ustawione w kodzie, więc odpowiednie opcje w panelu WooCommerce nie mają efektu.
 */
add_filter('pre_option_woocommerce_checkout_address_2_field', function () {
    return 'hidden';
});
add_filter('pre_option_woocommerce_checkout_phone_field', function () {
    return 'required';
});

function moretti_checkout_address_labels($fields) {
    $fields['address_1'] = array_merge($fields['address_1'] ?? [], [
        'label'         => 'Ulica i numer',
        'optionalLabel' => 'Ulica i numer (opcjonalnie)',
    ]);
    return $fields;
}
add_filter('woocommerce_get_country_locale_default', 'moretti_checkout_address_labels', 20);
add_filter('woocommerce_get_country_locale', function ($locales) {
    $locales['PL'] = moretti_checkout_address_labels($locales['PL'] ?? []);
    return $locales;
}, 20);

/**
 * GET /wp-json/moretti/v1/postcode/00-001 → { lat, lng, precision: code|area|region }.
 * Dokładny kod, a gdy go brak w danych — środek okolicy (XX-X) albo regionu (XX).
 */
function moretti_checkout_postcode_route() {
    register_rest_route('moretti/v1', '/postcode/(?P<code>\\d{2}-?\\d{3})', [
        'methods'             => 'GET',
        'permission_callback' => '__return_true',
        'callback'            => function (WP_REST_Request $request) {
            $digits = preg_replace('/\\D/', '', $request['code']);
            $code   = substr($digits, 0, 2) . '-' . substr($digits, 2);
            $data   = require __DIR__ . '/data/pl-postcodes.php';

            foreach ([['codes', $code, 'code'], ['prefix3', substr($code, 0, 4), 'area'], ['prefix2', substr($code, 0, 2), 'region']] as [$table, $key, $precision]) {
                if (isset($data[$table][$key])) {
                    $response = new WP_REST_Response(['lat' => $data[$table][$key][0], 'lng' => $data[$table][$key][1], 'precision' => $precision]);
                    $response->header('Cache-Control', 'public, max-age=2592000');
                    return $response;
                }
            }
            return new WP_Error('moretti_postcode_unknown', 'Nieznany kod pocztowy.', ['status' => 404]);
        },
    ]);
}
add_action('rest_api_init', 'moretti_checkout_postcode_route');

/**
 * Treść jedynej zgody na kasie (HTML z linkami).
 */
function moretti_checkout_terms_text() {
    $terms   = wc_terms_and_conditions_page_id() ? get_permalink(wc_terms_and_conditions_page_id()) : home_url('/regulamin-sklepu/');
    $privacy = get_privacy_policy_url() ?: home_url('/polityka-prywatnosci/');

    return sprintf(
        'Akceptuję <a href="%1$s" target="_blank">Regulamin sklepu</a> oraz <a href="%2$s" target="_blank" rel="noopener">regulamin Przelewy24</a>. Zapoznałem/am się z <a href="%3$s" target="_blank">Polityką prywatności</a> i <a href="%4$s" target="_blank" rel="noopener">obowiązkiem informacyjnym Przelewy24</a>.',
        esc_url($terms),
        'https://www.przelewy24.pl/regulamin',
        esc_url($privacy),
        'https://www.przelewy24.pl/obowiazek-informacyjny-rodo-platnicy'
    );
}

/**
 * Włącza checkbox w bloku „Warunki” i podmienia treść — w kodzie, a nie w treści strony w bazie,
 * żeby zgoda była wersjonowana razem z walidacją poniżej. WooCommerce przenosi atrybuty bloku do data-*.
 */
function moretti_checkout_terms_block($parsed_block) {
    if (($parsed_block['blockName'] ?? '') === 'woocommerce/checkout-terms-block') {
        $parsed_block['attrs'] = array_merge($parsed_block['attrs'] ?? [], [
            'checkbox'      => true,
            'showSeparator' => false,
            'text'          => moretti_checkout_terms_text(),
        ]);
    }
    return $parsed_block;
}
add_filter('render_block_data', 'moretti_checkout_terms_block');

function moretti_checkout_register_store_api_data() {
    if (!function_exists('woocommerce_store_api_register_endpoint_data')) {
        return;
    }

    woocommerce_store_api_register_endpoint_data([
        'endpoint'        => CheckoutSchema::IDENTIFIER,
        'namespace'       => 'moretti',
        'schema_callback' => function () {
            return [
                'terms_accepted' => [
                    'description' => 'Klient zaznaczył zgodę na regulaminy sklepu i Przelewy24.',
                    'type'        => ['boolean', 'null'],
                    'context'     => ['view', 'edit'],
                ],
            ];
        },
    ]);
}
// Motyw ładuje się po „woocommerce_blocks_loaded” (plugins_loaded) — rejestrujemy na init.
add_action('init', 'moretti_checkout_register_store_api_data');

/**
 * Serwerowa walidacja zgody przy składaniu zamówienia (POST /checkout) i zapis dowodu w zamówieniu.
 */
function moretti_checkout_require_terms(WC_Order $order, WP_REST_Request $request) {
    if ($request->get_method() !== 'POST') {
        return;
    }

    if (empty($request['extensions']['moretti']['terms_accepted'])) {
        throw new RouteException('moretti_terms_required', 'Zaznacz zgodę na regulamin, aby złożyć zamówienie.', 400);
    }

    $order->update_meta_data('_moretti_terms_accepted', MORETTI_TERMS_VERSION . ' @ ' . gmdate('c'));
}
add_action('woocommerce_store_api_checkout_update_order_from_request', 'moretti_checkout_require_terms', 10, 2);

/**
 * Kolejność płatności. Priorytet 20 — po P24 (10), który ustawia swoje wyróżnione metody na górze.
 */
function moretti_checkout_order_gateways($wc_gateways) {
    $gateways = $wc_gateways->payment_gateways;
    $first = $middle = $last = [];

    foreach (MORETTI_PAYMENT_ORDER as $id) {
        foreach ($gateways as $key => $gateway) {
            if ($gateway->id === $id) {
                $first[$key] = $gateway;
            }
        }
    }
    foreach ($gateways as $key => $gateway) {
        if (isset($first[$key])) {
            continue;
        }
        if (in_array($gateway->id, MORETTI_PAYMENT_LAST, true)) {
            $last[$key] = $gateway;
        } else {
            $middle[$key] = $gateway;
        }
    }

    $wc_gateways->payment_gateways = $first + $middle + $last;
}
add_action('wc_payment_gateways_initialized', 'moretti_checkout_order_gateways', 20);

/**
 * Teksty P24: przycisk zgodny z regulaminem („Zamawiam i płacę”) i opis transakcji bez polskich znaków —
 * BLIK i wyciągi bankowe gubią „ó” („Numer za mowienia”).
 */
function moretti_checkout_p24_strings($translation, $text, $domain) {
    if ($domain !== 'woocommerce-p24') {
        return $translation;
    }
    switch ($text) {
        case 'Pay with Przelewy24':
            return is_admin() ? $translation : 'Zamawiam i płacę';
        case 'Order no.: %s':
            return 'Moretti zamowienie %s';
    }
    return $translation;
}
add_filter('gettext', 'moretti_checkout_p24_strings', 10, 3);
