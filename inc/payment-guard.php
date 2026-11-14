<?php
/**
 * Strażnik płatności Przelewy24.
 *
 * Wtyczka P24 potwierdza zamówienie, gdy P24 przyśle powiadomienie (urlStatus). Gdy powiadomienie nie dotrze
 * (hosting leży, limit zasobów), zamówienie wisi jako „oczekujące”, po 60 min WooCommerce je anuluje, a pieniądze
 * klienta są pobrane — i nikt się o tym nie dowiaduje. Strażnik zamyka te dziury:
 *
 * 1. Co 10 min pyta P24 o status nieopłaconych zamówień P24 z ostatnich 48 h (także anulowanych przez limit czasu)
 *    i potwierdza te, które P24 widzi jako opłacone (funkcja wtyczki: Transaction::reconcile_pending_payment).
 * 2. Zanim WooCommerce anuluje nieopłacone zamówienie, pyta P24. Nie anuluje, gdy pieniądze dotarły, gdy metoda
 *    z natury trwa dniami (przekaz tradycyjny) albo gdy klient dostał przypomnienie z linkiem do zapłaty.
 * 3. Klient bez potwierdzonej płatności dostaje jedno przypomnienie z linkiem do zapłaty tego samego zamówienia
 *    (15 min po zamówieniu; przelew bankowy 40 min, bo bank potrafi potwierdzać kilkanaście minut).
 * 4. Właściciel (portfelland@gmail.com) dostaje maila tylko w sytuacjach wymagających ręki: płatność po anulowaniu, płatność, której sklep
 *    nie usłyszał, rozbieżność P24/sklep, dwie opłacone płatności jednego klienta.
 *
 * Wszystko, co strażnik robi, ląduje w notatkach zamówienia z prefiksem „Strażnik płatności”.
 * Zadania w tle: Action Scheduler (WooCommerce) — uruchamiany przez WP-Cron, czyli przez ruch na stronie.
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

const MORETTI_GUARD_SWEEP_HOOK         = 'moretti_payment_guard_sweep';
const MORETTI_GUARD_GROUP              = 'moretti-payment-guard';
const MORETTI_GUARD_SWEEP_INTERVAL     = 10 * MINUTE_IN_SECONDS;
const MORETTI_GUARD_LOOKBACK           = 48 * HOUR_IN_SECONDS;
const MORETTI_GUARD_MIN_AGE            = 5 * MINUTE_IN_SECONDS;   // normalna ścieżka ma pierwszeństwo
const MORETTI_GUARD_REMIND_AFTER       = 15 * MINUTE_IN_SECONDS;
const MORETTI_GUARD_REMIND_AFTER_BANK  = 40 * MINUTE_IN_SECONDS;
const MORETTI_GUARD_REMINDED_GRACE     = 24 * HOUR_IN_SECONDS;    // tyle czeka zamówienie po przypomnieniu
const MORETTI_GUARD_SLOW_METHODS       = [178];                   // przekaz tradycyjny: pieniądze idą dniami
const MORETTI_GUARD_SLOW_GRACE         = 72 * HOUR_IN_SECONDS;
const MORETTI_GUARD_DOUBLE_WINDOW      = 2 * HOUR_IN_SECONDS;

const MORETTI_GUARD_META_AUTO_CANCELLED = '_moretti_guard_auto_cancelled';
const MORETTI_GUARD_META_REMINDED       = '_moretti_guard_reminded';
const MORETTI_GUARD_META_ALERTED        = '_moretti_guard_alerted_';

/* -------------------------------------------------------------------------------------------------------------
 * Pomocnicze
 * ---------------------------------------------------------------------------------------------------------- */

function moretti_guard_available(): bool {
    return class_exists(\WC_P24\Models\Transaction::class)
        && class_exists(\WC_P24\API\Resources\Transaction_Resource::class)
        && method_exists(\WC_P24\Models\Transaction::class, 'reconcile_pending_payment');
}

/** Zamówienie P24, dla którego wtyczka zarejestrowała transakcję (jest ID sesji, po którym pytamy P24). */
function moretti_guard_is_p24_order($order): bool {
    return $order instanceof WC_Order
        && str_starts_with((string) $order->get_payment_method(), 'p24')
        && (string) $order->get_meta('_p24_session_id') !== '';
}

function moretti_guard_order_age(WC_Order $order): int {
    $created = $order->get_date_created();
    return $created ? time() - $created->getTimestamp() : 0;
}

