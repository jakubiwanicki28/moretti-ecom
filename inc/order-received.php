<?php
/**
 * Strona „Zamówienie otrzymane” (i podgląd zamówienia na koncie) — ostatni ekran zakupu.
 *
 * - Wygląd sklepu zamiast surowych szablonów WooCommerce (assets/css/order-received.css).
 * - Przy odbiorze w punkcie (Paczkomat, DPD, Orlen — Apaczka zapisuje punkt w zamówieniu) nie pokazujemy
 *   „Adresu do wysyłki” z adresem domowym: paczka jedzie do punktu, który widać w wierszu „Wysyłka”,
 *   a adres domowy sugerował coś innego (audyt 2026-11-12, pkt 14).
 * - „Co dalej”: kiedy wyślemy paczkę (następny dzień roboczy po opłaceniu, inc/store-facts.php) i gdzie przyjdzie
 *   numer przesyłki.
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

function moretti_order_has_pickup_point($order): bool {
    return $order instanceof WC_Order && (string) $order->get_meta('apaczka_delivery_point') !== '';
}

function moretti_is_order_view(): bool {
    return function_exists('is_order_received_page') && (is_order_received_page() || is_wc_endpoint_url('view-order'));
}

add_filter('woocommerce_order_needs_shipping_address', function ($needs, $hide, $order) {
    if ($needs && moretti_is_order_view() && moretti_order_has_pickup_point($order)) {
        return false;
    }
    return $needs;
}, 10, 3);

add_action('wp_enqueue_scripts', function () {
    if (!moretti_is_order_view()) {
        return;
    }
    $dir = get_template_directory();
    wp_enqueue_style('moretti-order-received', get_template_directory_uri() . '/assets/css/order-received.css', ['moretti-main-style'], filemtime($dir . '/assets/css/order-received.css'));
}, 20);

/** „Co dalej” — nad szczegółami zamówienia. */
add_action('woocommerce_thankyou', function ($order_id) {
    $order = wc_get_order($order_id);
    if (!$order instanceof WC_Order || !$order->is_paid() || !function_exists('moretti_shipping_dates')) {
        return;
    }
    $paid  = $order->get_date_paid() ? new DateTimeImmutable('@' . $order->get_date_paid()->getTimestamp()) : null;
    $dates = moretti_shipping_dates($paid);
    $where = moretti_order_has_pickup_point($order) ? 'do wybranego punktu odbioru' : 'na podany adres';
    ?>
    <section class="moretti-next-steps" aria-label="Co dalej">
        <h2 class="moretti-next-steps__title">Co dalej</h2>
        <ol class="moretti-next-steps__list">
            <li><strong>Wysyłka <?php echo esc_html(moretti_weekday_label($dates['ship'])); ?></strong> — paczka pojedzie <?php echo esc_html($where); ?>.</li>
            <li>Numer przesyłki wyślemy na <strong><?php echo esc_html($order->get_billing_email()); ?></strong>.</li>
            <li>Masz pytanie? Napisz na <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a> i podaj numer zamówienia <?php echo esc_html($order->get_order_number()); ?>.</li>
        </ol>
    </section>
    <?php
}, 5);
