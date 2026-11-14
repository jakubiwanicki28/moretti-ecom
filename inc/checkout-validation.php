<?php
/**
 * Walidacja kasy po stronie serwera (uzupełnienie assets/js/checkout.js).
 *
 * Telefon: WooCommerce sprawdza tylko znaki, więc przechodził np. „12”. Kurier i Paczkomat wysyłają na ten numer
 * SMS z kodem odbioru — zły numer to paczka, której klient nie odbierze. Dla Polski wymagamy 9 cyfr
 * (spacje, myślniki, nawiasy i prefiks +48 / 0048 są dozwolone).
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

function moretti_is_valid_pl_phone(string $phone): bool {
    $digits = preg_replace('/[\s\-().]/', '', $phone);
    $digits = preg_replace('/^(\+48|0048)/', '', $digits);
    return (bool) preg_match('/^\d{9}$/', $digits);
}

add_filter('woocommerce_validate_phone', function ($valid, $phone, $country) {
    if (!$valid || ($country !== null && $country !== '' && $country !== 'PL')) {
        return $valid;
    }
    return moretti_is_valid_pl_phone((string) $phone);
}, 10, 3);
