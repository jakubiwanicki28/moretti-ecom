<?php
/**
 * „Najniższa cena z 30 dni przed obniżką” (art. 4 ust. 2 ustawy o informowaniu o cenach, wyjaśnienia UOKiK).
 *
 * Zasady, których pilnujemy:
 * - informację pokazujemy TYLKO przy ogłaszanej obniżce (produkt w promocji), wszędzie gdzie widać cenę obniżoną
 *   (karta produktu i karty w listach), bezpośrednio przy cenie, dokładnie tą formułą;
 * - kwota to najniższa cena produktu w okresie 30 dni PRZED rozpoczęciem obniżki — nie cena bieżąca.
 *
 * WooCommerce nie przechowuje historii cen, więc zapisujemy ją sami w meta produktu (_moretti_price_history):
 * każda zmiana ceny obowiązującej (przy zapisie w panelu, imporcie, starcie/końcu promocji z harmonogramu) to wpis
 * [znacznik czasu UTC, cena]. Trzymamy 400 dni — promocja może trwać długo, a liczymy od jej początku.
 *
 * Przed wdrożeniem historii nie było. Dla produktów, które już są w promocji, zakładamy, że przed obniżką
 * obowiązywała cena regularna (tak jest w tym sklepie — ceny się nie zmieniają, a promocje są rzadkie).
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

const MORETTI_PRICE_HISTORY_META = '_moretti_price_history';
const MORETTI_PRICE_HISTORY_KEEP = 400 * DAY_IN_SECONDS;
const MORETTI_OMNIBUS_WINDOW     = 30 * DAY_IN_SECONDS;

/** @return array<int, array{0: int, 1: float}> posortowane rosnąco po czasie */
function moretti_price_history(WC_Product $product): array {
    $history = get_post_meta($product->get_id(), MORETTI_PRICE_HISTORY_META, true);
    return is_array($history) ? $history : [];
}

/** Dopisuje cenę obowiązującą, jeśli różni się od ostatniej zapisanej. */
function moretti_price_history_record($product): void {
    if (!$product instanceof WC_Product || $product->is_type('variable') || $product->get_price() === '') {
        return;
    }

    $price   = round((float) $product->get_price(), 2);
    $history = moretti_price_history($product);
    $last    = end($history);
    if ($last && abs((float) $last[1] - $price) < 0.005) {
        return;
    }

    $history[] = [time(), $price];
    $cutoff    = time() - MORETTI_PRICE_HISTORY_KEEP;
    // Najstarszy wpis sprzed okna zostaje: mówi, jaka cena obowiązywała na jego początku.
    while (count($history) > 1 && $history[1][0] < $cutoff) {
        array_shift($history);
    }
    update_post_meta($product->get_id(), MORETTI_PRICE_HISTORY_META, $history);
}
add_action('woocommerce_after_product_object_save', 'moretti_price_history_record');

/** Cena obowiązująca w chwili $ts według historii; null, gdy historia zaczyna się później. */
function moretti_price_at(array $history, int $ts): ?float {
    $price = null;
    foreach ($history as [$time, $value]) {
        if ($time > $ts) {
            break;
        }
        $price = (float) $value;
    }
    return $price;
}

/** Początek obecnej obniżki: data z harmonogramu promocji albo moment, od którego obowiązuje obecna cena. */
function moretti_sale_started_at(WC_Product $product, array $history): ?int {
    if ($product->get_date_on_sale_from()) {
        return $product->get_date_on_sale_from()->getTimestamp();
    }
    $current = round((float) $product->get_price(), 2);
    $start   = null;
    foreach (array_reverse($history) as [$time, $value]) {
        if (abs((float) $value - $current) >= 0.005) {
            break;
        }
        $start = (int) $time;
    }
    return $start;
}

/**
 * Najniższa cena z 30 dni przed obniżką; null, gdy produkt nie jest w promocji (wtedy nic nie pokazujemy).
 */
function moretti_get_lowest_price_last_30_days($product): ?float {
    if (!$product instanceof WC_Product || !$product->is_on_sale() || $product->is_type('variable')) {
        return null;
    }

    $history = moretti_price_history($product);
    $regular = (float) $product->get_regular_price();
    $start   = moretti_sale_started_at($product, $history);

    if ($start === null) {
        return $regular > 0 ? $regular : null;
    }

    $window_start = $start - MORETTI_OMNIBUS_WINDOW;
    $at_start     = moretti_price_at($history, $window_start);
    $candidates   = [$at_start ?? $regular];
    foreach ($history as [$time, $value]) {
        if ($time > $window_start && $time < $start) {
            $candidates[] = (float) $value;
        }
    }
    $candidates = array_filter($candidates, fn($p) => $p > 0);

    return $candidates ? min($candidates) : null;
}

/** Linia przy cenie: strona produktu i karty w listach. Pusty tekst, gdy produkt nie jest w promocji. */
function moretti_omnibus_note_html($product, string $class = 'product-lowest-price-note'): string {
    $lowest = moretti_get_lowest_price_last_30_days($product);
    if ($lowest === null) {
        return '';
    }
    return sprintf(
        '<div class="%s">Najniższa cena z 30 dni przed obniżką: %s</div>',
        esc_attr($class),
        wp_kses_post(wc_price(wc_get_price_to_display($product, ['price' => $lowest])))
    );
}

/**
 * Jednorazowo przy wdrożeniu: historia startuje od bieżącej ceny każdego produktu, żeby kolejne zmiany
 * miały punkt odniesienia.
 */
function moretti_price_history_seed(): void {
    if (get_option('moretti_price_history_seeded')) {
        return;
    }
    update_option('moretti_price_history_seeded', time(), false);
    foreach (wc_get_products(['limit' => -1, 'status' => ['publish', 'private', 'draft'], 'type' => ['simple', 'external']]) as $product) {
        moretti_price_history_record($product);
    }
}
add_action('admin_init', 'moretti_price_history_seed');
add_action('woocommerce_scheduled_sales', 'moretti_price_history_seed', 5);