function moretti_guard_is_bank_transfer(WC_Order $order): bool {
    $method = (int) $order->get_meta('_p24_method');
    if (!$method || !class_exists(\WC_P24\Utilities\Payment_Methods::class)) {
        return false;
    }
    $groups = defined('MORETTI_P24_NO_WAIT_GROUPS') ? MORETTI_P24_NO_WAIT_GROUPS : ['FastTransfers', 'eTransfer'];
    return in_array(\WC_P24\Utilities\Payment_Methods::get_group_name($method), $groups, true);
}

function moretti_guard_note(WC_Order $order, string $text): void {
    $order->add_order_note('Strażnik płatności: ' . $text);
}

function moretti_guard_warsaw_time(int $timestamp): string {
    return wp_date('j.m.Y, H:i', $timestamp, new DateTimeZone('Europe/Warsaw'));
}

/**
 * Status transakcji wprost z API P24 (transaction/by/sessionId). Status 2 = opłacona.
 *
 * @return array|null Dane transakcji albo null, gdy API nie odpowiedziało sensownie.
 */
function moretti_guard_p24_transaction(WC_Order $order): ?array {
    try {
        $response = (new \WC_P24\API\Resources\Transaction_Resource())
            ->get_transaction_by_session_id((string) $order->get_meta('_p24_session_id'));
    } catch (\Throwable $e) {
        return null;
    }
    return isset($response['data']) && is_array($response['data']) ? $response['data'] : null;
}

/**
 * Pyta P24 i potwierdza zamówienie, jeśli pieniądze dotarły.
 *
 * @return bool true, gdy zamówienie jest (już albo teraz) opłacone.
 */
function moretti_guard_reconcile(WC_Order $order, string $reason): bool {
    if ($order->is_paid()) {
        return true;
    }
    if (!moretti_guard_available() || !moretti_guard_is_p24_order($order)) {
        return false;
    }

    $was_status = $order->get_status();

    try {
        $paid = (new \WC_P24\Models\Transaction($order->get_id()))->reconcile_pending_payment();
    } catch (\Throwable $e) {
        $paid = false;
    }

    $order = wc_get_order($order->get_id());

    if ($paid && $order && $order->is_paid()) {
        moretti_guard_note($order, sprintf(
            'płatność potwierdzona bezpośrednio w P24 (%s). Powiadomienie od P24 nie dotarło do sklepu na czas.',
            $reason
        ));
        if ($was_status === 'cancelled') {
            return true; // jeden alert o tym zdarzeniu wysyła moretti_guard_paid_after_cancel()
        }
        moretti_guard_alert($order, 'missed', 'Płatność potwierdzona dopiero przez strażnika', [
            sprintf('Zamówienie nr %s było „%s”, a P24 pokazywało je jako opłacone.', $order->get_order_number(), wc_get_order_status_name($was_status)),
            'Strażnik potwierdził je samodzielnie — klient dostał zwykły mail o przyjęciu do realizacji.',
            'Powiadomienie od P24 nie dotarło do sklepu. Jeśli to się powtarza, sprawdź hosting (dostępność, limity) i logi.',
        ]);
        return true;
    }

    // Wtyczka odmówiła potwierdzenia — sprawdzamy, czy P24 mimo to widzi pieniądze (wtedy potrzebna jest ręka).
    $transaction = moretti_guard_p24_transaction($order);
    if ($transaction && (int) ($transaction['status'] ?? 0) === 2) {
        moretti_guard_note($order, 'P24 pokazuje transakcję jako opłaconą, ale automatyczne potwierdzenie nie przeszło — wymaga sprawdzenia.');
        moretti_guard_alert($order, 'mismatch', 'P24 widzi opłacone, sklep nie — sprawdź ręcznie', [
            sprintf('Zamówienie nr %s (%s) ma w sklepie status „%s”.', $order->get_order_number(), wp_strip_all_tags(wc_price($order->get_total())), wc_get_order_status_name($order->get_status())),
            sprintf('P24: transakcja %s opłacona, kwota %s gr, metoda %s.', $transaction['orderId'] ?? '?', $transaction['amount'] ?? '?', $transaction['paymentMethod'] ?? '?'),
            'Sprawdź transakcję w panelu P24 i ustaw zamówienie ręcznie na „W trakcie realizacji”, jeśli kwota się zgadza.',
        ]);
    }

    return false;
}

/* -------------------------------------------------------------------------------------------------------------
 * 1. Cykliczny przegląd
 * ---------------------------------------------------------------------------------------------------------- */

