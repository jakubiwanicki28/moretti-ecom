<?php
/**
 * Opinie: pokazujemy tylko prawdziwe, a o nowe prosimy kupujących mailem.
 *
 * - Gwiazdki i licznik opinii pojawiają się dopiero, gdy produkt ma choć jedną opinię (wcześniej strona główna
 *   rysowała 5 gwiazdek przy „0 opinie”). Polska odmiana: 1 opinia, 2–4 opinie, 5–21 opinii, 22–24 opinie…
 * - 7 dni po zmianie statusu zamówienia na „Zrealizowane” klient dostaje jeden mail z prośbą o opinię i linkami
 *   do formularza przy każdym kupionym produkcie (Action Scheduler). Link „nie chcę takich wiadomości” wypisuje
 *   adres na stałe.
 *
 * Opinię może zostawić także gość — WooCommerce sam oznacza ją jako „zweryfikowany zakup”, gdy email zgadza się
 * z zamówieniem. Wymóg logowania odciąłby większość kupujących (kupują bez konta).
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

const MORETTI_REVIEW_REQUEST_HOOK  = 'moretti_review_request';
const MORETTI_REVIEW_REQUEST_DELAY = 7 * DAY_IN_SECONDS;
const MORETTI_REVIEW_OPTOUT_OPTION = 'moretti_review_optout';

/** Polska odmiana liczebnika: moretti_plural(5, 'opinia', 'opinie', 'opinii') → „5 opinii”. */
function moretti_plural(int $count, string $one, string $few, string $many): string {
    $mod10  = $count % 10;
    $mod100 = $count % 100;
    if ($count === 1) {
        $word = $one;
    } elseif ($mod10 >= 2 && $mod10 <= 4 && ($mod100 < 12 || $mod100 > 14)) {
        $word = $few;
    } else {
        $word = $many;
    }
    return $count . ' ' . $word;
}

function moretti_reviews_label(int $count): string {
    return moretti_plural($count, 'opinia', 'opinie', 'opinii');
}

/** Gwiazdki ★ z prawdziwej średniej (do pół gwiazdki zaokrąglamy w dół); pusty tekst przy braku opinii. */
function moretti_stars_html(WC_Product $product): string {
    $count = (int) $product->get_review_count();
    if ($count < 1) {
        return '';
    }
    $average = (float) $product->get_average_rating();
    $full    = (int) round($average);
    return sprintf(
        '<span class="moretti-stars" aria-label="Ocena %s na 5"><span aria-hidden="true">%s</span><span class="moretti-stars__empty" aria-hidden="true">%s</span> <a class="moretti-stars__count" href="%s">(%s)</a></span>',
        esc_attr(number_format($average, 1, ',', '')),
        str_repeat('★', $full),
        str_repeat('★', 5 - $full),
        esc_url(get_permalink($product->get_id()) . '#reviews'),
        esc_html(moretti_reviews_label($count))
    );
}

/* ---- Prośba o opinię ---------------------------------------------------------------------------------------- */

function moretti_review_request_schedule($order_id): void {
    if (!function_exists('as_schedule_single_action') || as_has_scheduled_action(MORETTI_REVIEW_REQUEST_HOOK, [(int) $order_id], 'moretti-reviews')) {
        return;
    }
    as_schedule_single_action(time() + MORETTI_REVIEW_REQUEST_DELAY, MORETTI_REVIEW_REQUEST_HOOK, [(int) $order_id], 'moretti-reviews');
}
add_action('woocommerce_order_status_completed', 'moretti_review_request_schedule');

function moretti_review_optout_signature(string $email): string {
    return substr(hash_hmac('sha256', strtolower($email), wp_salt('auth')), 0, 24);
}

function moretti_review_is_opted_out(string $email): bool {
    $list = get_option(MORETTI_REVIEW_OPTOUT_OPTION, []);
    return is_array($list) && in_array(moretti_review_optout_signature($email), $list, true);
}

