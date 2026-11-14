<?php
/**
 * Fakty o sklepie w jednym miejscu: próg darmowej dostawy, czas na zwrot, zasady wysyłki.
 *
 * Każdy tekst na stronie (karta produktu, strona główna, FAQ, meta, strony prawne) i każda reguła w kodzie
 * (darmowa dostawa, data dostawy) czyta stąd — żeby sklep nigdy więcej nie obiecywał w jednym miejscu
 * czegoś innego niż w drugim (audyt 2026-11-12: zwrot 30 dni vs 14 dni, darmowa dostawa bez reguły).
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

/**
 * Darmowa dostawa (każda metoda) od tej wartości produktów w koszyku, w zł brutto po rabatach.
 * Zmiana progu = też § 5 Regulaminu (content/legal-pages/regulamin-sklepu.html) — tam kwota jest wpisana na stałe,
 * bo dokument prawny nie może się zmieniać sam.
 */
const MORETTI_FREE_SHIPPING_FROM = 250;

/** Ustawowe 14 dni na odstąpienie od umowy — tak samo w regulaminie, na /zwroty/ i na karcie produktu. */
const MORETTI_RETURN_DAYS = 14;

/** „250 zł” — kwota bez groszy, gdy są zerowe. */
function moretti_money_label($amount): string {
    $amount = (float) $amount;
    $decimals = abs($amount - round($amount)) < 0.005 ? 0 : 2;
    return number_format($amount, $decimals, ',', ' ') . ' zł';
}

function moretti_free_shipping_from_label(): string {
    return moretti_money_label(MORETTI_FREE_SHIPPING_FROM);
}

/**
 * Metody dostawy ze strefy „Polska” — dokładnie to, co klient zobaczy w kasie.
 *
 * @return array<int, array{title: string, cost: float}>
 */
function moretti_shipping_methods(): array {
    if (!class_exists('WC_Shipping_Zones')) {
        return [];
    }
    $methods = [];
    foreach (WC_Shipping_Zones::get_zones() as $zone) {
        if (($zone['zone_name'] ?? '') !== 'Polska') {
            continue;
        }
        foreach ($zone['shipping_methods'] as $method) {
            if ($method->enabled !== 'yes') {
                continue;
            }
            $methods[] = [
                'title' => $method->get_title(),
                'cost'  => (float) str_replace(',', '.', (string) $method->get_option('cost', '0')),
                'order' => (int) $method->method_order,
            ];
        }
    }
    usort($methods, fn($a, $b) => $a['order'] <=> $b['order']);
    return $methods;
}

/** Cena metody dostawy po nazwie (FAQ na stronie głównej). */
function moretti_shipping_cost_label(string $title_contains): string {
    foreach (moretti_shipping_methods() as $method) {
        if (stripos($method['title'], $title_contains) !== false) {
            return moretti_money_label($method['cost']);
        }
    }
    return '';
}

/** [moretti_shipping_table] — tabela dostaw na stronie „Dostawa i płatności”, zawsze zgodna z kasą. */
function moretti_shipping_table_shortcode(): string {
    $methods = moretti_shipping_methods();
    if (!$methods) {
        return '';
    }
    $cell = 'border:1px solid #ccc;padding:8px 12px;text-align:left;vertical-align:top;';
    $head = $cell . 'background:#f5f5f5;font-weight:700;font-family:Arial,sans-serif;';
    $rows = '';
    foreach ($methods as $method) {
        $rows .= sprintf(
            '<tr><td style="%1$s">%2$s</td><td style="%1$s">%3$s</td><td style="%1$s">zwykle 1 dzień roboczy</td></tr>',
            $cell . 'font-family:Georgia,serif;',
            esc_html($method['title']),
            esc_html(moretti_money_label($method['cost']))
        );
    }
    return sprintf(
        '<table style="border-collapse:collapse;width:100%%;margin:1rem 0;font-size:0.88rem;font-family:Georgia,serif;"><thead><tr><th style="%1$s">Metoda dostawy</th><th style="%1$s">Koszt</th><th style="%1$s">Czas dostawy po wysyłce</th></tr></thead><tbody>%2$s</tbody></table>'
        . '<p style="font-size:0.95rem;font-family:Georgia,serif;color:#1a1a1a;line-height:1.75;"><strong>Darmowa dostawa każdą metodą</strong>, gdy wartość produktów w zamówieniu (po rabatach) wynosi co najmniej %3$s.</p>',
        $head,
        $rows,
        esc_html(moretti_free_shipping_from_label())
    );
}
add_shortcode('moretti_shipping_table', 'moretti_shipping_table_shortcode');

/** Stary adres /koszty-dostawy/ (dawne linki w stopce i wyszukiwarkach) → /dostawa-i-platnosci/. */
add_action('template_redirect', function () {
    if (is_page('koszty-dostawy')) {
        wp_safe_redirect(home_url('/dostawa-i-platnosci/'), 301);
        exit;
    }
});

/* ---- Wysyłka: następny dzień roboczy ------------------------------------------------------------------------ */