function moretti_guard_schedule(): void {
    if (!function_exists('as_has_scheduled_action') || !function_exists('as_schedule_recurring_action')) {
        return;
    }
    if (!as_has_scheduled_action(MORETTI_GUARD_SWEEP_HOOK, [], MORETTI_GUARD_GROUP)) {
        as_schedule_recurring_action(time() + MINUTE_IN_SECONDS, MORETTI_GUARD_SWEEP_INTERVAL, MORETTI_GUARD_SWEEP_HOOK, [], MORETTI_GUARD_GROUP);
    }
}
add_action('init', 'moretti_guard_schedule', 20);

function moretti_guard_sweep(): void {
    if (!moretti_guard_available()) {
        return;
    }

    $orders = wc_get_orders([
        'limit'        => 200,
        'status'       => ['pending', 'cancelled'],
        'date_created' => '>' . (time() - MORETTI_GUARD_LOOKBACK),
        'orderby'      => 'date',
        'order'        => 'ASC',
    ]);

    foreach ($orders as $order) {
        if (!moretti_guard_is_p24_order($order) || moretti_guard_order_age($order) < MORETTI_GUARD_MIN_AGE) {
            continue;
        }

        if ($order->has_status('cancelled')) {
            // Tylko zamówienia anulowane przez limit czasu — ręcznych anulowań nie wskrzeszamy.
            if ($order->get_meta(MORETTI_GUARD_META_AUTO_CANCELLED)) {
                moretti_guard_reconcile($order, 'przegląd zamówień anulowanych przez limit czasu');
            }
            continue;
        }

        if (moretti_guard_reconcile($order, 'cykliczny przegląd')) {
            continue;
        }

        moretti_guard_maybe_remind($order);
    }
}
add_action(MORETTI_GUARD_SWEEP_HOOK, 'moretti_guard_sweep');

/* -------------------------------------------------------------------------------------------------------------
 * 2. Automatyczne anulowanie
 * ---------------------------------------------------------------------------------------------------------- */

function moretti_guard_before_cancel($should_cancel, $order) {
    if (!$should_cancel || !moretti_guard_is_p24_order($order)) {
        return $should_cancel;
    }

    if (moretti_guard_reconcile($order, 'sprawdzenie przed automatycznym anulowaniem')) {
        return false;
    }

    $age = moretti_guard_order_age($order);

    if (in_array((int) $order->get_meta('_p24_method'), MORETTI_GUARD_SLOW_METHODS, true) && $age < MORETTI_GUARD_SLOW_GRACE) {
        return false;
    }

    $reminded = (int) $order->get_meta(MORETTI_GUARD_META_REMINDED);
    if ($reminded && time() - $reminded < MORETTI_GUARD_REMINDED_GRACE) {
        return false;
    }

    $order->update_meta_data(MORETTI_GUARD_META_AUTO_CANCELLED, time());
    $order->save_meta_data();

    return true;
}
add_filter('woocommerce_cancel_unpaid_order', 'moretti_guard_before_cancel', 10, 2);

/* -------------------------------------------------------------------------------------------------------------
 * 3. Przypomnienie dla klienta
 * ---------------------------------------------------------------------------------------------------------- */

function moretti_guard_maybe_remind(WC_Order $order): void {
    if (!$order->has_status('pending') || $order->get_meta(MORETTI_GUARD_META_REMINDED) || !$order->get_billing_email()) {
        return;
    }

    // Przekaz tradycyjny klient wysyła ręcznie i idzie on dniami — przypomnienie namawiałoby do drugiego przelewu.
    if (in_array((int) $order->get_meta('_p24_method'), MORETTI_GUARD_SLOW_METHODS, true)) {
        return;
    }

    $wait = moretti_guard_is_bank_transfer($order) ? MORETTI_GUARD_REMIND_AFTER_BANK : MORETTI_GUARD_REMIND_AFTER;
    if (moretti_guard_order_age($order) < $wait) {
        return;
    }

    // Klient, który po nieudanej płatności złożył nowe zamówienie, nie dostaje przypomnienia o starym.
    $newer = wc_get_orders([
        'limit'         => 1,
        'billing_email' => $order->get_billing_email(),
        'date_created'  => '>' . $order->get_date_created()->getTimestamp(),
        'status'        => ['pending', 'processing', 'completed', 'on-hold'],
        'exclude'       => [$order->get_id()],
        'return'        => 'ids',
    ]);
    if ($newer) {
        $order->update_meta_data(MORETTI_GUARD_META_REMINDED, 'skipped-newer-order');
        $order->save_meta_data();
        moretti_guard_note($order, sprintf('bez przypomnienia — klient złożył później zamówienie nr %s.', $newer[0]));
        return;
    }

    if (moretti_guard_send_reminder($order)) {
        $order->update_meta_data(MORETTI_GUARD_META_REMINDED, time());
        $order->save_meta_data();
        moretti_guard_note($order, 'wysłano klientowi przypomnienie z linkiem do dokończenia płatności. Zamówienie czeka 24 h.');
    }
}