function moretti_review_request_send($order_id): void {
    $order = wc_get_order($order_id);
    if (!$order instanceof WC_Order || !$order->has_status('completed') || $order->get_meta('_moretti_review_requested')) {
        return;
    }
    $email = (string) $order->get_billing_email();
    if ($email === '' || moretti_review_is_opted_out($email)) {
        return;
    }

    $items = '';
    $seen  = [];
    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        if (!$product || isset($seen[$product->get_id()]) || !comments_open($product->get_id())) {
            continue;
        }
        $seen[$product->get_id()] = true;
        $items .= sprintf(
            '<tr><td style="padding:10px 0;border-bottom:1px solid #eee">%s</td><td style="padding:10px 0;border-bottom:1px solid #eee;text-align:right"><a href="%s" style="color:#1a1a1a;font-weight:600">Wystaw opinię</a></td></tr>',
            esc_html($product->get_name()),
            esc_url(get_permalink($product->get_id()) . '#napisz-opinie')
        );
    }
    if ($items === '') {
        return;
    }

    $optout = add_query_arg([
        'moretti_review_optout' => rawurlencode($email),
        'sig'                   => moretti_review_optout_signature($email),
    ], home_url('/'));

    $body = sprintf(
        '<p>Dzień dobry%s,</p>'
        . '<p>dziękujemy za zakupy w Moretti. Jak sprawdza się Twój zakup? Twoja opinia pomoże innym w wyborze, a nam w ulepszaniu kolekcji — zajmie minutę.</p>'
        . '<table style="width:100%%;border-collapse:collapse">%s</table>'
        . '<p style="font-size:12px;color:#888;margin-top:24px">Wysyłamy jedną taką wiadomość po zamówieniu nr %s. <a href="%s" style="color:#888">Nie chcę otrzymywać próśb o opinię</a>.</p>',
        $order->get_billing_first_name() ? ' ' . esc_html($order->get_billing_first_name()) : '',
        $items,
        esc_html($order->get_order_number()),
        esc_url($optout)
    );

    $mailer = WC()->mailer();
    $sent   = $mailer->send($email, 'Jak sprawdza się Twój zakup w Moretti?', $mailer->wrap_message('Podziel się opinią', $body));

    $order->update_meta_data('_moretti_review_requested', time());
    $order->save_meta_data();
    $order->add_order_note($sent ? 'Wysłano prośbę o opinię.' : 'Prośba o opinię: wysyłka maila nie powiodła się.');
}
add_action(MORETTI_REVIEW_REQUEST_HOOK, 'moretti_review_request_send');

/** Link „nie chcę otrzymywać próśb o opinię” z maila. */
add_action('template_redirect', function () {
    if (empty($_GET['moretti_review_optout']) || empty($_GET['sig'])) {
        return;
    }
    $email = sanitize_email(wp_unslash($_GET['moretti_review_optout']));
    $sig   = sanitize_text_field(wp_unslash($_GET['sig']));
    if ($email === '' || !hash_equals(moretti_review_optout_signature($email), $sig)) {
        return;
    }
    $list = get_option(MORETTI_REVIEW_OPTOUT_OPTION, []);
    $list = is_array($list) ? $list : [];
    if (!in_array($sig, $list, true)) {
        $list[] = $sig;
        update_option(MORETTI_REVIEW_OPTOUT_OPTION, $list, false);
    }
    wp_die('Gotowe — nie będziemy już prosić o opinie na ten adres.', 'Moretti', ['response' => 200, 'link_url' => home_url('/'), 'link_text' => 'Wróć do sklepu']);
});

/** Link z maila (#napisz-opinie) i z licznika opinii (#reviews) rozwija sekcję opinii; #napisz-opinie otwiera formularz. */
add_action('wp_footer', function () {
    if (!is_product()) {
        return;
    }
    ?>
    <script>
    (function () {
        function openReviews() {
            var hash = window.location.hash;
            if (hash !== '#reviews' && hash !== '#napisz-opinie') { return; }
            var section = document.getElementById('reviews');
            if (section) { section.open = true; section.scrollIntoView({ block: 'start' }); }
            if (hash === '#napisz-opinie' && typeof window.openReviewModal === 'function') { window.openReviewModal(); }
        }
        openReviews();
        window.addEventListener('hashchange', openReviews);
    })();
    </script>
    <?php
}, 30);
