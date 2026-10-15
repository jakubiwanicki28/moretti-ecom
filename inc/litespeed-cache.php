<?php
/**
 * LiteSpeed Cache — bezpieczne ustawienia dla sklepu.
 *
 * 2026-10-15: po włączeniu wtyczki z zastaną konfiguracją („Zapisuj REST API” włączone)
 * koszyk z /wp-json/wc/store/v1/cart trafiał do publicznego cache i jeden klient
 * widział koszyk drugiego, a licznik w nagłówku pokazywał cudzą liczbę produktów.
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

/**
 * Odpowiedzi REST (koszyk, kasa, ulubione) są zawsze prywatne — nawet gdy ktoś
 * włączy w panelu „Zapisuj REST API do pamięci podręcznej”.
 */
add_action('rest_api_init', function () {
    do_action('litespeed_control_set_nocache', 'moretti: REST API nie jest cache\'owane');
}, 1);

/**
 * Klient z koszykiem dostaje własną wersję strony (licznik koszyka w nagłówku
 * jest renderowany po stronie serwera). To samo robi opcja WooCommerce „Vary for
 * Mini Cart”, ale tu działa niezależnie od panelu; trafia też do .htaccess przy
 * każdym zapisie ustawień wtyczki.
 */
add_filter('litespeed_vary_cookies', function ($cookies) {
    $cookies[] = 'woocommerce_cart_hash';
    return array_values(array_unique($cookies));
});

/**
 * Konfiguracja przez API, bez klikania w panelu (tylko administrator, hasło aplikacji).
 *
 * GET  /wp-json/moretti/v1/litespeed — odczyt ustawień, które mają znaczenie dla koszyka.
 * POST /wp-json/moretti/v1/litespeed — zapis bezpiecznego zestawu; przechodzi tą samą
 *      ścieżką co „Zapisz zmiany” w panelu (przepisuje .htaccess), potem czyści cały cache.
 */
add_action('rest_api_init', function () {
    register_rest_route('moretti/v1', '/litespeed', array(
        array(
            'methods'             => 'GET',
            'callback'            => 'moretti_litespeed_settings_snapshot',
            'permission_callback' => 'moretti_litespeed_settings_permission',
        ),
        array(
            'methods'             => 'POST',
            'callback'            => 'moretti_litespeed_apply_safe_settings',
            'permission_callback' => 'moretti_litespeed_settings_permission',
        ),
    ));
});

function moretti_litespeed_settings_permission() {
    return current_user_can('manage_options');
}

/**
 * Bezpieczny zestaw: bez cache'owania REST (domyślnie włączone po instalacji wtyczki)
 * i bez trybu gościa, który pomija rozpoznawanie klienta z koszykiem.
 */
function moretti_litespeed_safe_settings() {
    return array(
        'cache-rest' => false,
        'guest'      => false,
        'guest_optm' => false,
    );
}

function moretti_litespeed_settings_snapshot() {
    if (!class_exists('\LiteSpeed\Conf')) {
        return new WP_Error('moretti_litespeed_inactive', 'LiteSpeed Cache nie jest aktywny.', array('status' => 409));
    }

    $conf   = \LiteSpeed\Conf::cls();
    $report = array();
    foreach (array('cache', 'cache-priv', 'cache-rest', 'cache-mobile', 'guest', 'guest_optm', 'esi', 'object') as $id) {
        $report[$id] = $conf->has_conf($id) ? $conf->conf($id) : null;
    }
    $report['vary_cookies'] = apply_filters('litespeed_vary_cookies', array());

    return rest_ensure_response($report);
}

function moretti_litespeed_apply_safe_settings() {
    if (!class_exists('\LiteSpeed\Conf')) {
        return new WP_Error('moretti_litespeed_inactive', 'LiteSpeed Cache nie jest aktywny.', array('status' => 409));
    }

    $conf = \LiteSpeed\Conf::cls();

    // Zapis ustawień przepisuje też plik object-cache.php, gdy włączony jest cache obiektów
    // LiteSpeed — a ten plik należy do wtyczki Redis. W takim stanie nic nie zmieniamy.
    if ($conf->conf('object')) {
        return new WP_Error('moretti_litespeed_object_cache', 'Włączony cache obiektów LiteSpeed — zapis nadpisałby plik Redisa. Przerwano.', array('status' => 409));
    }

    $conf->update_confs(moretti_litespeed_safe_settings());
    do_action('litespeed_purge_all', 'moretti: bezpieczne ustawienia sklepu');

    return moretti_litespeed_settings_snapshot();
}