function moretti_guard_send_reminder(WC_Order $order): bool {
    $mailer = WC()->mailer();
    $expires  = time() + MORETTI_GUARD_REMINDED_GRACE;
    $deadline = moretti_guard_warsaw_time($expires);
    $pay_url  = moretti_guard_signed_pay_url($order, $expires);

    $items = '';
    foreach ($order->get_items() as $item) {
        $items .= sprintf('<li>%s × %d</li>', esc_html($item->get_name()), $item->get_quantity());
    }

    $body = sprintf(
        '<p>Dzień dobry%s,</p>'
        . '<p>Twoje zamówienie nr <strong>%s</strong> w sklepie Moretti zostało zapisane, ale nie mamy jeszcze potwierdzenia płatności.</p>'
        . '<ul>%s</ul>'
        . '<p>Do zapłaty: <strong>%s</strong></p>'
        . '<p><a href="%s" style="display:inline-block;padding:12px 22px;background:#1a1a1a;color:#ffffff;text-decoration:none;border-radius:4px;font-weight:600">Dokończ płatność</a></p>'
        . '<p>Zamówienie czeka na płatność do %s. Jeśli płatność się nie udała, możesz wybrać w tym linku inną metodę, np. BLIK.</p>'
        . '<p>Jeśli właśnie zapłaciłeś, zignoruj tę wiadomość — gdy bank potwierdzi przelew, dostaniesz osobny mail z potwierdzeniem. Prosimy nie płacić drugi raz.</p>'
        . '<p>W razie pytań po prostu odpowiedz na tę wiadomość.</p>',
        $order->get_billing_first_name() ? ' ' . esc_html($order->get_billing_first_name()) : '',
        esc_html($order->get_order_number()),
        $items,
        wp_kses_post($order->get_formatted_order_total()),
        esc_url($pay_url),
        esc_html($deadline)
    );

    $subject = sprintf('Zamówienie nr %s czeka na płatność', $order->get_order_number());
    $html    = $mailer->wrap_message('Dokończ płatność', $body);

    return (bool) $mailer->send($order->get_billing_email(), $subject, $html);
}

/**
 * Link „Dokończ płatność” z podpisem ważnym do terminu z przypomnienia.
 *
 * Gość, który wraca do płatności po ponad 10 min od zamówienia, musiałby u WooCommerce najpierw wpisać swój email
 * (ochrona na wypadek wycieku linku z kluczem zamówienia). Przypomnienie idzie właśnie na ten email, więc podpisany
 * link pomija ten krok — tylko na stronie płatności, tylko dla tego zamówienia i tylko do terminu.
 */
function moretti_guard_signed_pay_url(WC_Order $order, int $expires): string {
    return add_query_arg('moretti_pay', $expires . '.' . moretti_guard_pay_signature($order, $expires), $order->get_checkout_payment_url());
}

function moretti_guard_pay_signature(WC_Order $order, int $expires): string {
    return substr(hash_hmac('sha256', $order->get_id() . '|' . $order->get_order_key() . '|' . $expires, wp_salt('auth')), 0, 32);
}

function moretti_guard_skip_email_verification($grace, $order, $context) {
    if ($context !== 'order-pay' || empty($_GET['moretti_pay']) || !$order instanceof WC_Order) {
        return $grace;
    }

    $parts = explode('.', sanitize_text_field(wp_unslash($_GET['moretti_pay'])), 2);
    if (count($parts) !== 2 || (int) $parts[0] < time()) {
        return $grace;
    }

    return hash_equals(moretti_guard_pay_signature($order, (int) $parts[0]), $parts[1]) ? PHP_INT_MAX : $grace;
}
add_filter('woocommerce_order_email_verification_grace_period', 'moretti_guard_skip_email_verification', 10, 3);

/* -------------------------------------------------------------------------------------------------------------
 * 4. Alerty dla właściciela
 * ---------------------------------------------------------------------------------------------------------- */

/** Adres alertów ustawiony w kodzie, a nie w panelu — nie zmieni się przypadkiem przy edycji ustawień maili sklepu. */
const MORETTI_GUARD_ALERT_TO = 'portfelland@gmail.com';

