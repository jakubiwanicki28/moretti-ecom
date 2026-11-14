<?php
/**
 * Szybkość: skrypty wtyczek, których kupujący nie potrzebuje (audyt 2026-11-12 pkt 9).
 *
 * 1. Przelewy24 dokłada na KAŻDEJ stronie mały skrypt kasy (p24-block-checkout), który ciągnie ~37 skryptów
 *    WooCommerce Blocks/React (~258 KB gzip). Skrypt reaguje tylko na zdarzenia kasy — poza kasą jest zbędny.
 * 2. Apaczka rejestruje swój skrypt kasy z zależnościami od bibliotek edytora WordPressa (wp-blocks,
 *    wp-components…, ~378 KB gzip), choć jej własny plik budowania (apaczka-pl-block-frontend.asset.php) ich nie
 *    wymaga. Podmieniamy zależności na prawdziwe — bez zmian w plikach wtyczki, więc aktualizacja ich nie cofnie.
 *    Po aktualizacji Apaczki: sprawdzić, czy mapa punktów działa (ten plik pisze, czego oczekuje).
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

add_action('wp_enqueue_scripts', function () {
    if (!(is_checkout() && !is_wc_endpoint_url())) {
        wp_dequeue_script('p24-block-checkout');
    }
}, 20);

/** Zależności skryptu kasy Apaczki według jego pliku budowania (+ wc-settings, z którego czyta konfigurację). */
const MORETTI_APACZKA_SCRIPT_DEPS = ['react', 'wc-blocks-checkout', 'wc-settings', 'wp-data', 'wp-element', 'wp-i18n'];

function moretti_apaczka_trim_deps(): void {
    $scripts = wp_scripts();
    if (isset($scripts->registered['apaczka-pl-wc-blocks-integration'])) {
        $scripts->registered['apaczka-pl-wc-blocks-integration']->deps = MORETTI_APACZKA_SCRIPT_DEPS;
    }
}
add_action('wp_print_scripts', 'moretti_apaczka_trim_deps', 1);
add_action('wp_print_footer_scripts', 'moretti_apaczka_trim_deps', 1);
