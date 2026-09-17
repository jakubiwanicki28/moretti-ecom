<?php
/**
 * Warianty linku "Pielęgnacja" na karcie produktu.
 *
 * Warianty definiowane są wyłącznie w kodzie (tablica poniżej). Każdy wariant
 * ma etykietę linku oraz stronę WP (tworzoną automatycznie, jeśli nie istnieje).
 * W edycji produktu (zakładka "Ogólne") można wybrać wariant lub wyłączyć link.
 * Brak wyboru = wariant domyślny (portfel), co obejmuje też istniejące produkty.
 *
 * Aby dodać lub zmienić wariant: edytuj moretti_care_variants(). Strona zostanie
 * utworzona / zaktualizowana przy następnym wejściu do panelu admina (treść stron
 * wariantów z niepustym page_content jest źródłem prawdy w kodzie, nie w edytorze WP).
 */

if (!defined('ABSPATH')) {
    exit;
}

const MORETTI_CARE_META_KEY = '_moretti_care_variant';
const MORETTI_CARE_DEFAULT = 'portfel';
const MORETTI_CARE_NONE = 'none';

function moretti_care_variants() {
    return array(
        'portfel' => array(
            'name'       => 'Portfel',
            'link_label' => 'Pielęgnacja portfela ze skóry naturalnej',
            'page_slug'  => 'pielegnacja-portfela',
            'page_title' => 'Pielęgnacja portfela',
            // Treść strony portfela jest już seedowana w moretti_ensure_legal_pages_exist().
            'page_content' => '',
        ),
        'torebka' => array(
            'name'       => 'Torebka',
            'link_label' => 'Pielęgnacja torebki z ekoskóry',
            'page_slug'  => 'pielegnacja-torebki',
            'page_title' => 'Pielęgnacja torebki',
            'page_content' => '
            <div class="moretti-legal-doc">
                <h1>Jak dbać o torebkę z ekoskóry?</h1>
                <p>Torebka ze skóry ekologicznej to dodatek, który przy odpowiedniej pielęgnacji może zachować świetny wygląd przez lata. Eko skórka jest trwała, ale wymaga regularnej troski: delikatnego czyszczenia, ochrony przed wilgocią oraz właściwego przechowywania.</p>
                <p>Poniżej znajdziesz praktyczne zasady, które pomogą utrzymać torebkę w bardzo dobrej kondycji na co dzień.</p>

                <h2>1. Codzienne użytkowanie</h2>
                <p>Skóra ekologiczna jest odporna, ale nie jest niezniszczalna. Najczęściej uszkodzenia powstają przez kontakt z twardymi przedmiotami (klucze, metalowe elementy) oraz nadmierne wypychanie torebki.</p>
                <ul>
                    <li>noś torebkę oddzielnie od ostrych przedmiotów,</li>
                    <li>nie przeładowuj przegród – nadmierne obciążenie może zdeformować produkt oraz zniszczyć trwale.</li>
                </ul>

                <h2>2. Usuwanie zabrudzeń</h2>
                <p>Do bieżącego czyszczenia zawsze używaj miękkiej, lekko wilgotnej ściereczki. Silne środki domowe mogą naruszyć strukturę ekoskóry i pozostawić plamy.</p>
                <ul>
                    <li>najpierw usuń kurz i drobny brud suchą mikrofibrą,</li>
                    <li>przy trudniejszych zabrudzeniach zastosuj preparat przeznaczony do konkretnego rodzaju ekoskóry,</li>
                    <li>unikaj mocnego tarcia oraz nadmiaru wody.</li>
                </ul>

                <h2>3. Konserwacja</h2>
                <p>Po oczyszczeniu warto wykonać konserwację, która zabezpieczy powierzchnię i ograniczy przesuszanie materiału.</p>
                <ul>
                    <li>stosuj dedykowane produkty do wyrobów z ekoskóry,</li>
                    <li>nakładaj małą ilość preparatu i postępuj zgodnie z instrukcją preparatu,</li>
                    <li>impregnację wykonuj regularnie, najlepiej raz na kilka miesięcy.</li>
                </ul>

                <h2>4. Pielęgnacja zależnie od typu wykończenia</h2>
                <p>Różne rodzaje ekoskór wymagają nieco innego podejścia.</p>
                <ul>
                    <li><strong>Ekoskóra lakierowana:</strong> czyść delikatnie i używaj środków do skór lakierowanych; zwykle nie wymaga intensywnego natłuszczania.</li>
                    <li><strong>Ekoskóra fakturowana:</strong> zazwyczaj wystarczy przetarcie wilgotną mikrofibrą i okresowe odświeżenie odpowiednim preparatem.</li>
                    <li><strong>Ekoskóra licowa:</strong> dobrze reaguje na regularne czyszczenie i lekką impregnację.</li>
                    <li><strong>Skóry o tłoczonej lub strukturze z drobnym włosem jak ekoskóra typ zamsz:</strong> pielęgnuj preparatami dedykowanymi do tego typu materiału, bez agresywnego szorowania czy szczotkowania.</li>
                </ul>

                <h2>5. Czego unikać</h2>
                <ul>
                    <li>długiego kontaktu z deszczem, śniegiem i silnym słońcem,</li>
                    <li>suszenia na kaloryferze lub przy źródłach ciepła,</li>
                    <li>przypadkowych detergentów i rozpuszczalników.</li>
                </ul>

                <h2>Podsumowanie</h2>
                <p>Najlepsze efekty daje regularna, spokojna pielęgnacja: czyszczenie i ochrona. Dzięki temu torebka z ekoskóry dłużej zachowuje kształt, kolor i elegancki wygląd.</p>
            </div>',
        ),
    );
}