function moretti_guard_alert_recipient(): string {
    return (string) apply_filters('moretti_payment_guard_alert_recipient', MORETTI_GUARD_ALERT_TO);
}

/** Jeden alert danego typu na zamówienie. */
function moretti_guard_alert(WC_Order $order, string $type, string $title, array $lines): void {
    $key = MORETTI_GUARD_META_ALERTED . $type;
    if ($order->get_meta($key)) {
        return;
    }

    $lines[] = sprintf('<a href="%s">Otwórz zamówienie nr %s w panelu</a>', esc_url($order->get_edit_order_url()), esc_html($order->get_order_number()));
    $body    = '<p>' . implode('</p><p>', $lines) . '</p>';
    $mailer  = WC()->mailer();
    $sent    = $mailer->send(
        moretti_guard_alert_recipient(),
        sprintf('[Moretti] %s — zamówienie nr %s', $title, $order->get_order_number()),
        $mailer->wrap_message($title, $body)
    );

    $order->update_meta_data($key, time());
    $order->save_meta_data();
    moretti_guard_note($order, sprintf('alert do właściciela: %s%s.', $title, $sent ? '' : ' (wysyłka maila NIE powiodła się)'));
}

/** Pieniądze przyszły do zamówienia, które było już anulowane — towar mógł się w międzyczasie sprzedać. */
function moretti_guard_paid_after_cancel($order_id, $order = null): void {
    $order = $order instanceof WC_Order ? $order : wc_get_order($order_id);
    if (!moretti_guard_is_p24_order($order)) {
        return;
    }

    $lines = [
        sprintf('Zamówienie nr %s (%s) zostało anulowane, a potem P24 potwierdziło płatność. Zamówienie wróciło do realizacji.', $order->get_order_number(), wp_strip_all_tags(wc_price($order->get_total()))),
    ];
    $problem = false;
    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        if ($product && $product->managing_stock()) {
            $stock = (int) $product->get_stock_quantity();
            $problem = $problem || $stock < 0;
            $lines[] = sprintf('%s — stan po tej sprzedaży: %d%s', esc_html($item->get_name()), $stock, $stock < 0 ? ' (UJEMNY — towaru zabrakło)' : '');
        }
    }
    $lines[] = $problem
        ? 'Czegoś zabrakło: skontaktuj się z klientem (wymiana albo zwrot pieniędzy).'
        : 'Stan magazynu wystarcza — wystarczy normalnie wysłać zamówienie.';

    moretti_guard_alert($order, 'paid_after_cancel', 'Płatność za anulowane zamówienie', $lines);
}
add_action('woocommerce_order_status_cancelled_to_processing', 'moretti_guard_paid_after_cancel', 20, 2);
add_action('woocommerce_order_status_cancelled_to_completed', 'moretti_guard_paid_after_cancel', 20, 2);

/** Ten sam klient zapłacił dwa razy tę samą kwotę w krótkim czasie — prawdopodobnie podwójne zamówienie. */
function moretti_guard_double_payment($order_id): void {
    $order = wc_get_order($order_id);
    if (!$order instanceof WC_Order || !$order->get_billing_email()) {
        return;
    }

    $paid_at = $order->get_date_paid() ? $order->get_date_paid()->getTimestamp() : time();
    $others  = wc_get_orders([
        'limit'         => 5,
        'billing_email' => $order->get_billing_email(),
        'status'        => ['processing', 'completed', 'on-hold'],
        'date_paid'     => ($paid_at - MORETTI_GUARD_DOUBLE_WINDOW) . '...' . ($paid_at + MORETTI_GUARD_DOUBLE_WINDOW),
        'exclude'       => [$order->get_id()],
    ]);

    foreach ($others as $other) {
        if (abs((float) $other->get_total() - (float) $order->get_total()) < 0.01) {
            moretti_guard_alert($order, 'double', 'Możliwa podwójna płatność', [
                sprintf('Klient %s opłacił zamówienia nr %s i nr %s na tę samą kwotę %s w odstępie mniej niż 2 h.', esc_html($order->get_billing_email()), $other->get_order_number(), $order->get_order_number(), wp_strip_all_tags(wc_price($order->get_total()))),
                'Sprawdź, czy to świadomy drugi zakup. Jeśli nie — anuluj jedno i zrób zwrot w panelu P24.',
            ]);
            return;
        }
    }
}
add_action('woocommerce_payment_complete', 'moretti_guard_double_payment', 20);