/** Polskie dni ustawowo wolne w danym roku (Y-m-d). Wigilia wolna od 2025 r. */
function moretti_pl_holidays(int $year): array {
    // Wielkanoc — algorytm Meeusa/Jonesa/Butchera (bez rozszerzenia calendar).
    $a = $year % 19; $b = intdiv($year, 100); $c = $year % 100; $d = intdiv($b, 4); $e = $b % 4;
    $f = intdiv($b + 8, 25); $g = intdiv($b - $f + 1, 3); $h = (19 * $a + $b - $d - $g + 15) % 30;
    $i = intdiv($c, 4); $k = $c % 4; $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7; $m = intdiv($a + 11 * $h + 22 * $l, 451);
    $month = intdiv($h + $l - 7 * $m + 114, 31); $day = (($h + $l - 7 * $m + 114) % 31) + 1;
    $easter = new DateTimeImmutable(sprintf('%04d-%02d-%02d', $year, $month, $day));

    $days = ["$year-01-01", "$year-01-06", "$year-05-01", "$year-05-03", "$year-08-15", "$year-11-01", "$year-11-11", "$year-12-25", "$year-12-26"];
    if ($year >= 2025) {
        $days[] = "$year-12-24";
    }
    foreach ([0, 1, 49, 60] as $offset) { // Wielkanoc, Poniedziałek Wielkanocny, Zielone Świątki, Boże Ciało
        $days[] = $easter->modify("+$offset days")->format('Y-m-d');
    }
    sort($days);
    return $days;
}

function moretti_is_business_day(DateTimeImmutable $date): bool {
    return (int) $date->format('N') <= 5 && !in_array($date->format('Y-m-d'), moretti_pl_holidays((int) $date->format('Y')), true);
}

function moretti_next_business_day(DateTimeImmutable $date): DateTimeImmutable {
    do {
        $date = $date->modify('+1 day');
    } while (!moretti_is_business_day($date));
    return $date;
}

/**
 * Wysyłka następnego dnia roboczego po zamówieniu, dostawa dzień roboczy po wysyłce. Czas polski.
 *
 * @return array{ship: DateTimeImmutable, delivery: DateTimeImmutable}
 */
function moretti_shipping_dates(?DateTimeImmutable $now = null): array {
    $now  = ($now ?? new DateTimeImmutable('now'))->setTimezone(new DateTimeZone('Europe/Warsaw'))->setTime(0, 0);
    $ship = moretti_next_business_day($now);
    return ['ship' => $ship, 'delivery' => moretti_next_business_day($ship)];
}

/** „we wtorek 17.11” */
function moretti_weekday_label(DateTimeImmutable $date): string {
    $names = [1 => 'w poniedziałek', 'we wtorek', 'w środę', 'w czwartek', 'w piątek', 'w sobotę', 'w niedzielę'];
    return $names[(int) $date->format('N')] . ' ' . $date->format('d.m');
}

/**
 * Linia „Zamów teraz: wysyłka …, u Ciebie …” na karcie produktu.
 *
 * Strona produktu siedzi w cache LiteSpeed (dni), więc daty wyliczone na serwerze by się „zestarzały”.
 * Serwer wypisuje wersję startową (dla wyszukiwarek i bez JS), a przeglądarka liczy daty od nowa z listą świąt.
 */
function moretti_delivery_estimate_html(): string {
    $dates = moretti_shipping_dates();
    $year  = (int) $dates['ship']->format('Y');
    $holidays = array_merge(moretti_pl_holidays($year - 1), moretti_pl_holidays($year), moretti_pl_holidays($year + 1));

    return sprintf(
        '<span class="moretti-delivery-estimate" data-holidays="%s">Zamów teraz: wysyłka %s, u Ciebie %s</span>',
        esc_attr(implode(',', $holidays)),
        esc_html(moretti_weekday_label($dates['ship'])),
        esc_html(moretti_weekday_label($dates['delivery']))
    );
}

function moretti_delivery_estimate_script(): void {
    if (!is_product()) {
        return;
    }
    ?>
    <script>
    (function () {
        var el = document.querySelector('.moretti-delivery-estimate');
        if (!el || !window.Intl) { return; }
        var holidays = (el.getAttribute('data-holidays') || '').split(',');
        var names = ['w niedzielę', 'w poniedziałek', 'we wtorek', 'w środę', 'w czwartek', 'w piątek', 'w sobotę'];
        // Dzisiejsza data w Polsce, niezależnie od strefy czasowej telefonu.
        var parts = new Intl.DateTimeFormat('en-CA', { timeZone: 'Europe/Warsaw', year: 'numeric', month: '2-digit', day: '2-digit' }).format(new Date()).split('-');
        var day = new Date(Date.UTC(+parts[0], +parts[1] - 1, +parts[2]));
        function iso(d) { return d.toISOString().slice(0, 10); }
        function next(d) {
            do { d = new Date(d.getTime() + 864e5); } while (d.getUTCDay() === 0 || d.getUTCDay() === 6 || holidays.indexOf(iso(d)) !== -1);
            return d;
        }
        function label(d) { return names[d.getUTCDay()] + ' ' + iso(d).slice(8, 10) + '.' + iso(d).slice(5, 7); }
        var ship = next(day), delivery = next(ship);
        el.textContent = 'Zamów teraz: wysyłka ' + label(ship) + ', u Ciebie ' + label(delivery);
    })();
    </script>
    <?php
}
add_action('wp_footer', 'moretti_delivery_estimate_script', 30);