/**
 * Tworzy strony wariantów i synchronizuje ich treść z kodem przy każdej zmianie tablicy.
 */
function moretti_ensure_care_pages_exist() {
    $variants = moretti_care_variants();
    $fingerprint = md5(serialize($variants));
    if (get_option('moretti_care_pages_seeded') === $fingerprint) {
        return;
    }

    foreach ($variants as $variant) {
        if (empty($variant['page_content'])) {
            continue;
        }
        $existing = get_page_by_path($variant['page_slug'], OBJECT, 'page');
        if ($existing instanceof WP_Post) {
            // Treść wariantu jest zarządzana z kodu – synchronizuj przy zmianie.
            wp_update_post(array(
                'ID'           => $existing->ID,
                'post_title'   => $variant['page_title'],
                'post_content' => $variant['page_content'],
                'post_status'  => 'publish',
            ));
            continue;
        }
        wp_insert_post(array(
            'post_title'   => $variant['page_title'],
            'post_name'    => $variant['page_slug'],
            'post_content' => $variant['page_content'],
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_author'  => 1,
        ));
    }

    update_option('moretti_care_pages_seeded', $fingerprint);
}
add_action('admin_init', 'moretti_ensure_care_pages_exist');

/**
 * Zwraca array('url' => ..., 'label' => ...) albo null, gdy link ma się nie pokazywać.
 */
function moretti_get_product_care_link($product) {
    if (!$product instanceof WC_Product) {
        return null;
    }

    $variants = moretti_care_variants();
    $key = (string) $product->get_meta(MORETTI_CARE_META_KEY, true);

    if ($key === MORETTI_CARE_NONE) {
        return null;
    }
    if ($key === '' || !isset($variants[$key])) {
        $key = MORETTI_CARE_DEFAULT;
    }

    $variant = $variants[$key];
    $page = get_page_by_path($variant['page_slug'], OBJECT, 'page');
    $url = ($page instanceof WP_Post && $page->post_status === 'publish')
        ? get_permalink($page->ID)
        : home_url('/' . $variant['page_slug'] . '/');

    return array(
        'url'   => $url,
        'label' => $variant['link_label'],
    );
}

/**
 * Pole wyboru w edycji produktu (Dane produktu → Ogólne).
 */
function moretti_care_variant_product_field() {
    $options = array(
        '' => 'Domyślnie (' . moretti_care_variants()[MORETTI_CARE_DEFAULT]['name'] . ')',
    );
    foreach (moretti_care_variants() as $key => $variant) {
        $options[$key] = $variant['name'];
    }
    $options[MORETTI_CARE_NONE] = 'Nie pokazuj linku';

    echo '<div class="options_group">';
    woocommerce_wp_select(array(
        'id'          => MORETTI_CARE_META_KEY,
        'label'       => 'Link "Pielęgnacja"',
        'description' => 'Który poradnik pielęgnacji pokazać na karcie produktu.',
        'desc_tip'    => true,
        'options'     => $options,
    ));
    echo '</div>';
}
add_action('woocommerce_product_options_general_product_data', 'moretti_care_variant_product_field');

function moretti_care_variant_product_save($product) {
    $value = isset($_POST[MORETTI_CARE_META_KEY]) ? sanitize_key(wp_unslash($_POST[MORETTI_CARE_META_KEY])) : '';
    $allowed = array_merge(array_keys(moretti_care_variants()), array(MORETTI_CARE_NONE));
    if ($value === '' || !in_array($value, $allowed, true)) {
        $product->delete_meta_data(MORETTI_CARE_META_KEY);
    } else {
        $product->update_meta_data(MORETTI_CARE_META_KEY, $value);
    }
}
add_action('woocommerce_admin_process_product_object', 'moretti_care_variant_product_save');
