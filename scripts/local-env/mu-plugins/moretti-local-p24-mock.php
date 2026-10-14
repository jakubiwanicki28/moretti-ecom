<?php
/**
 * Plugin Name: Moretti — lokalna atrapa Przelewy24
 * Description: TYLKO środowisko lokalne. Bez kluczy sprzedawcy wtyczka P24 ukrywa metody płatności i nie zarejestruje
 *              transakcji, więc: (1) lista metod pochodzi ze zrzutu produkcji (fixtures/p24-payment-methods.json),
 *              (2) rejestracja transakcji zwraca token, (3) zamiast bramki P24 jest lokalny symulator
 *              (/?moretti-p24-sim=TOKEN) z przyciskami „Zapłać” / „Anuluj”, który kończy zamówienie jak prawdziwa płatność.
 */

if (!defined('ABSPATH') || wp_get_environment_type() !== 'local') {
    return;
}

const MORETTI_P24_SIM_PARAM = 'moretti-p24-sim';

add_filter('pre_http_request', function ($pre, $args, $url) {
    $host = (string) wp_parse_url($url, PHP_URL_HOST);
    if (!preg_match('/(^|\.)przelewy24\.pl$/', $host) || str_starts_with($host, 'static.')) {
        return $pre;
    }

    $path = (string) wp_parse_url($url, PHP_URL_PATH);

    if (str_contains($path, '/api/v1/payment/methods/')) {
        $body = file_get_contents(WP_CONTENT_DIR . '/themes/moretti-ecom/scripts/local-env/fixtures/p24-payment-methods.json');
    } elseif (str_contains($path, '/api/v1/transaction/register')) {
        // Opis i kwotę zapamiętujemy, żeby symulator pokazał to, co dostałby prawdziwy P24.
        $token = 'LOCAL-' . wp_generate_password(12, false);
        set_transient('moretti_p24_sim_' . $token, json_decode($args['body'] ?? '{}', true), DAY_IN_SECONDS);
        $body = wp_json_encode(['data' => ['token' => $token], 'responseCode' => 0]);
    } else {
        $body = wp_json_encode(['error' => 'Lokalna atrapa P24 — ta operacja nie jest obsługiwana', 'code' => 400]);
    }

    return [
        'headers'  => ['content-type' => 'application/json'],
        'body'     => $body,
        'response' => ['code' => 200, 'message' => 'OK'],
        'cookies'  => [],
        'filename' => null,
    ];
}, 10, 3);

// Przekierowanie do bramki P24 → lokalny symulator.
add_action('woocommerce_rest_checkout_process_payment_with_context', function ($context, $result) {
    if (preg_match('~przelewy24\.pl/trnRequest/(LOCAL-[A-Za-z0-9]+)~', (string) $result->redirect_url, $m)) {
        $result->set_redirect_url(add_query_arg(MORETTI_P24_SIM_PARAM, $m[1], home_url('/')));
    }
}, 100, 2);

add_action('template_redirect', function () {
    if (empty($_GET[MORETTI_P24_SIM_PARAM])) {
        return;
    }

    $token = sanitize_text_field(wp_unslash($_GET[MORETTI_P24_SIM_PARAM]));
    $data  = get_transient('moretti_p24_sim_' . $token) ?: [];
    $order = current(wc_get_orders(['limit' => 1, 'meta_key' => '_p24_token', 'meta_value' => $token, 'status' => array_keys(wc_get_order_statuses())]));

    if (!$order) {
        wp_die('Lokalny symulator P24: nie znaleziono zamówienia dla tokenu ' . esc_html($token));
    }

    if (isset($_POST['moretti_p24_action']) && check_admin_referer('moretti_p24_sim_' . $token)) {
        if ($_POST['moretti_p24_action'] === 'pay') {
            $order->payment_complete($token);
            $order->add_order_note('Lokalny symulator P24: płatność zaakceptowana.');
            wp_safe_redirect($order->get_checkout_order_received_url());
        } else {
            $order->add_order_note('Lokalny symulator P24: klient anulował płatność.');
            wp_safe_redirect(wc_get_checkout_url());
        }
        exit;
    }

    $amount = isset($data['amount']) ? wc_price($data['amount'] / 100) : $order->get_formatted_order_total();
    ?>
    <!doctype html>
    <html lang="pl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Symulator Przelewy24 (lokalnie)</title>
    <style>
        body{margin:0;font:15px/1.5 -apple-system,BlinkMacSystemFont,sans-serif;background:#f4f4f5;color:#222;display:grid;place-items:center;min-height:100vh}
        .box{background:#fff;max-width:420px;width:calc(100% - 32px);padding:28px;border-radius:8px;box-shadow:0 2px 12px rgba(0,0,0,.08)}
        .tag{display:inline-block;background:#fde68a;color:#78350f;font-size:12px;font-weight:600;padding:2px 8px;border-radius:4px}
        h1{font-size:18px;margin:14px 0 4px} .amount{font-size:30px;font-weight:700;margin:8px 0 16px}
        dl{display:grid;grid-template-columns:auto 1fr;gap:4px 12px;font-size:13px;color:#555;margin:0 0 20px} dt{color:#999}
        button{width:100%;padding:14px;border:0;border-radius:6px;font-size:15px;font-weight:600;cursor:pointer;margin-top:8px}
        .pay{background:#d13239;color:#fff} .cancel{background:#fff;border:1px solid #ccc;color:#333}
    </style></head><body><div class="box">
        <span class="tag">SYMULATOR — tylko lokalnie, bez prawdziwej płatności</span>
        <h1>Przelewy24</h1>
        <div class="amount"><?php echo wp_kses_post($amount); ?></div>
        <dl>
            <dt>Opis</dt><dd><?php echo esc_html($data['description'] ?? '—'); ?></dd>
            <dt>Zamówienie</dt><dd>#<?php echo esc_html($order->get_order_number()); ?></dd>
            <dt>Metoda</dt><dd><?php echo esc_html($order->get_payment_method_title()); ?></dd>
            <dt>Zgoda P24</dt><dd><?php echo !empty($data['regulationAccept']) ? 'tak' : 'nie'; ?></dd>
        </dl>
        <form method="post">
            <?php wp_nonce_field('moretti_p24_sim_' . $token); ?>
            <button class="pay" name="moretti_p24_action" value="pay">Zapłać</button>
            <button class="cancel" name="moretti_p24_action" value="cancel">Anuluj płatność</button>
        </form>
    </div></body></html>
    <?php
    exit;
});
